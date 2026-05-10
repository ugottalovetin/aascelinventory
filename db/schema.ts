import { pgTable, serial, text, integer, real } from "drizzle-orm/pg-core";

export const items = pgTable("items", {
  id: serial().primaryKey(),
  name: text().notNull(),
  category: text().notNull(),
  stock_level: integer("stock_level").notNull(),
  unit: text().notNull(),
  price: real().notNull(),
});
