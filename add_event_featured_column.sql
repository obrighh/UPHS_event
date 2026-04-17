-- Run once: featured "main campus" event for homepage countdown (organization sets this).
ALTER TABLE `events` ADD COLUMN `is_featured` tinyint(1) NOT NULL DEFAULT 0 AFTER `decline_reason`;
