-- Add read_at column to notification_logs (fix for notifications page error).
-- Run this once in phpMyAdmin or MySQL. If you see "Duplicate column" then the column already exists.
ALTER TABLE notification_logs ADD COLUMN read_at TIMESTAMP NULL DEFAULT NULL AFTER message;
