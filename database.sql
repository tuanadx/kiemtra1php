

--Tạo bảng sách
CREATE TABLE sach (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_sach VARCHAR(255) NOT NULL,
    gia_tien DECIMAL(10,2) NOT NULL,
    tac_gia VARCHAR(255) NOT NULL,
    anh VARCHAR(255) -- Lưu đường dẫn ảnh
);

-- Tạo bảng khách hàng
CREATE TABLE IF NOT EXISTS `khach_hang` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `ho_ten` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `mat_khau` VARCHAR(255) NOT NULL,
    `so_dien_thoai` VARCHAR(20),
    `dia_chi` TEXT,
    `ngay_sinh` DATE,
    `gioi_tinh` ENUM('Nam', 'Nữ', 'Khác'),
    `ngay_dang_ky` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `trang_thai` TINYINT(1) DEFAULT 1 COMMENT '1: Hoạt động, 0: Khóa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
-- Tạo bảng giỏ hàng
CREATE TABLE IF NOT EXISTS `gio_hang` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `id_khach_hang` INT NOT NULL,
    `tong_tien` DECIMAL(10,2) DEFAULT 0,
    `ngay_tao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `ngay_cap_nhat` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_khach_hang`) REFERENCES `khach_hang`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng chi tiết giỏ hàng
CREATE TABLE IF NOT EXISTS `chi_tiet_gio_hang` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `id_gio_hang` INT NOT NULL,
    `id_sach` INT NOT NULL,
    `so_luong` INT NOT NULL DEFAULT 1,
    `gia_tien` DECIMAL(10,2) NOT NULL,
    `thanh_tien` DECIMAL(10,2) GENERATED ALWAYS AS (`so_luong` * `gia_tien`) STORED,
    `ngay_them` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_gio_hang`) REFERENCES `gio_hang`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`id_sach`) REFERENCES `sach`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 