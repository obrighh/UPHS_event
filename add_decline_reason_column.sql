-- Run once on existing databases that were created before decline_reason was added.
ALTER TABLE `events` ADD COLUMN `decline_reason` text DEFAULT NULL AFTER `events_date_posted`;
