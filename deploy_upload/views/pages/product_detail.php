<?php
$avgRating = $reviewStats ? round((float) $reviewStats['avg_rating'], 1) : 0;
$reviewCount = $reviewStats ? (int) $reviewStats['review_count'] : 0;
?>
<style>
.dn-pd { background: #f4f3ef; padding: 40px 0 70px; font-family: 'Nunito', 'Kantumruy Pro', sans-serif; }
.dn-pd .container { max-width: 1200px; }

.dn-pd-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,.05);
}

/* Left: image */
.dn-pd-image-card {
    padding: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 380px;
}
.dn-pd-image-card img { max-width: 100%; max-height: 340px; object-fit: contain; }

/* Middle: info */
.dn-pd-info { padding: 6px 8px; }
.dn-pd-eyebrow {
    display: inline-block;
    background: rgba(15,85,83,.08);
    color: #0F5553;
    font-weight: 700;
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 5px 12px;
    border-radius: 5px;
    margin-bottom: 10px;
}
.dn-pd-info h1 { font-size: 1.8rem; font-weight: 800; color: #14110d; margin-bottom: 6px; }
.dn-pd-desc { color: #777; font-size: .92rem; line-height: 1.6; margin-bottom: 18px; }
.dn-pd-price-label { color: #9a9a92; font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px; }
.dn-pd-price { font-size: 2.1rem; font-weight: 800; color: #14110d; margin-bottom: 22px; }
.dn-pd-price sup { font-size: 1.1rem; top: -.9em; }

.dn-pd-actions { display: flex; align-items: center; gap: 12px; }
.dn-pd-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #14110d;
    color: #fff;
    font-weight: 700;
    font-size: .85rem;
    padding: 13px 26px;
    border-radius: 5px;
    border: none;
    text-decoration: none;
    transition: background .2s ease-in-out;
}
.dn-pd-add-btn:hover { background: #0F5553; color: #fff; }
.dn-pd-fav-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 5px;
    border: 1px solid #e5e5e0;
    background: #fff;
    color: #9a9a92;
    font-size: 1.15rem;
    cursor: pointer;
}
.dn-pd-fav-btn.ti-heart.active { color: #e0455a; }

/* Related strip */
.dn-pd-related { margin-top: 30px; }
.dn-pd-related-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.dn-pd-related-head h6 { font-weight: 800; color: #14110d; margin: 0; font-size: .85rem; text-transform: uppercase; letter-spacing: .04em; }
.dn-pd-related-track { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 4px; }
.dn-pd-related-item {
    flex: 0 0 130px;
    background: #fff;
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color .15s ease-in-out;
}
.dn-pd-related-item:hover { border-color: #0F5553; }
.dn-pd-related-item img { width: 100%; height: 80px; object-fit: contain; margin-bottom: 8px; }
.dn-pd-related-item .n { font-weight: 700; font-size: .8rem; color: #14110d; display: block; }
.dn-pd-related-item .p { font-size: .74rem; color: #9a9a92; }

/* Right: reviews */
.dn-pd-side { padding: 24px; }
.dn-pd-side-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.dn-pd-side-head span { color: #9a9a92; font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; }
.dn-pd-rating-badge { font-size: 2rem; font-weight: 800; color: #14110d; margin-bottom: 4px; }
.dn-pd-stars i { color: #e5e5e0; font-size: 1rem; }
.dn-pd-stars i.filled { color: #f5a623; }
.dn-pd-review-count { color: #9a9a92; font-size: .78rem; margin-left: 6px; }

.dn-pd-review { border-top: 1px solid #f1f1ee; padding: 16px 0; }
.dn-pd-review:first-of-type { border-top: none; padding-top: 20px; }
.dn-pd-review-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.dn-pd-review-top strong { color: #14110d; font-size: .88rem; }
.dn-pd-review-stars i { color: #e5e5e0; font-size: .78rem; }
.dn-pd-review-stars i.filled { color: #f5a623; }
.dn-pd-review p { color: #777; font-size: .85rem; margin: 0; line-height: 1.55; }
.dn-pd-empty { color: #9a9a92; font-size: .88rem; text-align: center; padding: 30px 10px; }
</style>

<div class="dn-pd">
    <div class="container">
        <?php if (!$product): ?>
            <div class="dn-pd-card dn-pd-empty">No products available yet.</div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <div class="dn-pd-card dn-pd-image-card">
                                <img src="<?= htmlspecialchars($product['imageURL']) ?>" alt="<?= htmlspecialchars($product['productname']) ?>">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="dn-pd-info">
                                <span class="dn-pd-eyebrow"><?= htmlspecialchars($product['categories']) ?></span>
                                <h1><?= htmlspecialchars($product['productname']) ?></h1>
                                <p class="dn-pd-desc"><?= htmlspecialchars($product['descriptions']) ?></p>
                                <div class="dn-pd-price-label">Total price</div>
                                <div class="dn-pd-price">$<?= number_format($product['price'], 2) ?></div>
                                <div class="dn-pd-actions">
                                    <button class="dn-pd-add-btn" onclick="addToCart(<?= (int) $product['id'] ?>)">
                                        <i class="ti ti-shopping-cart"></i> Add to shopping cart
                                    </button>
                                    <i class="ti ti-heart dn-pd-fav-btn" data-heart-id="<?= (int) $product['id'] ?>" onclick="toggleFavorite(<?= (int) $product['id'] ?>)"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($related)): ?>
                        <div class="dn-pd-related">
                            <div class="dn-pd-related-head"><h6>More Like This</h6></div>
                            <div class="dn-pd-related-track">
                                <?php foreach ($related as $r): ?>
                                    <div class="dn-pd-related-item" onclick="window.location.href='/product_detail?productId=<?= (int) $r['id'] ?>'">
                                        <img src="<?= htmlspecialchars($r['imageURL']) ?>" alt="<?= htmlspecialchars($r['productname']) ?>">
                                        <span class="n"><?= htmlspecialchars($r['productname']) ?></span>
                                        <span class="p">$<?= number_format($r['price'], 2) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="dn-pd-card dn-pd-side">
                        <div class="dn-pd-side-head">
                            <span>Reviews</span>
                            <span>Overview</span>
                        </div>
                        <div class="dn-pd-rating-badge"><?= $avgRating ?></div>
                        <div class="dn-pd-stars mb-3">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="ti ti-star<?= $i <= round($avgRating) ? ' filled' : '' ?>"></i>
                            <?php endfor; ?>
                            <span class="dn-pd-review-count"><?= $reviewCount ?> review<?= $reviewCount === 1 ? '' : 's' ?></span>
                        </div>

                        <?php if (empty($reviews)): ?>
                            <div class="dn-pd-empty">No reviews yet — be the first to review this product.</div>
                        <?php else: ?>
                            <?php foreach ($reviews as $rev): ?>
                                <div class="dn-pd-review">
                                    <div class="dn-pd-review-top">
                                        <strong><?= htmlspecialchars($rev['username']) ?></strong>
                                        <span class="dn-pd-review-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="ti ti-star<?= $i <= (int) $rev['rating'] ? ' filled' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <p><?= htmlspecialchars($rev['comment']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
