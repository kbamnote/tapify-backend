-- Link designs to a content category so the app can show them in the
-- matching tab (e.g. "Important Dates" shows its associated designs).
ALTER TABLE designs
    ADD COLUMN content_category_id INT DEFAULT NULL
        COMMENT 'FK → categories.id; optional content-category association',
    ADD CONSTRAINT fk_designs_content_category
        FOREIGN KEY (content_category_id) REFERENCES categories(id)
        ON DELETE SET NULL;
