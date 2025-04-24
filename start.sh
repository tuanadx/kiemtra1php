#!/bin/bash

# Đảm bảo biến môi trường PORT được đặt, sử dụng 8080 là giá trị mặc định
PORT="${PORT:-8080}"

# Khởi động máy chủ PHP với cổng từ biến môi trường
php -S 0.0.0.0:$PORT 