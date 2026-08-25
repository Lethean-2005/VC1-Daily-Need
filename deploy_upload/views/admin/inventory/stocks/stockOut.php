<h1>STOCK OUT</h1>

<!-- Custom CSS -->
<style>
    .image2-wrapper {
        width: 50px;
        height: 50px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image2-wrapper img {
        /* width: 100%; */
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        border-radius: 4px;
    }

    .table td, .table th {
        vertical-align: middle;
        height: 60px;
    }
</style>

<!-- Stock History Table -->
<div class="card p-4 shadow mt-4">
    <h5 class="mb-3">STOCK HISTORY</h5>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th width="14%">PRODUCT IMAGE</th>
                <th>PRODUCT NAME</th>
                <th>QUANTITY</th>
                <th>TYPE</th>
                <th>TOTAL PRICE</th>
                <th>DATE</th>
            </tr>
        </thead>
        <tbody id="stockTableBody">
            <?php if (!empty($data['stockHistory'])): ?>
                <?php foreach ($data['stockHistory'] as $row): ?>
                    <tr>
                        <td>
                            <div class="image2-wrapper">
                                <img src="/<?php echo htmlspecialchars($row['product_image']); ?>"
                                     alt="<?php echo htmlspecialchars($row['product']); ?>">
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['product']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity_out']); ?></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo number_format($row['total_price'], 2); ?>$</td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No stock out history available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Stock Level Table -->
<div class="card p-4 shadow mt-4">
    <h5 class="mb-3">STOCK LEVELS</h5>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th width="14%">PRODUCT IMAGE</th>
                <th>PRODUCT NAME</th>
                <th width="20%">REMAINNING QUANTITY</th>
            </tr>
        </thead>
        <tbody id="stockLevelBody">
            <?php if (!empty($data['stockLevels'])): ?>
                <?php foreach ($data['stockLevels'] as $row): ?>
                    <tr>
                        <td>
                            <div class="image2-wrapper">
                                <img src="/<?php echo htmlspecialchars($row['product_image']); ?>"
                                     alt="<?php echo htmlspecialchars($row['product']); ?>">
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['product']); ?></td>
                        <td><?php echo htmlspecialchars($row['remaining_quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No stock levels available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
