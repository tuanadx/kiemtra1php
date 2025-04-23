# Hướng dẫn Deploy lên Railway

## 1. Đăng ký tài khoản Railway

- Truy cập [Railway.app](https://railway.app/)
- Đăng ký tài khoản mới hoặc đăng nhập bằng GitHub

## 2. Chuẩn bị code

- Đưa dự án lên GitHub repository
- Đảm bảo repository có các file:
  - `composer.json`
  - `Procfile`
  - `.gitignore`

## 3. Tạo project mới trên Railway

1. Nhấn "New Project"
2. Chọn "Deploy from GitHub repo"
3. Kết nối với GitHub và chọn repository

## 4. Thêm MySQL database

1. Trong project, chọn "New" > "Database" > "Add MySQL"
2. Railway sẽ tự động tạo một database và thêm biến `DATABASE_URL` vào project

## 5. Nhập dữ liệu vào database

1. Vào tab "Data" của MySQL service
2. Chọn "Connect" > "MySQL CLI"
3. Nhập dữ liệu từ file `database.sql`
   ```sql
   -- Copy nội dung từ file database.sql vào đây
   ```

## 6. Cấu hình biến môi trường

1. Vào tab "Variables" trong project
2. Thêm các biến:
   - `DEBUG`: `false`
   - `RAILWAY_STATIC_URL`: URL của ứng dụng sau khi deploy (Điền sau khi deploy xong)

## 7. Deploy

1. Railway sẽ tự động deploy khi mọi thứ đã cài đặt
2. Chờ quá trình deploy hoàn tất và nhấn vào URL được cấp để truy cập website

## 8. Cập nhật URL

1. Sau khi deploy thành công, lấy URL được cấp (ví dụ: https://nhanam-bookstore.up.railway.app)
2. Vào tab "Variables", cập nhật giá trị `RAILWAY_STATIC_URL` bằng URL này
3. Railway sẽ tự động redeploy

## 9. Xử lý lỗi upload ảnh

Vì Railway không lưu trữ file tải lên vĩnh viễn, bạn nên:
1. Sử dụng dịch vụ như Amazon S3, Cloudinary để lưu trữ hình ảnh
2. Hoặc sửa code để lưu URL hình ảnh thay vì file vật lý

## 10. Theo dõi logs

- Vào tab "Deployments" để xem logs của ứng dụng
- Kiểm tra lỗi và sửa nếu cần 