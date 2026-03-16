<?php
session_start();
require_once 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?auth=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>لوحة تسيير العمليات</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">بوابة العمليات - Qat ERP</span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">تسجيل الخروج</a>
        </div>
    </nav>

    <?php
    require_once 'includes/Autoloader.php';
    $productRepo = new ProductRepository($pdo);
    $today = date('Y-m-d');

    // Total Shipments
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM purchases WHERE purchase_date = ?");
    $stmt->execute([$today]);
    $count = $stmt->fetchColumn();

    // Total Weight Sent (Field View)
    $stmt = $pdo->prepare("SELECT SUM(source_weight_grams) FROM purchases WHERE purchase_date = ?");
    $stmt->execute([$today]);
    $total_grams = $stmt->fetchColumn() ?: 0;
    $total_kg = $total_grams / 1000;

    // Total Cost (Agreed Price)
    $stmt = $pdo->prepare("SELECT SUM(agreed_price) FROM purchases WHERE purchase_date = ?");
    $stmt->execute([$today]);
    $total_cost = $stmt->fetchColumn() ?: 0;
    ?>

    <div class="row g-4 animate__animated animate__fadeIn">
        <div class="col-12 text-center mb-2">
            <h2 class="fw-bold text-dark"><i class="fas fa-tachometer-alt text-warning me-2"></i> لوحة تسيير العمليات</h2>
            <p class="text-secondary">متابعة الأداء اليومي للتوريد الميداني</p>
        </div>

        <!-- Stats Cards -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute end-0 bottom-0 opacity-25 p-3" style="font-size: 5rem;">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h6 class="text-uppercase small fw-bold opacity-75">شحنات اليوم</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= $count ?></h2>
                    <p class="mt-2 mb-0 small"><i class="fas fa-calendar-day me-1"></i> إجمالي العمليات المسجلة</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute end-0 bottom-0 opacity-25 p-3" style="font-size: 5rem;">
                        <i class="fas fa-weight-hanging"></i>
                    </div>
                    <h6 class="text-uppercase small fw-bold opacity-75">إجمالي الوزن</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= number_format($total_kg, 2) ?> <small class="fs-6">كجم</small></h2>
                    <p class="mt-2 mb-0 small"><i class="fas fa-balance-scale me-1"></i> الوزن المرسل من الميدان</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="position-absolute end-0 bottom-0 opacity-25 p-3" style="font-size: 5rem;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h6 class="text-uppercase small fw-bold opacity-75">إجمالي التكلفة</h6>
                    <h2 class="display-5 fw-bold mb-0"><?= number_format($total_cost) ?> <small class="fs-6">ريال</small></h2>
                    <p class="mt-2 mb-0 small"><i class="fas fa-receipt me-1"></i> تكلفة الشراء المتفق عليها</p>
                </div>
            </div>
        </div>

        <!-- Qat Types Pricing Management -->
        <div class="col-12 mt-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-tags me-2 text-warning"></i> أسعار الأنواع (التقنيات الثلاث)</h4>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="pricingTable">
                            <thead class="table-light">
                                <tr>
                                    <th>النوع</th>
                                    <th>سعر الوزن (جرام)</th>
                                    <th>سعر القبضة</th>
                                    <th>سعر القرطاس</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $productRepo = new ProductRepository($pdo);
                                $allTypes = $productRepo->getAllActive();
                                foreach ($allTypes as $t):
                                ?>
                                    <tr data-id="<?= $t['id'] ?>">
                                        <td class="fw-bold"><?= htmlspecialchars($t['name']) ?></td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm price-weight" value="<?= (float)$t['price_weight'] ?>" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm price-qabdah" value="<?= (float)$t['price_qabdah'] ?>" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm price-qartas" value="<?= (float)$t['price_qartas'] ?>" placeholder="0">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="saveTypePrice(<?= $t['id'] ?>, this)">
                                                <i class="fas fa-save"></i> حفظ
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Action Section -->
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-4 bg-warning d-flex align-items-center justify-content-center p-5">
                        <i class="fas fa-truck-loading fa-6x text-white"></i>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-5">
                            <h3 class="fw-bold text-dark mb-3">مركز إدارة التوريد</h3>
                            <p class="text-secondary fs-5 mb-4">
                                ابدأ بتسجيل الشحنات اليومية، إدارة الموردين (الرعية)، وتتبع المبالغ المصروفة في الميدان بكل سهولة ودقة.
                            </p>
                            <div class="d-grid gap-3 d-md-flex">
                                <a href="sourcing.php" class="btn btn-warning btn-lg px-5 py-3 fw-bold rounded-pill shadow-sm">
                                    <i class="fas fa-plus-circle me-2"></i> واجهة التوريد
                                </a>
                                <a href="providers.php" class="btn btn-outline-dark btn-lg px-5 py-3 fw-bold rounded-pill">
                                    <i class="fas fa-users-cog me-2"></i> إدارة الموردين
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function saveTypePrice(id, btn) {
            const row = btn.closest('tr');
            const data = {
                id: id,
                price_weight: row.querySelector('.price-weight').value,
                price_qabdah: row.querySelector('.price-qabdah').value,
                price_qartas: row.querySelector('.price-qartas').value
            };

            const formData = new FormData();
            for (let key in data) formData.append(key, data[key]);

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch('requests/update_type_prices.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        btn.className = 'btn btn-success btn-sm rounded-pill px-3';
                        btn.innerHTML = '<i class="fas fa-check"></i> تم';
                        setTimeout(() => {
                            btn.className = 'btn btn-primary btn-sm rounded-pill px-3';
                            btn.innerHTML = '<i class="fas fa-save"></i> حفظ';
                            btn.disabled = false;
                        }, 2000);
                    } else {
                        alert('خطأ: ' + res.message);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-save"></i> حفظ';
                    }
                });
        }
    </script>

    <?php include 'includes/footer.php'; ?>