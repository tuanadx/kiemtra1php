<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Đăng ký người dùng
    public function register($data) {
        $this->db->query('INSERT INTO khach_hang (ho_ten, email, mat_khau, so_dien_thoai) VALUES(:name, :email, :password, :phone)');
        // Bind values
        $this->db->bind(':name', $data['ho_ten']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['mat_khau']);
        $this->db->bind(':phone', $data['so_dien_thoai']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Kiểm tra login
    public function login($email, $password) {
        $this->db->query('SELECT * FROM khach_hang WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row) {
            $hashed_password = $row->mat_khau;
            if(password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        
        return false;
    }

    // Tìm người dùng bằng email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM khach_hang WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Kiểm tra hàng 
        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Lấy thông tin người dùng theo ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM khach_hang WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    // Cập nhật thông tin người dùng
    public function updateProfile($data) {
        $this->db->query('UPDATE khach_hang SET ho_ten = :name, so_dien_thoai = :phone, dia_chi = :address, ngay_sinh = :birthday, gioi_tinh = :gender WHERE id = :id');
        // Bind values
        $this->db->bind(':name', $data['ho_ten']);
        $this->db->bind(':phone', $data['so_dien_thoai']);
        $this->db->bind(':address', $data['dia_chi']);
        $this->db->bind(':birthday', $data['ngay_sinh']);
        $this->db->bind(':gender', $data['gioi_tinh']);
        $this->db->bind(':id', $data['id']);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Đổi mật khẩu
    public function changePassword($id, $newPassword) {
        $this->db->query('UPDATE khach_hang SET mat_khau = :password WHERE id = :id');
        // Bind values
        $this->db->bind(':password', $newPassword);
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
} 