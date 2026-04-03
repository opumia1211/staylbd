<?php
    header("Content-Type:text/css");
    $color = "#f0f";
    function checkhexcolor($color)
    {
        return preg_match('/^#[a-f0-9]{6}$/i', $color);
    }

    if (isset($_GET['color']) and $_GET['color'] != '') {
        $color = "#" . $_GET['color'];
    }

    if (!$color or !checkhexcolor($color)) {
        $color = "#336699";
    }

    function checkhexcolor2($secondColor)
    {
        return preg_match('/^#[a-f0-9]{6}$/i', $secondColor);
    }
?>

.bg--base,.banner__wrapper-category-inner-header,.banner__wrapper-products-inner-header,.section__header .title,.btn--base, .table th,.custom--card .card-header, .btn--base:active, btn--base:focus {
    background-color: <?php echo $color ?> !important;
}
.cmn--btn,.footer__widget ul li a::before,.filter__widget-title,.filter-price-widget .ui-slider-range,.side__menu li a.active,.cmn--table thead,.filter--bar,.owl-dots .owl-dot.active span,.ticket__wrapper-title::after,.menu-area .menu li:hover>a,.scrollToTop, .cart--btn .qty, .video-icon,.post__date {
    background: <?php echo $color ?> !important;
}
.text--base,.change-language span,.filter-price-widget .price-range input,.top__bar-left li.active,.dashboard-sidebar-toggler,.contact-info__icon,.nav--tabs li a.active,.change-language a:not(:last-child)::after, .cart--btn,.policy-page a:hover,.loader-bg h3{
    color: <?php echo $color ?> !important;
}
.search--group .form-control,.cmn--btn:not(button):hover, .loader::after {
    border-color:<?php echo $color ?> !important;
}
.section__header{
    border-bottom:1px solid <?php echo $color ?> !important;
}
h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,.side__menu li a:hover i,.side__menu li a.active i,.form--control-4::placeholder,.feature-card__icon{
    color:<?php echo $color ?> !important;
}

.product__item .product-right-btn a:hover,.product__item .product-right-btn a.active, .cart-plus-minus .qtybutton.active,.page-item.active .page-link, .feature-card:hover{
    background: <?php echo $color ?> !important;
    border-color:<?php echo $color ?> !important;
}
.filter-price-widget .ui-widget.ui-widget-content::after,.dashboard__responsive__header{
    background: <?php echo $color ?>33 !important;
}
.form--check .form-check-input:checked~.form-check-label::before{
    background: <?php echo $color ?> !important;
}
.form--control:focus{
    border:1px solid <?php echo $color ?>80 !important;
}
.side__menu li a:hover{
    background: <?php echo $color ?>1a !important;
    color: <?php echo $color ?> !important;
}
.reply-item{
    border:1px solid <?php echo $color ?>33 !important;
}
.contact-info{
    background:<?php echo $color ?>26 !important;
}
.contact-info__icon{
    border:2px solid <?php echo $color ?>33 !important;
}
.form--control-4{
    background: <?php echo $color ?>26 !important;
    color: <?php echo $color ?> !important;
}
.tos-links a{
    background: <?php echo $color ?>1f !important;
}
.cmn--card,.order-summary{
    box-shadow: 0 3px 25px <?php echo $color ?>1f !important; ;
}
.review-item{
    border-bottom:1px solid <?php echo $color ?>33 !important;
}
.pagination .page-item.disabled span{
    background:<?php echo $color ?>4d !important;
}
.pagination .page-item a, .pagination .page-item span{
    background: <?php echo $color ?>33 !important;
    border: 1px solid <?php echo $color ?>33 !important;
}
.pagination .page-item a.active, .pagination .page-item a:hover, .pagination .page-item span.active, .pagination .page-item span:hover{
    background: <?php echo $color ?> !important;
}
.input-group:focus-within .input-group-text{
    border-color: <?php echo $color ?>80 !important;
    color: #fff !important;
    background-color: <?php echo $color ?> !important;
}
@media (max-width: 991px){
    .category-link-area .category-link li.open.cate-icon>a,.category-link-area .category-link li:hover>a{
        background: <?php echo $color ?> !important;
    }
}
.loader:after {
    border-color: <?php echo $color ?> transparent <?php echo $color ?> transparent !important;
}
.banner__wrapper-products-inner-body{
    background-color: <?php echo $color ?>;
}