<?php require_once APPROOT . '/views/includes/header.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
            <li class="active"><?php echo $data['book']->ten_sach; ?></li>
        </ul>
    </div>
</div>

<!-- Book Detail Section -->
<section class="book-detail">
    <div class="container">
        <div class="book-wrapper">
            <div class="book-image">
                <img src="<?php echo !empty($data['book']->anh) ? URL_ROOT . '/public/uploads/images/' . $data['book']->anh : URL_ROOT . '/public/images/default-book.jpg'; ?>" alt="<?php echo $data['book']->ten_sach; ?>">
            </div>
            <div class="book-info">
                <h1 class="book-title"><?php echo $data['book']->ten_sach; ?></h1>
                <p class="book-author">Tác giả: <span><?php echo $data['book']->tac_gia; ?></span></p>
                <!-- <p class="book-publisher">Nhà xuất bản: <span><?php echo $data['book']->nha_xuat_ban; ?></span></p>
                <p class="book-country">Quốc gia: <span><?php echo $data['book']->quoc_gia; ?></span></p>
                <p class="book-date">Ngày xuất bản: <span><?php echo date('d/m/Y', strtotime($data['book']->ngay_xuat_ban)); ?></span></p> -->
                <div class="book-price">
                    <span class="price"><?php echo formatCurrency($data['book']->gia_tien); ?></span>
                </div>
                <div class="book-actions">
                    <button class="add-to-cart" data-book-id="<?php echo $data['book']->id; ?>">Thêm vào giỏ hàng</button>
                    <button class="buy-now" data-book-id="<?php echo $data['book']->id; ?>">Mua ngay</button>
                </div>
            </div>
        </div>
        
        <!-- <div class="book-description">
            <h2>Mô tả sách</h2>
            <div class="description-content">
                <?php echo $data['book']->mo_ta; ?>
            </div>
        </div> -->
    </div>
</section>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 