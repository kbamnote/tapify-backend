-- =====================================================
-- Migration: Add pan_no, address, owner_dob, date_of_incorp to businesses
-- =====================================================

ALTER TABLE businesses
  ADD COLUMN pan_no         VARCHAR(10)  DEFAULT NULL AFTER gstin,
  ADD COLUMN address        TEXT         DEFAULT NULL AFTER city,
  ADD COLUMN owner_dob      DATE         DEFAULT NULL AFTER address,
  ADD COLUMN date_of_incorp DATE         DEFAULT NULL AFTER owner_dob;
