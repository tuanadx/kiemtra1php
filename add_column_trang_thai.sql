-- Add trang_thai column to gio_hang table if it doesn't exist
ALTER TABLE gio_hang ADD COLUMN IF NOT EXISTS trang_thai ENUM('active', 'temporary', 'completed') DEFAULT 'active'; 