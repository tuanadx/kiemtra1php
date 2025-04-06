<?php
class Book {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Lấy tất cả sách
    public function getBooks($limit = null, $offset = 0) {
        if($limit) {
            $this->db->query("SELECT * FROM sach ORDER BY id DESC LIMIT :limit OFFSET :offset");
            $this->db->bind(':limit', $limit);
            $this->db->bind(':offset', $offset);
        } else {
            $this->db->query("SELECT * FROM sach ORDER BY id DESC");
        }

        return $this->db->resultSet();
    }

    // Đếm tổng số sách
    public function getTotalBooks() {
        $this->db->query("SELECT COUNT(*) as total FROM sach");
        $row = $this->db->single();
        return $row->total;
    }

    // Đếm tổng số sách theo quốc gia
    public function getTotalBooksByCountry($country) {
        $this->db->query("SELECT COUNT(*) as total FROM sach WHERE quoc_gia = :country");
        $this->db->bind(':country', $country);
        $row = $this->db->single();
        return $row->total;
    }

    // Đếm tổng số kết quả tìm kiếm
    public function getTotalSearchResults($keyword) {
        $searchTerm = '%' . $keyword . '%';
        $this->db->query("SELECT COUNT(*) as total FROM sach WHERE ten_sach LIKE :keyword OR tac_gia LIKE :keyword");
        $this->db->bind(':keyword', $searchTerm);
        $row = $this->db->single();
        return $row->total;
    }

    // Lấy chi tiết sách theo ID
    public function getBookById($id) {
        $this->db->query("SELECT * FROM sach WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Tìm kiếm sách
    public function searchBooks($keyword, $limit = null, $offset = 0) {
        $searchTerm = '%' . $keyword . '%';
        
        if($limit) {
            $this->db->query("SELECT * FROM sach WHERE ten_sach LIKE :keyword OR tac_gia LIKE :keyword ORDER BY id DESC LIMIT :limit OFFSET :offset");
            $this->db->bind(':keyword', $searchTerm);
            $this->db->bind(':limit', $limit);
            $this->db->bind(':offset', $offset);
        } else {
            $this->db->query("SELECT * FROM sach WHERE ten_sach LIKE :keyword OR tac_gia LIKE :keyword ORDER BY id DESC");
            $this->db->bind(':keyword', $searchTerm);
        }

        return $this->db->resultSet();
    }

    // Lọc sách theo quốc gia
    public function filterBooksByCountry($country, $limit = null, $offset = 0) {
        if($limit) {
            $this->db->query("SELECT * FROM sach WHERE quoc_gia = :country ORDER BY id DESC LIMIT :limit OFFSET :offset");
            $this->db->bind(':country', $country);
            $this->db->bind(':limit', $limit);
            $this->db->bind(':offset', $offset);
        } else {
            $this->db->query("SELECT * FROM sach WHERE quoc_gia = :country ORDER BY id DESC");
            $this->db->bind(':country', $country);
        }

        return $this->db->resultSet();
    }

    // Sắp xếp sách theo giá tăng dần
    public function sortBooksByPriceAsc($limit = null, $offset = 0) {
        if($limit) {
            $this->db->query("SELECT * FROM sach ORDER BY gia_tien ASC LIMIT :limit OFFSET :offset");
            $this->db->bind(':limit', $limit);
            $this->db->bind(':offset', $offset);
        } else {
            $this->db->query("SELECT * FROM sach ORDER BY gia_tien ASC");
        }

        return $this->db->resultSet();
    }

    // Sắp xếp sách theo giá giảm dần
    public function sortBooksByPriceDesc($limit = null, $offset = 0) {
        if($limit) {
            $this->db->query("SELECT * FROM sach ORDER BY gia_tien DESC LIMIT :limit OFFSET :offset");
            $this->db->bind(':limit', $limit);
            $this->db->bind(':offset', $offset);
        } else {
            $this->db->query("SELECT * FROM sach ORDER BY gia_tien DESC");
        }

        return $this->db->resultSet();
    }

    // Sắp xếp sách mới nhất
    public function getNewestBooks($limit = null, $offset = 0) {
        if($limit) {
            $this->db->query("SELECT * FROM sach ORDER BY ngay_xuat_ban DESC LIMIT :limit OFFSET :offset");
            $this->db->bind(':limit', $limit);
            $this->db->bind(':offset', $offset);
        } else {
            $this->db->query("SELECT * FROM sach ORDER BY ngay_xuat_ban DESC");
        }

        return $this->db->resultSet();
    }
} 