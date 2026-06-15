-- Dynamic QR styling: store logo + corner/dot style options per QR.
-- Run once on the live database.
-- `logo` already exists (Cloudinary URL of the center logo).
-- `style_json` holds the visual style (dots type, corner styles, corner color, etc.) as JSON.

ALTER TABLE dynamic_qrs
    ADD COLUMN style_json TEXT NULL AFTER logo;
