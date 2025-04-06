<?php
class Home extends Controller {
    private $bookModel;

    public function __construct() {
        $this->bookModel = $this->model('Book');
    }

    // Phương thức mặc định
    public function index() {                                                                                                       
        // Lấy danh sách sách mới nhất
        $books = $this->bookModel->getBooks(8, 0);
        
        // Số trang
        $totalBooks = $this->bookModel->getTotalBooks();
        $itemsPerPage = 8;
        $totalPages = ceil($totalBooks / $itemsPerPage);
        
        $data = [
            'title' => 'Tất cả sản phẩm',
            'books' => $books,
            'currentPage' => 1,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage
        ];

        $this->view('home/index', $data);
    }

    // Phương thức phân trang
    public function page($page = 1) {
        $page = (int)$page;
        if($page < 1) $page = 1;
        
        $itemsPerPage = 8;
        $offset = ($page - 1) * $itemsPerPage;
        
        // Lấy danh sách sách theo trang
        $books = $this->bookModel->getBooks($itemsPerPage, $offset);
        
        // Số trang
        $totalBooks = $this->bookModel->getTotalBooks();
        $totalPages = ceil($totalBooks / $itemsPerPage);
        
        if($page > $totalPages && $totalPages > 0) {
            redirect('home/page/' . $totalPages);
        }
        
        $data = [
            'title' => 'Tất cả sản phẩm - Trang ' . $page,
            'books' => $books,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage
        ];

        $this->view('home/index', $data);
    }

    // Phương thức sắp xếp sách theo giá tăng dần
    public function sortPriceAsc($page = 1) {
        $page = (int)$page;
        if($page < 1) $page = 1;
        
        $itemsPerPage = 8;
        $offset = ($page - 1) * $itemsPerPage;
        
        // Lấy danh sách sách theo giá tăng dần
        $books = $this->bookModel->sortBooksByPriceAsc($itemsPerPage, $offset);
        
        // Số trang
        $totalBooks = $this->bookModel->getTotalBooks();
        $totalPages = ceil($totalBooks / $itemsPerPage);
        
        $data = [
            'title' => 'Sản phẩm theo giá tăng dần',
            'books' => $books,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'sortType' => 'price-asc'
        ];

        $this->view('home/index', $data);
    }

    // Phương thức sắp xếp sách theo giá giảm dần
    public function sortPriceDesc($page = 1) {
        $page = (int)$page;
        if($page < 1) $page = 1;
        
        $itemsPerPage = 8;
        $offset = ($page - 1) * $itemsPerPage;
        
        // Lấy danh sách sách theo giá giảm dần
        $books = $this->bookModel->sortBooksByPriceDesc($itemsPerPage, $offset);
        
        // Số trang
        $totalBooks = $this->bookModel->getTotalBooks();
        $totalPages = ceil($totalBooks / $itemsPerPage);
        
        $data = [
            'title' => 'Sản phẩm theo giá giảm dần',
            'books' => $books,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'sortType' => 'price-desc'
        ];

        $this->view('home/index', $data);
    }

    // Phương thức hiển thị sách mới nhất
    public function newest($page = 1) {
        $page = (int)$page;
        if($page < 1) $page = 1;
        
        $itemsPerPage = 8;
        $offset = ($page - 1) * $itemsPerPage;
        
        // Lấy danh sách sách mới nhất
        $books = $this->bookModel->getNewestBooks($itemsPerPage, $offset);
        
        // Số trang
        $totalBooks = $this->bookModel->getTotalBooks();
        $totalPages = ceil($totalBooks / $itemsPerPage);
        
        $data = [
            'title' => 'Sách mới',
            'books' => $books,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'sortType' => 'newest'
        ];

        $this->view('home/index', $data);
    }

    // Phương thức lọc sách theo quốc gia
    public function country($country, $page = 1) {
        $page = (int)$page;
        if($page < 1) $page = 1;
        
        $itemsPerPage = 8;
        $offset = ($page - 1) * $itemsPerPage;
        
        // Lấy danh sách sách theo quốc gia
        $books = $this->bookModel->filterBooksByCountry($country, $itemsPerPage, $offset);
        
        // Số trang
        $totalCountryBooks = $this->bookModel->getTotalBooksByCountry($country);
        $totalPages = ceil($totalCountryBooks / $itemsPerPage);
        
        $data = [
            'title' => 'Sách ' . $country,
            'books' => $books,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'country' => $country
        ];

        $this->view('home/index', $data);
    }

    // Phương thức tìm kiếm sách
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