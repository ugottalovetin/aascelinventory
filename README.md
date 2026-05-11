# aascelinventory

## System Documentation

### Introduction

`aascelinventory` is a simple Restaurant Inventory System designed to help a small restaurant keep track of ingredients and supplies. It is built so users can view what is in stock, add new items, update existing stock, and remove products when needed.

This document explains what the system does, how it works, and how it was implemented, written for readers who are not developers.

## What the system does

The system provides the following capabilities:

- View all inventory items in one dashboard.
- See important metrics such as total number of items, low-stock warnings, and inventory value.
- Add new products with category, quantity, unit type, and price.
- Edit existing products when stock changes or pricing is updated.
- Delete products that are no longer needed.

The app is meant to be an easy way for restaurant staff to manage stock without a complex software system.

## How it works

The application is a web-based system, which means it runs in a browser and stores its data on the same server where it is installed.

### User interaction

A restaurant worker uses the system through a web page:

- The dashboard shows the current inventory list and summary cards.
- The "Add Item" form allows creating new inventory entries.
- The "Edit" option opens the same form with existing item details.
- The "Delete" button removes an item from the inventory.

Whenever the user submits information, the system checks the data, saves it, and then returns to the dashboard with a confirmation message.

### Data storage

All inventory information is saved in a local database file. This means the system remembers the data even after the browser is closed.

- The database stores item name, category, stock quantity, unit type, and price.
- The system has a default low-stock threshold, so it can identify items that need restocking.

## Tools and technologies used

The system is implemented using the following tools:

- **PHP**: the programming language used to build the application logic.
- **SQLite**: a lightweight database that stores all inventory records in one file.
- **HTML and CSS**: used to create the pages and make them presentable.
- **JavaScript**: used for minor client-side behaviors and interactions.
- **Netlify**: the deployment platform used to publish the application online.

## Implementation overview

The system is organized into simple parts. Each part has a role that keeps the application easy to understand and maintain.

### 1. Interface layer

The system includes pages for showing information and collecting input from users.

- **Dashboard page**: shows the inventory list and summary information.
- **Inventory form page**: used for adding new items or editing existing items.
- **Not found page**: shown when a user navigates to an unknown page.

### 2. Application logic

The application decides what should happen when a user requests a page or submits a form.

- It reads the requested page name from the web address.
- It routes the request to the correct action: show dashboard, show form, save changes, or delete an item.
- It validates the input before saving to avoid incorrect or incomplete data.

### 3. Storage layer

The system stores inventory items in a local database file using SQLite.

- The database is created automatically on the first run.
- If the database is empty, the system adds a few example items so the user can see how it works immediately.

### 4. Feedback and validation

The system provides immediate feedback:

- If a user adds or updates an item successfully, the system shows a success message.
- If the input is incomplete or incorrect, it shows clear error messages.
- Form values are preserved when an error occurs, so the user does not need to type everything again.

## Deployment

This system was deployed using **Netlify**.

Netlify is a service that publishes web applications to the internet. In this project, Netlify hosts the files and makes the system accessible from a public web address.

> Important: Since this system uses PHP, the deployment must be set up to support PHP execution. If the deployment is successful on Netlify, it means the project was configured so server-side PHP code can run correctly.

## Benefits of this system

- Easy to use for restaurant staff.
- No complex installation required beyond a PHP-capable environment.
- Stores data locally in a single file, making it simple to move or back up.
- Provides useful stock information and alerts for low inventory.

## How to use it

1. Open the web application in a browser.
2. Review the dashboard to see current stock and quick summary metrics.
3. Use the "Add Item" button to enter new inventory.
4. Use the "Edit" button to update stock or pricing.
5. Use the "Delete" button to remove old items.

## System structure summary

The system contains the following main parts:

- A web page interface for users.
- A controller that directs actions based on user requests.
- A validation process that ensures data quality.
- A local database that stores inventory details.

## Summary

`aascelinventory` is a simple restaurant inventory tracking system built to make stock management straightforward and reliable. It is designed for ease-of-use, with direct inventory editing, low-stock awareness, and a clean dashboard view. Deployed with Netlify, it can be accessed online while keeping the inventory data stored safely on the server.
