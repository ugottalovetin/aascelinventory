INSERT INTO "items" ("name", "category", "stock_level", "unit", "price")
SELECT 'Tomato Sauce', 'Condiments', 24, 'bottle', 4.75
WHERE NOT EXISTS (SELECT 1 FROM "items" LIMIT 1);

INSERT INTO "items" ("name", "category", "stock_level", "unit", "price")
SELECT 'Spaghetti', 'Dry Goods', 58, 'pack', 2.45
WHERE NOT EXISTS (SELECT 1 FROM "items" WHERE name = 'Spaghetti');

INSERT INTO "items" ("name", "category", "stock_level", "unit", "price")
SELECT 'Mozzarella', 'Dairy', 8, 'kg', 9.90
WHERE NOT EXISTS (SELECT 1 FROM "items" WHERE name = 'Mozzarella');
