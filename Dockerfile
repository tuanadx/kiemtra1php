FROM php:8.1-cli

# Cài đặt các extension PHP cần thiết
RUN docker-php-ext-install pdo pdo_mysql

# Thiết lập thư mục làm việc
WORKDIR /app

# Sao chép mã nguồn vào container
COPY . .

# Thiết lập quyền thực thi cho script
RUN chmod +x start.sh

# Mở cổng
EXPOSE 8080

# Chạy script khởi động
CMD ["/app/start.sh"] 