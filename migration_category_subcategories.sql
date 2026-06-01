-- Add optional parent_id to categories to support sub-categories.
-- Top-level categories have parent_id = NULL.
-- Sub-categories have parent_id = <parent category id>.
ALTER TABLE categories
    ADD COLUMN parent_id INT DEFAULT NULL,
    ADD CONSTRAINT fk_categories_parent
        FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE;
