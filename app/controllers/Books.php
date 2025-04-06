<?php
class Books extends Controller {
    private $bookModel;

    public function __construct() {
        $this->bookModel = $this->model('Book');
    }

    // Hiển thị chi tiết sách
    public function detail($id) {
        $book = $this->bookModel->getBookById($id);

        if($book) {
            $data = [
                'title' => $book->ten_sach,
                'book' => $book
            ];

            $this->view('books/detail', $data);
        } else {
            redirect('home');
        }
    }

    // Tìm kiếm sách
    public function search() {
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            // Xử lý tìm kiếm
            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            
            if($page < 1) $page = 1;
            
            $itemsPerPage = 8;
            $offset = ($page - 1) * $itemsPerPage;
            
            // Lấy kết quả tìm kiếm
            $books = $this->bookModel->searchBooks($keyword, $itemsPerPage, $offset);
            
            // Đếm tổng số kết quả
            $totalSearchResults = $this->bookModel->getTotalSearchResults($keyword);
            $totalPages = ceil($totalSearchResults / $itemsPerPage);
            
            $data = [
                'title' => 'Kết quả tìm kiếm: ' . $keyword,
                'books' => $books,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'itemsPerPage' => $itemsPerPage,
                'keyword' => $keyword
            ];

            $this->view('home/index', $data);
        } else {
            redirect('home');
        }
    }
} 