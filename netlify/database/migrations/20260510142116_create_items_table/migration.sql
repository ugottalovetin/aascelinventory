CREATE TABLE "items" (
	"id" serial PRIMARY KEY,
	"name" text NOT NULL,
	"category" text NOT NULL,
	"stock_level" integer NOT NULL,
	"unit" text NOT NULL,
	"price" real NOT NULL
);
