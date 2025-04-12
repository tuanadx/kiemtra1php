--Tạo bảng sách
CREATE TABLE sach (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_sach VARCHAR(255) NOT NULL,
    gia_tien DECIMAL(10,2) NOT NULL,
    tac_gia VARCHAR(255) NOT NULL,
    so_luong INT NOT NULL DEFAULT 0,
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
    `trang_thai` ENUM('active', 'temporary', 'completed') DEFAULT 'active',
    `ghi_chu` TEXT,
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

-- Lệnh INSERT dữ liệu mẫu vào bảng sách
INSERT INTO sach (ten_sach, gia_tien, tac_gia, so_luong, anh) VALUES 
('Tôi Là Một Con Lừa', 85000.00, 'Nguyễn An', 30, 'toi-la-mot-con-lua.webp'),
('Tân Đèn Đầu Lạc Bìa', 78000.00, 'Trần Minh', 25, 'tandendaulacbia.webp'),
('Tất Xấu Người Việt', 95000.00, 'Phạm Quang', 40, 'tatxaunguoiviet01.webp'),
('Pickleball Cho Người Mới Bắt Đầu', 120000.00, 'Lê Thể Thao', 35, 'pickleball-cho-nguoi-moi-bat-dau-01.webp'),
('Sách Lật Tương Tác Song Ngữ', 150000.00, 'Hà Anh', 20, 'sachlattuongtacsongngu03tuoime-edf50dd9-76ed-4f2c-9110-09551e6ff964.webp'),
('Nơ Dây Rồi', 65000.00, 'Tú Anh', 45, 'nodayroi-1-6cm.webp'),
('Huơn Cao Có Gãy Ô Ô Ô', 75000.00, 'Hoàng Văn', 30, 'huon-cao-co-gay-o-o-o-01.webp'),
('Ngọn Đèn Đầu Lạc Bìa', 85000.00, 'Phương Linh', 35, 'ngondendaulacbia.webp'),
('Quái Đế Môn Hạc Ác Vua', 110000.00, 'Kim Long', 25, 'full-quademonhacacvua-1-6.webp'),
('Điều Kỳ Diệu Của Tiệm Tạp Hóa Namiya', 95000.00, 'Keigo Higashino', 40, 'dieukydieucuatiemtaphoanamiya0.webp'),
('Chuyện Kể Tình Yêu Và Bóng Tối', 88000.00, 'Minh Tâm', 30, 'chuyenketinhyeuvabongtoi01scal.webp'),
('Con Yêu Mẹ Vô Cùng', 70000.00, 'Thu Hương', 50, 'conyeumevocungtusachtinhcamgia.webp'),
('Chết Lần Hai', 92000.00, 'Phạm Đình', 35, 'chet-lan-hai-01.webp'),
('Cá Hồi - Hành Trình Tỉnh Thức', 105000.00, 'Nguyễn Đức', 40, 'cahoihanhtrinhtinhthuc01.webp'),
('Căn Nhà Bên Nhau', 86000.00, 'Hoàng Yến', 45, 'canhabennhautusachtinhcamgiadi.webp'),
('Bốn Ngồi Cùng Con Nhé', 78000.00, 'Lan Phương', 55, 'bongoicungconnhe01.webp'),
('Các Loại Cây Việt Nam', 135000.00, 'GS. Trần Đại', 30, 'cac-loai-cay-viet-nam-01.webp'),
('Bách Gia Chư Tử Các Môn Phái Triết Học', 145000.00, 'PGS. Lê Văn', 25, 'bachgiachutucacmonphaitriethoc.webp'),
('Anh Là Chú, Em Là Cô Dâu', 80000.00, 'Diệp Lạc', 40, 'anhlachureemlacodau04.webp'),
('Bạch Dạ Hành', 98000.00, 'Keigo Higashino', 35, 'bachdahanh.webp'),
('Ăn Dặm Không Nước Mắt', 125000.00, 'Mai Lan', 50, 'an-dam-khong-nuoc-mat.webp');

