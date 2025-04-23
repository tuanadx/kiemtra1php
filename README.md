# Nhã Nam Bookstore

Ứng dụng web bán sách xây dựng bằng PHP thuần với mô hình MVC.

## Cài đặt ứng dụng

1. Clone repository
```
git clone https://github.com/username/nhanam-bookstore.git
```

2. Cấu hình cơ sở dữ liệu
- Tạo database `cuahangsach`
- Nhập file `database.sql` để tạo các bảng và dữ liệu mẫu

3. Cấu hình kết nối
- Điều chỉnh thông tin kết nối trong `config/config.php`

## Deploy lên Railway

1. Đăng ký tài khoản tại [Railway](https://railway.app/)

2. Tạo MySQL database trên Railway

3. Kết nối repository GitHub với Railway

4. Cấu hình các biến môi trường:
   - `DATABASE_URL`: URL kết nối cơ sở dữ liệu (được cung cấp tự động khi thêm MySQL)
   - `DEBUG`: `false` cho môi trường production

5. Deploy!

## Cấu trúc ứng dụng

```
/app
  /controllers     # Các controller xử lý logic
  /core            # Core framework
  /models          # Các model tương tác với database
  /views           # Giao diện người dùng
/config            # Cấu hình ứng dụng
/public            # Tài nguyên public (CSS, JS, images)
``` 