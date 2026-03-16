<?php
require_once 'config/db.php';
require_once 'includes/Autoloader.php';
include 'includes/header.php';

// Initialize Repositories
$customerRepo = new CustomerRepository($pdo);
$leftoverRepo = new LeftoverRepository($pdo);
$purchaseRepo = new PurchaseRepository($pdo);
$saleRepo = new SaleRepository($pdo);
$productRepo = new ProductRepository($pdo);

// Initialize Services
$unitSalesService = new UnitSalesService($purchaseRepo, $leftoverRepo, $saleRepo);
$saleService = new SaleService($saleRepo, $purchaseRepo, $customerRepo, $leftoverRepo, $unitSalesService);

$customers = $customerRepo->getAllActive();
$types = $productRepo->getAllActive();

// Unified stock calculation via Service
$leftoverStocks = $saleService->getAvailableLeftoverStock();

$jsonStocks = json_encode($leftoverStocks);
$jsonCustomers = json_encode($customers);
?>

<style>
    .step-container {
        display: none;
        text-align: center;
        animation: fadeIn 0.4s;
    }

    .step-container.active {
        display: block;
    }

    .grid-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        margin-top: 20px;
    }

    .circle-btn {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        border: none;
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .circle-btn:active {
        transform: scale(0.95);
    }

    .circle-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-type {
        background: linear-gradient(135deg, #198754, #28a745);
    }

    .btn-provider {
        background: linear-gradient(135deg, #0d6efd, #0dcaf0);
    }

    .btn-cust {
        background: linear-gradient(135deg, #6610f2, #6f42c1);
    }

    .btn-weight {
        background: linear-gradient(135deg, #ffc107, #ffca2c);
        color: #000;
    }

    .btn-price {
        background: linear-gradient(135deg, #fd7e14, #ff9f43);
    }

    .btn-pay {
        background: linear-gradient(135deg, #20c997, #28a745);
    }

    .summary-bar {
        background: #343a40;
        color: white;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-around;
        font-size: 0.9rem;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="text-center mb-4 mt-3 fw-bold text-dark"><i class="fas fa-recycle text-warning me-2"></i> بيع البقايا</h2>

            <div class="summary-bar" id="summaryBar" dir="rtl">
                <span>النوع: <b id="s_type">-</b></span>
                <span>الرعوي: <b id="s_rawi">-</b></span>
                <span>الزبون: <b id="s_cust">-</b></span>
                <span>الكمية: <b id="s_weight">-</b></span>
                <span>السعر: <b id="s_price">-</b></span>
            </div>

            <div class="text-start mb-3">
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" onclick="location.reload()">إلغاء العملية / جديد (X)</button>
            </div>
        </div>
    </div>

    <form action="requests/process_sale.php" method="POST" id="saleForm">
        <input type="hidden" name="sale_date" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="qat_type_id" id="i_type">
        <input type="hidden" name="purchase_id" id="i_pid">
        <input type="hidden" name="leftover_id" id="i_lid">
        <input type="hidden" name="qat_status" id="i_status" value="">
        <input type="hidden" name="source_page" value="leftovers">
        <input type="hidden" name="customer_id" id="i_cust">
        <input type="hidden" name="weight_grams" id="i_weight" value="0">
        <input type="hidden" name="quantity_units" id="i_units" value="0">
        <input type="hidden" name="unit_type" id="i_unit_type" value="weight">
        <input type="hidden" name="price" id="i_price">
        <input type="hidden" name="payment_method" id="i_method">
        <input type="hidden" name="debt_type" id="i_dtype">

        <!-- STEP 1: Qat Type -->
        <div id="step1" class="step-container active">
            <h3>اختر النوع</h3>
            <div class="grid-container">
                <?php foreach ($types as $t): ?>
                    <button type="button" class="circle-btn btn-type" onclick="nextStep(1, {id: <?= $t['id'] ?>, name: '<?= addslashes($t['name']) ?>'})">
                        <?= $t['name'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- STEP 2: Provider (Providers from leftovers) -->
        <div id="step2" class="step-container">
            <h3>اختر الرعوي / الدفعة</h3>
            <div class="grid-container" id="providerGrid"></div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(1)">عودة</button></div>
        </div>

        <!-- STEP 3: Customer -->
        <div id="step3" class="step-container">
            <h3>من الزبون؟</h3>
            <div class="grid-container">
                <button type="button" class="circle-btn btn-cust" onclick="showTayyarPrompt()">طيار</button>
                <button type="button" class="circle-btn btn-cust" onclick="showCustList()">بحث</button>
                <button type="button" class="circle-btn btn-cust" style="background: #dc3545;" onclick="showAddCust()">إضافة</button>
            </div>
            <div id="custList" class="d-none mt-3 w-50 mx-auto">
                <input type="text" id="cSearch" class="form-control mb-2 p-3 text-end" placeholder="...ابدأ بالكتابة" onkeyup="filterCust()">
                <div class="list-group text-end" id="cListGroup" style="max-height: 200px; overflow-y:auto;"></div>
            </div>
            <div id="newCustForm" class="d-none mt-3 w-50 mx-auto bg-white p-3 rounded shadow text-end">
                <h5>إضافة زبون جديد</h5>
                <input type="text" id="new_name" class="form-control mb-2 text-end" placeholder="الاسم الكامل">
                <input type="text" id="new_phone" class="form-control mb-2 text-end" placeholder="رقم الهاتف">
                <button type="button" class="btn btn-success w-100" onclick="saveNewCust()">حفظ واختيار</button>
            </div>
            <div id="tayyarForm" class="d-none mt-3 w-50 mx-auto bg-white p-3 rounded shadow text-end">
                <h5>اسم الزبون (الطيار)</h5>
                <input type="text" id="t_name" class="form-control mb-2 text-end" placeholder="الاسم الكامل">
                <button type="button" class="btn btn-warning w-100" onclick="confirmTayyar()">تأكيد واختيار</button>
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(2)">عودة</button></div>
        </div>

        <!-- STEP 4: Weight (for weight-based stock) -->
        <div id="step4_weight" class="step-container">
            <h3>الوزن</h3>
            <div class="grid-container">
                <button type="button" class="circle-btn btn-weight" onclick="setWeight(50)">50g</button>
                <button type="button" class="circle-btn btn-weight" onclick="setWeight(100)">100g</button>
                <button type="button" class="circle-btn btn-weight" onclick="setWeight(250)">250g</button>
                <button type="button" class="circle-btn btn-weight" onclick="setWeight(500)">500g</button>
                <button type="button" class="circle-btn btn-weight" onclick="setWeight(1000)">1000g</button>
                <button type="button" class="circle-btn bg-dark text-white" onclick="document.getElementById('manualWeight').classList.remove('d-none')">يدوي</button>
            </div>
            <div id="manualWeight" class="d-none mt-3 w-25 mx-auto">
                <input type="number" id="m_weight_val" class="form-control p-3 text-center fs-4" placeholder="جرام" onchange="setWeight(this.value)">
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(3)">عودة</button></div>
        </div>

        <!-- STEP 4 (Units): for Qabdah / Qartas stocks -->
        <div id="step4_units" class="step-container">
            <h3 id="step4_units_title">العدد</h3>
            <p id="step4_units_max" class="text-muted"></p>
            <div class="grid-container" id="unitBtnsGrid"></div>
            <div id="manualUnits" class="d-none mt-3 w-25 mx-auto">
                <input type="number" id="m_units_val" class="form-control p-3 text-center fs-4" min="1" placeholder="العدد" onchange="setUnits(this.value)">
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(3)">عودة</button></div>
        </div>

        <!-- STEP 5: Price -->
        <div id="step5" class="step-container">
            <h3>السعر</h3>
            <div class="grid-container">
                <button type="button" class="circle-btn btn-price" onclick="nextStep(5, 1000)">1000</button>
                <button type="button" class="circle-btn btn-price" onclick="nextStep(5, 2000)">2000</button>
                <button type="button" class="circle-btn btn-price" onclick="nextStep(5, 3000)">3000</button>
                <button type="button" class="circle-btn btn-price" onclick="nextStep(5, 5000)">5000</button>
                <button type="button" class="circle-btn btn-price" onclick="nextStep(5, 10000)">10000</button>
                <button type="button" class="circle-btn bg-dark text-white" onclick="document.getElementById('manualPrice').classList.remove('d-none')">يدوي</button>
            </div>
            <div id="manualPrice" class="d-none mt-3 w-25 mx-auto">
                <input type="number" id="m_price_val" class="form-control p-3 text-center fs-4" placeholder="YER" onchange="nextStep(5, this.value)">
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(4)">عودة</button></div>
        </div>

        <!-- STEP 6: Payment -->
        <div id="step6" class="step-container">
            <h3>طريقة الدفع</h3>
            <div class="grid-container">
                <button type="button" class="circle-btn btn-pay" onclick="finishSale('Cash', null)">نقد</button>
                <button type="button" class="circle-btn btn-pay" style="background: #dc3545;" onclick="nextStep(6, 'Debt')">آجل</button>
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(5)">عودة</button></div>
        </div>

        <!-- STEP 7: Debt -->
        <div id="step7" class="step-container">
            <h3>نوع الدين</h3>
            <div class="grid-container">
                <button type="button" class="circle-btn btn-pay" style="background: #dc3545;" onclick="finishSale('Debt', 'Daily')">يومي</button>
                <button type="button" class="circle-btn btn-pay" style="background: #fd7e14;" onclick="finishSale('Debt', 'Monthly')">شهري</button>
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(6)">عودة</button></div>
        </div>
    </form>
</div>

<script>
    const allStocks = <?= $jsonStocks ?>;
    const allCustomers = <?= $jsonCustomers ?>;
    let currentStep = 1;
    let selectedStock = null; // Holds the selected provider/stock object

    function nextStep(step, data) {
        if (step === 1) { // Type selected
            document.getElementById('i_type').value = data.id;
            document.getElementById('s_type').innerText = data.name;
            populateProviders(data.id);
        } else if (step === 2) { // Provider/stock selected
            document.getElementById('i_lid').value = data.type === 'manual' ? data.id : '';
            document.getElementById('i_pid').value = data.type === 'momsi' ? data.id : '';
            document.getElementById('i_status').value = data.type === 'momsi' ? 'Momsi' : 'Leftover';
            document.getElementById('s_rawi').innerText = data.name;
            selectedStock = data; // Save full stock object
        } else if (step === 3) { // Customer selected
            document.getElementById('i_cust').value = data.id;
            document.getElementById('s_cust').innerText = data.name;
        } else if (step === 5) { // Price
            document.getElementById('i_price').value = data;
            document.getElementById('s_price').innerText = data;
        } else if (step === 6) { // Payment Init
            document.getElementById('i_method').value = data;
            if (data === 'Debt') {
                goTo(7);
                return;
            }
        }
        goTo(step + 1);
    }

    function setWeight(grams) {
        document.getElementById('i_weight').value = grams;
        document.getElementById('i_units').value = 0;
        document.getElementById('i_unit_type').value = 'weight';
        document.getElementById('s_weight').innerText = grams + ' جرام';
        goTo(5);
    }

    function setUnits(count) {
        count = parseInt(count);
        if (!count || count < 1) return;
        const ut = selectedStock ? (selectedStock.unit_type || 'وحدة') : 'وحدة';
        document.getElementById('i_units').value = count;
        document.getElementById('i_weight').value = 0;
        document.getElementById('i_unit_type').value = ut;
        document.getElementById('s_weight').innerText = ut + ' × ' + count;
        goTo(5);
    }

    function populateProviders(typeId) {
        const grid = document.getElementById('providerGrid');
        grid.innerHTML = '';
        const providers = allStocks.filter(s => s.qat_type_id == typeId);

        if (providers.length === 0) {
            grid.innerHTML = '<div class="alert alert-warning w-100">لا توجد بقايا لهذا النوع حالياً</div>';
        } else {
            providers.forEach(p => {
                const btn = document.createElement('button');
                btn.className = 'circle-btn btn-provider';
                btn.type = 'button';

                // Show unit count for unit-based stocks, weight for weight-based
                const ut = p.unit_type || 'weight';
                let qtyLabel;
                if (ut === 'قبضة') {
                    qtyLabel = `<small>قبضة × ${p.remaining_units}</small>`;
                } else if (ut === 'قراطيس') {
                    qtyLabel = `<small>قراطيس × ${p.remaining_units}</small>`;
                } else {
                    qtyLabel = `<small>${p.remaining_kg} كجم</small>`;
                }

                btn.innerHTML = `<span>${p.provider_name}</span><br><small class="badge bg-light text-dark text-wrap">${p.sale_date || p.source_date}</small><br>${qtyLabel}`;
                btn.onclick = () => nextStep(2, {
                    id: p.id,
                    name: p.provider_name,
                    type: p.type,
                    unit_type: ut,
                    remaining_units: p.remaining_units,
                    remaining_kg: p.remaining_kg
                });
                grid.appendChild(btn);
            });
        }
    }

    function goTo(step) {
        // When going to step 4, decide which step4 variant to show
        if (step === 4) {
            const ut = selectedStock ? (selectedStock.unit_type || 'weight') : 'weight';
            const isUnits = (ut === 'قبضة' || ut === 'قراطيس');

            document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));

            if (isUnits) {
                // Build unit buttons dynamically based on remaining stock
                const maxU = selectedStock.remaining_units || 10;
                const grid = document.getElementById('unitBtnsGrid');
                grid.innerHTML = '';
                const btnCount = Math.min(maxU, 6);
                for (let i = 1; i <= btnCount; i++) {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'circle-btn btn-weight';
                    b.textContent = i;
                    b.onclick = () => setUnits(i);
                    grid.appendChild(b);
                }
                // Add manual entry button
                const manBtn = document.createElement('button');
                manBtn.type = 'button';
                manBtn.className = 'circle-btn bg-dark text-white';
                manBtn.textContent = 'يدوي';
                manBtn.onclick = () => document.getElementById('manualUnits').classList.remove('d-none');
                grid.appendChild(manBtn);

                document.getElementById('step4_units_title').innerText = 'كم ' + ut + '؟';
                document.getElementById('step4_units_max').innerText = 'المتاح: ' + maxU + ' ' + ut;
                document.getElementById('step4_units').classList.add('active');
                currentStep = 4;
            } else {
                document.getElementById('step4_weight').classList.add('active');
                currentStep = 4;
            }
            return;
        }

        document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
        const target = document.getElementById('step' + step);
        if (target) target.classList.add('active');
        currentStep = step;
    }

    function backStep(from) {
        // If backing from step5, go back to the correct step4 variant
        if (from === 4) {
            goTo(4);
        } else {
            goTo(from);
        }
    }

    function finishSale(method, debtType) {
        if (method) document.getElementById('i_method').value = method;
        if (debtType) document.getElementById('i_dtype').value = debtType;
        document.getElementById('saleForm').submit();
    }

    // Customer Helpers
    function showCustList() {
        document.getElementById('custList').classList.remove('d-none');
        document.getElementById('newCustForm').classList.add('d-none');
        document.getElementById('tayyarForm').classList.add('d-none');
        renderCustList(allCustomers);
    }

    function filterCust() {
        const term = document.getElementById('cSearch').value.toLowerCase();
        const filtered = allCustomers.filter(c => c.name.toLowerCase().includes(term) || (c.phone && c.phone.includes(term)));
        renderCustList(filtered);
    }

    function renderCustList(list) {
        const div = document.getElementById('cListGroup');
        div.innerHTML = '';
        list.forEach(c => {
            const a = document.createElement('a');
            a.className = 'list-group-item list-group-item-action text-end';
            a.innerHTML = `<b>${c.name}</b> ${c.phone ? '<small>('+c.phone+')</small>' : ''}`;
            a.onclick = () => nextStep(3, {
                id: c.id,
                name: c.name
            });
            a.style = "cursor:pointer";
            div.appendChild(a);
        });
    }

    function showTayyarPrompt() {
        document.getElementById('custList').classList.add('d-none');
        document.getElementById('newCustForm').classList.add('d-none');
        document.getElementById('tayyarForm').classList.remove('d-none');
    }

    function confirmTayyar() {
        const name = document.getElementById('t_name').value;
        if (!name) return alert("الاسم مطلوب");
        document.getElementById('new_name').value = name;
        document.getElementById('new_phone').value = '';
        saveNewCust();
    }

    function showAddCust() {
        document.getElementById('custList').classList.add('d-none');
        document.getElementById('tayyarForm').classList.add('d-none');
        document.getElementById('newCustForm').classList.remove('d-none');
    }

    function saveNewCust() {
        const name = document.getElementById('new_name').value;
        const phone = document.getElementById('new_phone').value;
        if (!name) return alert("الاسم مطلوب");
        const formData = new FormData();
        formData.append('name', name);
        formData.append('phone', phone);
        fetch('requests/add_customer_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allCustomers.push({
                        id: data.id,
                        name: name,
                        phone: phone
                    });
                    nextStep(3, {
                        id: data.id,
                        name: name
                    });
                } else {
                    alert("خطأ: " + (data.error || "فشل في إضافة الزبون"));
                }
            });
    }
</script>

<?php include 'includes/footer.php'; ?>