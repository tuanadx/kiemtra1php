<?php require_once APPROOT . '/views/includes/header.php'; ?>
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
            <li class="active">Tất cả sản phẩm</li>
        </ul>
    </div>
</div>

<!-- Banner -->
<div class="banner">
    <div class="container">
        <img src="https://ext.same-assets.com/3715259319/2232221781.jpeg" alt="Banner">
    </div>
</div>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <div class="sidebar">
            <div class="filter-block">
                <h2>Quốc gia</h2>
                <div class="filter-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="country" value="Việt Nam">
                        <span class="checkmark"></span>
                        Việt Nam
                    </label>
                    <label class="checkbox-container">
                        <input type="checkbox" name="country" value="Trung Quốc">
                        <span class="checkmark"></span>
                        Trung Quốc
                    </label>
                    <label class="checkbox-container">
                        <input type="checkbox" name="country" value="Nhật Bản">
                        <span class="checkmark"></span>
                        Nhật Bản
                    </label>
                    <label class="checkbox-container">
                        <input type="checkbox" name="country" value="Pháp">
                        <span class="checkmark"></span>
                        Pháp
                    </label>
                    <label class="checkbox-container">
                        <input type="checkbox" name="country" value="Đức">
                        <span class="checkmark"></span>
                        Đức
                    </label>
                    <label class="checkbox-container">
                        <input type="checkbox" name="country" value="Hàn Quốc">
                        <span class="checkmark"></span>
                        Hàn Quốc
                    </label>
                    <label class="checkbox-container">
                        <input type="checkbox" name="country" value="Italy">
                        <span class="checkmark"></span>
                        Italy
                    </label>
                    <label class="checkbox-container">
                        <input type="checkbox" name="country" value="Mỹ">
                        <span class="checkmark"></span>
                        Mỹ
                    </label>
                </div>
            </div>
        </div>
        <div class="product-content">
            <div class="collection-header" id="product-section">
                <h1><?php echo $data['title']; ?></h1>
                <div class="collection-sorting">
                    <span>Sắp xếp theo</span>
                    <div class="sort-options">
                        <a href="<?php echo URL_ROOT; ?>/home" class="<?php echo !isset($data['sortType']) ? 'active' : ''; ?>">Mặc định</a>
                        <a href="<?php echo URL_ROOT; ?>/home/newest" class="<?php echo (isset($data['sortType']) && $data['sortType'] == 'newest') ? 'active' : ''; ?>">Sách mới</a>
                        <a href="<?php echo URL_ROOT; ?>/home/sortPriceAsc" class="<?php echo (isset($data['sortType']) && $data['sortType'] == 'price-asc') ? 'active' : ''; ?>">Giá thấp - cao</a>
                        <a href="<?php echo URL_ROOT; ?>/home/sortPriceDesc" class="<?php echo (isset($data['sortType']) && $data['sortType'] == 'price-desc') ? 'active' : ''; ?>">Giá cao - thấp</a>
                    </div>
                </div>
            </div>
            
            <div class="product-grid">
                <?php if (!empty($data['books'])) : ?>
                    <?php foreach ($data['books'] as $book) : ?>
                        <div class="product-item">
                            <div class="product-image">
                                <a href="<?php echo URL_ROOT; ?>/books/detail/<?php echo $book->id; ?>">
                                    <img src="<?php echo !empty($book->anh) ? URL_ROOT . '/public/uploads/images/' . $book->anh : URL_ROOT . '/public/images/default-book.jpg'; ?>" alt="<?php echo $book->ten_sach; ?>">
                                </a>
                            </div>
                            <div class="product-info">
                                <h3><a href="<?php echo URL_ROOT; ?>/books/detail/<?php echo $book->id; ?>"><?php echo $book->ten_sach; ?></a></h3>
                                <div class="product-price">
                                    <span class="contact"><?php echo formatCurrency($book->gia_tien); ?></span>
                                </div>
                            </div>
                            <div class="product-actions">
                                <button class="add-to-cart" data-book-id="<?php echo $book->id; ?>"><i class="fas fa-shopping-cart"></i></button>
                                <button class="buy-now" data-book-id="<?php echo $book->id; ?>">Mua ngay</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>Không có sản phẩm nào.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if($data['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php if($data['currentPage'] > 1): ?>
                        <a href="<?php echo URL_ROOT; ?>/home/page/<?php echo $data['currentPage'] - 1; ?>" class="prev"><i class="fas fa-chevron-left"></i></a>
                    <?php else: ?>
                        <a href="#" class="prev disabled"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>

                    <?php 
                    // Tính toán phạm vi trang hiển thị
                    $startPage = max(1, $data['currentPage'] - 2);
                    $endPage = min($data['totalPages'], $data['currentPage'] + 2);
                    
                    // Hiển thị các trang trong phạm vi
                    for($i = $startPage; $i <= $endPage; $i++): 
                    ?>
                        <a href="<?php echo URL_ROOT; ?>/home/page/<?php echo $i; ?>" class="<?php echo $i == $data['currentPage'] ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if($data['currentPage'] < $data['totalPages']): ?>
                        <a href="<?php echo URL_ROOT; ?>/home/page/<?php echo $data['currentPage'] + 1; ?>" class="next"><i class="fas fa-chevron-right"></i></a>
                    <?php else: ?>
                        <a href="#" class="next disabled"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 