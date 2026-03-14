<?php

declare(strict_types=1);

namespace Core;

class App
{
    private Router $router;

    private Session $session;

    private DataStore $dataStore;

    private Validator $validator;

    private View $view;

    public function __construct(private string $basePath)
    {
        $this->session = new Session();
        $this->dataStore = new DataStore($this->session);
        $this->validator = new Validator();
        $this->view = new View($this->basePath . '/views');
        $this->router = new Router();
        $this->registerRoutes();
    }

    public function run(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $page = $_GET['page'] ?? 'dashboard';

        if (!is_string($page) || $page === '') {
            $page = 'dashboard';
        }

        $this->router->dispatch($method, $page);
    }

    private function registerRoutes(): void
    {
        $this->router->get('dashboard', function (): void {
            $this->showDashboard();
        });

        $this->router->get('inventory-form', function (): void {
            $this->showForm();
        });

        $this->router->get('edit', function (): void {
            $idValue = $_GET['id'] ?? '';
            $itemId = filter_var(
                $idValue,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if ($itemId === false) {
                $this->session->setFlash('error', 'Invalid item selected for editing.');
                $this->redirect('index.php?page=dashboard');
            }

            $this->showForm((int) $itemId);
        });

        $this->router->post('save', function (): void {
            $this->saveInventoryItem();
        });

        $this->router->post('delete', function (): void {
            $this->deleteInventoryItem();
        });

        $this->router->setNotFound(function (): void {
            $this->renderNotFound();
        });
    }

    private function showDashboard(): void
    {
        $this->view->render('dashboard', [
            'title' => 'Restaurant Inventory Dashboard',
            'activePage' => 'dashboard',
            'items' => $this->dataStore->all(),
            'stats' => $this->dataStore->getStats(),
            'analytics' => $this->dataStore->getAnalytics(),
            'successMessage' => $this->session->getFlash('success'),
            'errorMessage' => $this->session->getFlash('error'),
        ]);
    }

    private function showForm(?int $itemId = null): void
    {
        $categories = $this->dataStore->getCategories();
        $units = $this->dataStore->getUnits();

        $baseFormData = [
            'name' => '',
            'category' => $categories[0] ?? '',
            'stock_level' => '0',
            'unit' => $units[0] ?? '',
            'price' => '0.00',
        ];

        $isEdit = $itemId !== null;

        if ($isEdit) {
            $existingItem = $this->dataStore->find($itemId);

            if ($existingItem === null) {
                $this->session->setFlash('error', 'The requested inventory item was not found.');
                $this->redirect('index.php?page=dashboard');
            }

            $baseFormData = [
                'name' => (string) $existingItem['name'],
                'category' => (string) $existingItem['category'],
                'stock_level' => (string) $existingItem['stock_level'],
                'unit' => (string) $existingItem['unit'],
                'price' => number_format((float) $existingItem['price'], 2, '.', ''),
            ];
        }

        $flashedInput = $this->session->getFlash('form_input', []);

        if (is_array($flashedInput) && $flashedInput !== []) {
            $baseFormData = array_merge($baseFormData, $flashedInput);
        }

        $flashedErrors = $this->session->getFlash('form_errors', []);

        $this->view->render('inventory-form', [
            'title' => $isEdit ? 'Edit Inventory Item' : 'Add Inventory Item',
            'activePage' => 'form',
            'isEdit' => $isEdit,
            'itemId' => $itemId,
            'categories' => $categories,
            'units' => $units,
            'formData' => $baseFormData,
            'errors' => is_array($flashedErrors) ? $flashedErrors : [],
        ]);
    }

    private function saveInventoryItem(): void
    {
        $payload = [
            'name' => $_POST['name'] ?? '',
            'category' => $_POST['category'] ?? '',
            'stock_level' => $_POST['stock_level'] ?? '',
            'unit' => $_POST['unit'] ?? '',
            'price' => $_POST['price'] ?? '',
        ];

        $itemId = null;
        $postedId = $_POST['id'] ?? null;

        if ($postedId !== null && is_scalar($postedId) && (string) $postedId !== '') {
            $validatedId = filter_var(
                (string) $postedId,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if ($validatedId !== false) {
                $itemId = (int) $validatedId;
            }
        }

        if ($itemId !== null && $this->dataStore->find($itemId) === null) {
            $this->session->setFlash('error', 'Unable to update. Inventory item does not exist.');
            $this->redirect('index.php?page=dashboard');
        }

        $validationResult = $this->validator->validateInventory(
            $payload,
            $this->dataStore->getCategories(),
            $this->dataStore->getUnits()
        );

        if ($validationResult['isValid'] !== true) {
            $this->session->setFlash('form_errors', $validationResult['errors']);
            $this->session->setFlash('form_input', $validationResult['input']);

            $redirectTarget = $itemId !== null
                ? 'index.php?page=edit&id=' . $itemId
                : 'index.php?page=inventory-form';

            $this->redirect($redirectTarget);
        }

        $this->dataStore->save($validationResult['data'], $itemId);

        $this->session->setFlash(
            'success',
            $itemId !== null
                ? 'Inventory item updated successfully.'
                : 'Inventory item added successfully.'
        );

        $this->redirect('index.php?page=dashboard');
    }

    private function deleteInventoryItem(): void
    {
        $idValue = $_POST['id'] ?? '';
        $itemId = filter_var(
            $idValue,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($itemId === false) {
            $this->session->setFlash('error', 'Invalid item selected for removal.');
            $this->redirect('index.php?page=dashboard');
        }

        $isDeleted = $this->dataStore->delete((int) $itemId);

        if (!$isDeleted) {
            $this->session->setFlash('error', 'Unable to remove item. It may not exist anymore.');
            $this->redirect('index.php?page=dashboard');
        }

        $this->session->setFlash('success', 'Inventory item removed successfully.');
        $this->redirect('index.php?page=dashboard');
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view->render('404', [
            'title' => 'Page Not Found',
            'activePage' => 'unknown',
        ]);
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
