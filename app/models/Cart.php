<?php
class Cart {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Tạo giỏ hàng mới
    public function createCart($userId) {
        $this->db->query('INSERT INTO gio_hang (id_khach_hang) VALUES(:user_id)');
        $this->db->bind(':user_id', $userId);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Lấy giỏ hàng của người dùng
    public function getCartByUserId($userId) {
        $this->db->query('SELECT * FROM gio_hang WHERE id_khach_hang = :user_id');
        $this->db->bind(':user_id', $userId);
        
        $cart = $this->db->single();
        
        if($cart) {
            return $cart;
        } else {
            // Tạo giỏ hàng mới nếu chưa tồn tại
            $cartId = $this->createCart($userId);
            if($cartId) {
                $this->db->query('SELECT * FROM gio_hang WHERE id = :id');
                $this->db->bind(':id', $cartId);
                return $this->db->single();
            }
            return false;
        }
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addItem($cartId, $bookId, $quantity, $price) {
        // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
        $this->db->query('SELECT * FROM chi_tiet_gio_hang WHERE id_gio_hang = :cart_id AND id_sach = :book_id');
        $this->db->bind(':cart_id', $cartId);
        $this->db->bind(':book_id', $bookId);
        
        $item = $this->db->single();
        
        if($item) {
            // Nếu đã tồn tại, cập nhật số lượng
            $newQuantity = $item->so_luong + $quantity;
            $this->db->query('UPDATE chi_tiet_gio_hang SET so_luong = :quantity WHERE id = :id');
            $this->db->bind(':quantity', $newQuantity);
            $this->db->bind(':id', $item->id);
            
            if($this->db->execute()) {
                $this->updateCartTotal($cartId);
                return true;
            } else {
                return false;
            }
        } else {
            // Nếu chưa tồn tại, thêm mới
            $this->db->query('INSERT INTO chi_tiet_gio_hang (id_gio_hang, id_sach, so_luong, gia_tien) VALUES(:cart_id, :book_id, :quantity, :price)');
            $this->db->bind(':cart_id', $cartId);
            $this->db->bind(':book_id', $bookId);
            $this->db->bind(':quantity', $quantity);
            $this->db->bind(':price', $price);
            
            if($this->db->execute()) {
                $this->updateCartTotal($cartId);
                return true;
            } else {
                return false;
            }
        }
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateQuantity($cartDetailId, $quantity) {
        // Lấy thông tin chi tiết giỏ hàng
        $this->db->query('SELECT * FROM chi_tiet_gio_hang WHERE id = :id');
        $this->db->bind(':id', $cartDetailId);
        $cartDetail = $this->db->single();
        
        if(!$cartDetail) {
            return false;
        }
        
        // Nếu số lượng <= 0, xóa sản phẩm khỏi giỏ hàng
        if($quantity <= 0) {
            return $this->removeItem($cartDetailId);
        }
        
        // Cập nhật số lượng
        $this->db->query('UPDATE chi_tiet_gio_hang SET so_luong = :quantity WHERE id = :id');
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':id', $cartDetailId);
        
        if($this->db->execute()) {
            $this->updateCartTotal($cartDetail->id_gio_hang);
            return true;
        } else {
            return false;
        }
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeItem($cartDetailId) {
        // Lấy thông tin chi tiết giỏ hàng
        $this->db->query('SELECT * FROM chi_tiet_gio_hang WHERE id = :id');
        $this->db->bind(':id', $cartDetailId);
        $cartDetail = $this->db->single();
        
        if(!$cartDetail) {
            return false;
        }
        
        $cartId = $cartDetail->id_gio_hang;
        
        // Xóa sản phẩm
        $this->db->query('DELETE FROM chi_tiet_gio_hang WHERE id = :id');
        $this->db->bind(':id', $cartDetailId);
        
        if($this->db->execute()) {
            $this->updateCartTotal($cartId);
            return true;
        } else {
            return false;
        }
    }

    // Lấy tất cả sản phẩm trong giỏ hàng
    public function getCartItems($cartId) {
        $this->db->query('
            SELECT c.*, s.ten_sach, s.anh 
            FROM chi_tiet_gio_hang c
            JOIN sach s ON c.id_sach = s.id
            WHERE c.id_gio_hang = :cart_id
        ');
        $this->db->bind(':cart_id', $cartId);
        
        return $this->db->resultSet();
    }

    // Cập nhật tổng tiền trong giỏ hàng
    public function updateCartTotal($cartId) {
        $this->db->query('
            SELECT SUM(thanh_tien) as total 
            FROM chi_tiet_gio_hang 
            WHERE id_gio_hang = :cart_id
        ');
        $this->db->bind(':cart_id', $cartId);
        
        $result = $this->db->single();
        $total = $result ? $result->total : 0;
        
        $this->db->query('UPDATE gio_hang SET tong_tien = :total WHERE id = :id');
        $this->db->bind(':total', $total);
        $this->db->bind(':id', $cartId);
        
        return $this->db->execute();
    }

    // Đếm số sản phẩm trong giỏ hàng
    public function countItems($cartId) {
        $this->db->query('SELECT COUNT(*) as count FROM chi_tiet_gio_hang WHERE id_gio_hang = :cart_id');
        $this->db->bind(':cart_id', $cartId);
        
        $result = $this->db->single();
        return $result ? $result->count : 0;
    }

    // Xóa tất cả sản phẩm trong giỏ hàng
    public function clearCart($cartId) {
        $this->db->query('DELETE FROM chi_tiet_gio_hang WHERE id_gio_hang = :cart_id');
        $this->db->bind(':cart_id', $cartId);
        
        if($this->db->execute()) {
            $this->updateCartTotal($cartId);
            return true;
        } else {
            return false;
        }
    }
} 