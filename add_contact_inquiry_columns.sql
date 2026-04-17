-- Run once on your UPHS_event database (phpMyAdmin or mysql CLI).
-- Enables admin unread badges and timestamps for contact inquiries.

ALTER TABLE `contact`
  ADD COLUMN `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `message`;

ALTER TABLE `contact`
  ADD COLUMN `is_read` tinyint(1) NOT NULL DEFAULT 0 AFTER `created_at`;

-- Treat existing rows as already handled so only new submissions notify.
UPDATE `contact` SET `is_read` = 1;
