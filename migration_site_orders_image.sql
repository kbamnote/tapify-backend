-- Store the product image with each order so the customer's "My Orders" page can
-- show it (Amazon/Flipkart style). Additive; safe to run once.
ALTER TABLE site_orders ADD COLUMN item_image VARCHAR(600) NOT NULL DEFAULT '' AFTER item_slug;
