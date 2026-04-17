-- Run once: optional inbox override for public contact form (admins only).
-- If NULL, the account `email` is used for that admin.

ALTER TABLE `accounts`
  ADD COLUMN `inquiry_notify_email` varchar(199) NULL DEFAULT NULL AFTER `email`;
