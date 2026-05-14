import type { Config, Context } from "@netlify/functions";
import { db } from "../../db/index.js";
import { items } from "../../db/schema.js";
import { eq } from "drizzle-orm";

const CATEGORIES = ["Produce", "Dairy", "Meat", "Condiments", "Beverages", "Dry Goods"];
const UNITS = ["kg", "liter", "pcs", "bottle", "pack"];
const LOW_STOCK_THRESHOLD = 10;

type Item = typeof items.$inferSelect;

function validate(body: Record<string, unknown>): { errors: Record<string, string>; isValid: boolean } {
  const errors: Record<string, string> = {};
  const name = String(body.name ?? "").trim();
  const category = String(body.category ?? "").trim();
  const stock_level = String(body.stock_level ?? "").trim();
  const unit = String(body.unit ?? "").trim();
  const price = String(body.price ?? "").trim();

  if (!name) {
    errors.name = "Product name is required.";
  } else if (!/^[a-zA-Z0-9 ]+$/.test(name)) {
    errors.name = "Product name must be alphanumeric.";
  }

  if (!category) {
    errors.category = "Category is required.";
  } else if (!CATEGORIES.includes(category)) {
    errors.category = "Select a valid category.";
  }

  if (!stock_level) {
    errors.stock_level = "Stock level is required.";
  } else if (!/^\d+$/.test(stock_level) || parseInt(stock_level, 10) < 0) {
    errors.stock_level = "Stock level must be a non-negative integer.";
  }

  if (!unit) {
    errors.unit = "Unit is required.";
  } else if (!UNITS.includes(unit)) {
    errors.unit = "Select a valid unit.";
  }

  if (!price) {
    errors.price = "Price is required.";
  } else if (!/^\d+(?:\.\d{1,2})?$/.test(price)) {
    errors.price = "Price must be a valid decimal value.";
  } else if (parseFloat(price) < 0) {
    errors.price = "Price cannot be negative.";
  }

  return { errors, isValid: Object.keys(errors).length === 0 };
}

function computeStats(allItems: Item[]) {
  let low_stock = 0;
  let inventory_value = 0;
  for (const item of allItems) {
    if (item.stock_level <= LOW_STOCK_THRESHOLD) low_stock++;
    inventory_value += item.stock_level * item.price;
  }
  return { total_items: allItems.length, low_stock, inventory_value };
}

function computeAnalytics(allItems: Item[]) {
  let total_stock_units = 0;
  let price_sum = 0;
  let high_value_items = 0;
  let top_item_name = "N/A";
  let top_item_value = 0;
  const category_buckets: Record<string, { item_count: number; stock_total: number; value_total: number }> = {};

  for (const item of allItems) {
    const value = item.stock_level * item.price;
    total_stock_units += item.stock_level;
    price_sum += item.price;
    if (value >= 5000) high_value_items++;
    if (value > top_item_value) {
      top_item_value = value;
      top_item_name = item.name;
    }
    if (!category_buckets[item.category]) {
      category_buckets[item.category] = { item_count: 0, stock_total: 0, value_total: 0 };
    }
    category_buckets[item.category].item_count++;
    category_buckets[item.category].stock_total += item.stock_level;
    category_buckets[item.category].value_total += value;
  }

  const item_count = allItems.length;
  const average_price = item_count > 0 ? price_sum / item_count : 0;

  const category_breakdown = Object.entries(category_buckets)
    .map(([category, data]) => ({ category, ...data }))
    .sort((a, b) => b.value_total - a.value_total);

  const top_category = category_breakdown.length > 0 ? category_breakdown[0].category : "N/A";

  return {
    total_stock_units,
    average_price,
    distinct_categories: Object.keys(category_buckets).length,
    high_value_items,
    top_category,
    top_item_name,
    top_item_value,
    category_breakdown,
  };
}

export default async (req: Request, context: Context) => {
  const { id } = context.params;
  const itemId = id ? parseInt(id, 10) : null;

  if (req.method === "GET" && !itemId) {
    const allItems = await db.select().from(items).orderBy(items.id);
    return Response.json({
      items: allItems,
      categories: CATEGORIES,
      units: UNITS,
      stats: computeStats(allItems),
      analytics: computeAnalytics(allItems),
    });
  }

  if (req.method === "GET" && itemId) {
    const [item] = await db.select().from(items).where(eq(items.id, itemId));
    if (!item) return new Response("Not found", { status: 404 });
    return Response.json({ item, categories: CATEGORIES, units: UNITS });
  }

  if (req.method === "POST" && !itemId) {
    const body = (await req.json()) as Record<string, unknown>;
    const { errors, isValid } = validate(body);
    if (!isValid) return Response.json({ errors }, { status: 422 });
    const [item] = await db
      .insert(items)
      .values({
        name: String(body.name).trim(),
        category: String(body.category).trim(),
        stock_level: parseInt(String(body.stock_level), 10),
        unit: String(body.unit).trim(),
        price: parseFloat(String(body.price)),
      })
      .returning();
    return Response.json({ item }, { status: 201 });
  }

  if (req.method === "PUT" && itemId) {
    const body = (await req.json()) as Record<string, unknown>;
    const { errors, isValid } = validate(body);
    if (!isValid) return Response.json({ errors }, { status: 422 });
    const [item] = await db
      .update(items)
      .set({
        name: String(body.name).trim(),
        category: String(body.category).trim(),
        stock_level: parseInt(String(body.stock_level), 10),
        unit: String(body.unit).trim(),
        price: parseFloat(String(body.price)),
      })
      .where(eq(items.id, itemId))
      .returning();
    if (!item) return new Response("Not found", { status: 404 });
    return Response.json({ item });
  }

  if (req.method === "DELETE" && itemId) {
    const result = await db.delete(items).where(eq(items.id, itemId)).returning();
    if (result.length === 0) return new Response("Not found", { status: 404 });
    return Response.json({ success: true });
  }

  return new Response("Method not allowed", { status: 405 });
};

export const config: Config = {
  path: ["/api/items", "/api/items/:id"],
};
