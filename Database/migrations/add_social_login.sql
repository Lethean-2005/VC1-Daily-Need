-- Run this once against dailyneed_db to enable Google login.
-- Example: mysql -u root dailyneed_db < Database/migrations/add_social_login.sql

ALTER TABLE `users`
    MODIFY `password` varchar(255) NULL,
    ADD COLUMN `google_id` varchar(255) NULL UNIQUE AFTER `role`;
