-- Migration: add image column to vcard_services
-- Lets each service carry an image (shown on the card / click-to-open modal).
-- Safe to run once on the production database.

ALTER TABLE `vcard_services`
  ADD COLUMN `image` VARCHAR(255) DEFAULT NULL AFTER `icon`;
