<?php
require 'config/db.php';
include 'includes/header.php';

// Fetch Types & Customers via Clean Architecture
$productRepo = new ProductRepository($pdo);
$types = $productRepo->getAllActive();
$customerRepo = new CustomerRepository($pdo);
$customers = $customerRepo->getAllActive();

// Fetch Today's Stock via Clean Architecture
$today = date('Y-m-d');
$purchaseRepo = new PurchaseRepository($pdo);
$saleRepo = new SaleRepository($pdo);
$leftoverRepo = new LeftoverRepository($pdo);
$unitSalesService = new UnitSalesService($purchaseRepo, $leftoverRepo, $saleRepo);
$saleService = new SaleService($saleRepo, $purchaseRepo, $customerRepo, $leftoverRepo, $unitSalesService);

$todaysStock = $saleService->getTodaysStock($today);

$jsonStock = json_encode($todaysStock);
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
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
    }

    .circle-btn:active {
        transform: scale(0.95);
    }

    .circle-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    /* Colors */
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

    <!-- Summary Bar (Progress) -->
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="summary-bar" id="summaryBar" dir="rtl">
                <span>النوع: <b id="s_type">-</b></span>
                <span>الرعوي: <b id="s_rawi">-</b></span>
                <span>الزبون: <b id="s_cust">-</b></span>
                <span>الوزن: <b id="s_weight">-</b></span>
                <span>السعر: <b id="s_price">-</b></span>
            </div>

            <!-- Cancel Button (Below Summary) -->
            <div class="text-start mb-3">
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" onclick="location.reload()">
                    إلغاء العملية / جديد (X)
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN FORM -->
    <form action="requests/process_sale.php" method="POST" id="saleForm">
        <input type="hidden" name="sale_date" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="qat_type_id" id="i_type">
        <input type="hidden" name="purchase_id" id="i_pid">
        <input type="hidden" name="customer_id" id="i_cust">
        <input type="hidden" name="weight_grams" id="i_weight">
        <input type="hidden" name="quantity_units" id="i_units">
        <input type="hidden" name="unit_type" id="i_utype">
        <input type="hidden" name="price" id="i_price">
        <input type="hidden" name="payment_method" id="i_method">

        <!-- Transfer Details -->
        <input type="hidden" name="transfer_sender" id="i_tsender">
        <input type="hidden" name="transfer_receiver" id="i_treceiver">
        <input type="hidden" name="transfer_number" id="i_tnum">
        <input type="hidden" name="transfer_company" id="i_tcompany">

        <input type="hidden" name="debt_type" id="i_dtype">

        <!-- STEP 1: Qat Type -->
        <div id="step1" class="step-container active">
            <h3>اختر النوع</h3>
            <!-- STRICTLY ARABIC BUTTONS from DB (updated by script) -->
            <div class="grid-container">
                <?php foreach ($types as $t): ?>
                    <button type="button" class="circle-btn btn-type" onclick="nextStep(1, {id: <?= $t['id'] ?>, name: '<?= $t['name'] ?>'})">
                        <?= $t['name'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- STEP 2: Provider -->
        <div id="step2" class="step-container">
            <h3>اختر الرعوي</h3>
            <div class="grid-container" id="providerGrid"></div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(1)">عودة</button></div>
        </div>

        <!-- STEP 3: Customer -->
        <div id="step3" class="step-container">
            <h3>من الزبون؟</h3>
            <div class="grid-container">
                <button type="button" class="circle-btn btn-cust" onclick="showTayyarPrompt()">
                    طيار
                </button>
                <button type="button" class="circle-btn btn-cust" onclick="showCustList()">
                    بحث
                </button>
                <button type="button" class="circle-btn btn-cust" style="background: #dc3545;" onclick="showAddCust()">
                    إضافة
                </button>
            </div>

            <!-- Hidden List for Existing -->
            <div id="custList" class="d-none mt-3 w-50 mx-auto">
                <input type="text" id="cSearch" class="form-control mb-2 p-3 text-end" placeholder="...ابدأ بالكتابة" onkeyup="filterCust()">
                <div class="list-group text-end" id="cListGroup" style="max-height: 200px; overflow-y:auto;">
                    <!-- JS Populated -->
                </div>
            </div>

            <!-- Hidden Form for New -->
            <div id="newCustForm" class="d-none mt-3 w-50 mx-auto bg-white p-3 rounded shadow text-end">
                <h5>إضافة زبون جديد</h5>
                <input type="text" id="new_name" class="form-control mb-2 text-end" placeholder="الاسم الكامل">
                <input type="text" id="new_phone" class="form-control mb-2 text-end" placeholder="رقم الهاتف">
                <button type="button" class="btn btn-success w-100" onclick="saveNewCust()">حفظ واختيار</button>
            </div>

            <!-- Hidden Form for Tayyar Name -->
            <div id="tayyarForm" class="d-none mt-3 w-50 mx-auto bg-white p-3 rounded shadow text-end">
                <h5>اسم الزبون (الطيار)</h5>
                <input type="text" id="t_name" class="form-control mb-2 text-end" placeholder="الاسم الكامل">
                <button type="button" class="btn btn-warning w-100" onclick="confirmTayyar()">تأكيد واختيار</button>
            </div>

            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(2)">عودة</button></div>
        </div>

        <!-- STEP 4: Weight -->
        <div id="step4" class="step-container">
            <h3>الوزن</h3>
            <div class="grid-container">
                <!-- Gram Presets -->
                <button type="button" class="circle-btn btn-weight" onclick="nextStep(4, 50)">50 جرام<br>حق 1000</button>
                <button type="button" class="circle-btn btn-weight" onclick="nextStep(4, 100)">100 جرام<br>ثمن</button>
                <button type="button" class="circle-btn btn-weight" onclick="nextStep(4, 250)">250 جرام<br>ربع</button>
                <button type="button" class="circle-btn btn-weight" onclick="nextStep(4, 500)">500 جرام<br>نص</button>
                <button type="button" class="circle-btn btn-weight" onclick="nextStep(4, 1000)">1000 جرام<br>كيلو</button>

                <!-- Manual Input -->
                <button type="button" class="circle-btn bg-dark text-white" onclick="document.getElementById('manualWeight').classList.remove('d-none')">
                    يدوي
                </button>
            </div>

            <div id="manualWeight" class="d-none mt-4 w-50 mx-auto">
                <div class="row g-2">
                    <div class="col-6">
                        <label>جرام</label>
                        <input type="number" id="m_grams" class="form-control p-3 text-center fs-4" placeholder="جرام" oninput="syncWeight('g')">
                    </div>
                    <div class="col-6">
                        <label>كيلو</label>
                        <input type="number" id="m_kg" class="form-control p-3 text-center fs-4" placeholder="كيلوجرام" oninput="syncWeight('k')">
                    </div>
                </div>
                <!-- Confirmation -->
                <button type="button" class="btn btn-primary w-100 mt-3 p-2" onclick="confirmManualWeight()">تأكيد الوزن</button>
            </div>

            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(3)">عودة</button></div>
        </div>

        <!-- STEP 4U: Units (Count-based) - dynamically populated -->
        <div id="step4u" class="step-container">
            <h3>كم <span id="unitLabel">حبة</span>؟</h3>
            <p id="unitMaxLabel" class="text-muted small"></p>
            <div class="grid-container" id="unitBtnsGrid"></div>
            <div id="manualUnits" class="d-none mt-4 w-25 mx-auto">
                <input type="number" id="m_units_val" class="form-control p-3 text-center fs-4" placeholder="العدد" min="1">
                <button type="button" class="btn btn-primary w-100 mt-3 p-2" onclick="confirmManualUnits()">تأكيد العدد</button>
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

                <button type="button" class="circle-btn bg-dark text-white" onclick="document.getElementById('manualPrice').classList.remove('d-none')">
                    يدوي
                </button>
            </div>
            <div id="manualPrice" class="d-none mt-3 w-25 mx-auto">
                <input type="number" id="m_price_val" class="form-control p-3 text-center fs-4" placeholder="ريال">
                <!-- Confirm button like weight (#11) -->
                <button type="button" class="btn btn-primary w-100 mt-3 p-2" onclick="confirmManualPrice()">تأكيد السعر</button>
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(4)">عودة</button></div>
        </div>

        <!-- STEP 6: Payment Method -->
        <div id="step6" class="step-container">
            <h3>طريقة الدفع</h3>
            <div class="grid-container">
                <button type="button" class="circle-btn btn-pay" onclick="finishSale('Cash', null)">نقد</button>
                <button type="button" class="circle-btn btn-pay" style="background: #dc3545;" onclick="nextStep(6, 'Debt')">آجل</button>
                <button type="button" class="circle-btn btn-pay" style="background: #0dcaf0;" onclick="showTransferInputs()">تحويل</button>
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(5)">عودة</button></div>
        </div>

        <!-- STEP 6.5: Transfer Details -->
        <div id="step_transfer" class="step-container">
            <h3>تفاصيل التحويل</h3>
            <div class="w-50 mx-auto text-end">
                <div class="mb-3">
                    <label class="form-label">المستلم (الذي استلم)</label>
                    <select id="t_receiver_val" class="form-select text-end p-3">
                        <option value="ماجد القادري">ماجد القادري</option>
                        <option value="ابن اخيه">ابن اخيه</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">الشركة المستخدمة للتحويل</label>
                    <select id="t_company_val" class="form-select text-end p-3" onchange="toggleOtherCompany(this)">
                        <option value="الكريمي">الكريمي</option>
                        <option value="كاك بنك">كاك بنك</option>
                        <option value="بنك التسليف">بنك التسليف</option>
                        <option value="مدى">مدى</option>
                        <option value="بنك اليمن الدولي">بنك اليمن الدولي</option>
                        <option value="بنك اليمن والكويت">بنك اليمن والكويت</option>
                        <option value="يمن موبايل">يمن موبايل</option>
                        <option value="سبأفون">سبأفون</option>
                        <option value="يو">يو</option>
                        <option value="أخرى">أخرى</option>
                    </select>
                    <!-- Show free-text input when أخرى selected (#14) -->
                    <input type="text" id="t_company_other" class="form-control mt-2 d-none" placeholder="اكتب اسم الشركة">
                </div>
                <div class="mb-3">
                    <label class="form-label">اسم المرسل (الذي حول)</label>
                    <input type="text" id="t_sender_val" class="form-control text-end p-3">
                </div>
                <div class="mb-3">
                    <label class="form-label">رقم الحوالة / السند <span class="text-danger">*</span></label>
                    <input type="number" id="t_num_val" class="form-control text-end p-3" required placeholder="رقم الحوالة إلزامي">
                </div>
                <button type="button" class="btn btn-success w-100" onclick="finishTransfer()">حفظ التحويل</button>
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(6)">عودة</button></div>
        </div>

        <!-- STEP 7: Debt Type -->
        <div id="step7" class="step-container">
            <h3>نوع الدين</h3>
            <div class="grid-container">
                <button type="button" class="circle-btn btn-pay" style="background: #dc3545;" onclick="finishSale('Debt', 'Daily')">يومي</button>
                <button type="button" class="circle-btn btn-pay" style="background: #fd7e14;" onclick="finishSale('Debt', 'Monthly')">شهري</button>
                <button type="button" class="circle-btn btn-pay" style="background: #ffc107;" onclick="finishSale('Debt', 'Yearly')">سنوي</button>
            </div>
            <div class="mt-4"><button type="button" class="btn btn-secondary" onclick="backStep(6)">عودة</button></div>
        </div>

    </form>
</div>

<!-- AJAX Script -->
<script>
    const allCustomers = <?= $jsonCustomers ?>;
    const stockData = <?= $jsonStock ?>;
    let currentStep = 1;

    function nextStep(step, data) {
        // Handle Data Logic
        if (step === 1) { // Type
            document.getElementById('i_type').value = data.id;
            document.getElementById('s_type').innerText = data.name;
            populateProviders(data.id);
        } else if (step === 2) { // Provider
            document.getElementById('i_pid').value = data.id;
            document.getElementById('s_rawi').innerText = data.name;
            document.getElementById('i_utype').value = data.unit_type;
            window._selectedRemainingUnits = data.remaining_units || 10;

            goTo(3); // Go to Customer selection
            return;
        } else if (step === 3) { // Customer
            document.getElementById('i_cust').value = data.id;
            document.getElementById('s_cust').innerText = data.name;

            // Route to Weight or Units based on unit_type from Step 2
            const utype = document.getElementById('i_utype').value;
            if (utype === 'weight') {
                goTo(4);
            } else {
                // Build dynamic unit buttons
                const maxU = window._selectedRemainingUnits || 10;
                const grid = document.getElementById('unitBtnsGrid');
                grid.innerHTML = '';
                const btnCount = Math.min(maxU, 8);
                for (let i = 1; i <= btnCount; i++) {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'circle-btn btn-weight';
                    b.textContent = i;
                    b.onclick = () => nextStep(4.5, i);
                    grid.appendChild(b);
                }
                const manBtn = document.createElement('button');
                manBtn.type = 'button';
                manBtn.className = 'circle-btn bg-dark text-white';
                manBtn.textContent = 'يدوي';
                manBtn.onclick = () => document.getElementById('manualUnits').classList.remove('d-none');
                grid.appendChild(manBtn);
                document.getElementById('unitLabel').innerText = utype;
                document.getElementById('unitMaxLabel').innerText = 'المتاح: ' + maxU + ' ' + utype;
                goTo('4u');
            }
            return;
        } else if (step === 4) { // Weight (Finalizing Weight)
            document.getElementById('i_weight').value = data;
            document.getElementById('s_weight').innerText = data + 'g';
            goTo(5);
            return;
        } else if (step === 4.5) { // Units (Finalizing Units)
            document.getElementById('i_units').value = data;
            document.getElementById('s_weight').innerText = data + ' (' + document.getElementById('i_utype').value + ')';
            goTo(5);
            return;
        } else if (step === 5) { // Price
            document.getElementById('i_price').value = data;
            document.getElementById('s_price').innerText = data;
        } else if (step === 6) { // Debt Init
            document.getElementById('i_method').value = data;
            if (data === 'Debt') {
                goTo(7);
                return;
            }
        }

        goTo(step + 1);
    }

    function showTransferInputs() {
        document.getElementById('i_method').value = 'Internal Transfer';
        goTo('_transfer');
    }

    function finishTransfer() {
        const sender = document.getElementById('t_sender_val').value;
        const receiver = document.getElementById('t_receiver_val').value;
        const num = document.getElementById('t_num_val').value;
        // #14: If 'أخرى' selected, use free-text input
        let company = document.getElementById('t_company_val').value;
        if (company === 'أخرى') {
            const otherInput = document.getElementById('t_company_other');
            company = otherInput ? otherInput.value.trim() : '';
            if (!company) return alert('يرجى كتابة اسم الشركة');
        }

        if (!sender) return alert('اسم المرسل مطلوب');
        // #15: Transfer number is required
        if (!num) return alert('رقم الحوالة مطلوب قبل القبول');

        document.getElementById('i_tsender').value = sender;
        document.getElementById('i_treceiver').value = receiver;
        document.getElementById('i_tnum').value = num;
        document.getElementById('i_tcompany').value = company;

        finishSale('Internal Transfer', null);
    }

    function finishSale(method, debtType) {
        if (method) document.getElementById('i_method').value = method;
        if (debtType) document.getElementById('i_dtype').value = debtType;

        // Validation for Debt
        const m = document.getElementById('i_method').value;
        const c = document.getElementById('i_cust').value;
        if (m === 'Debt' && !c) {
            alert('يرجى اختيار الزبون أولاً لعملية الآجل');
            goTo(3);
            return;
        }

        document.getElementById('saleForm').submit();
    }

    function goTo(step) {
        document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
        document.getElementById('step' + step).classList.add('active');
        currentStep = step;
    }

    function backStep(from) {
        if (from === 6 && currentStep === '_transfer') {
            goTo(6);
            return;
        }
        goTo(from);
    }

    // --- Helpers ---
    function populateProviders(typeId) {
        const grid = document.getElementById('providerGrid');
        grid.innerHTML = '';

        // --- Admin-Only Technique Restriction ---
        const userRole = '<?= $_SESSION['role'] ?>';
        const isAdmin = ['admin', 'super_admin'].includes(userRole);

        const providers = stockData.filter(i => {
            if (i.qat_type_id != typeId) return false;
            // Unit-based products are now visible to all staff
            return true;
        });

        if (providers.length === 0) {
            grid.innerHTML = '<div class="alert alert-warning w-100">لا يوجد مخزون متاح لهذا النوع اليوم</div>';
        } else {
            providers.forEach(p => {
                const btn = document.createElement('button');
                btn.className = 'circle-btn btn-provider';

                // Inventory Check
                let remaining = 0;
                let unitText = '';
                if (p.unit_type === 'weight') {
                    remaining = parseFloat(p.remaining_kg);
                    unitText = 'kg';
                } else {
                    remaining = parseInt(p.remaining_units);
                    unitText = p.unit_type;
                }

                const isSoldOut = remaining <= 0;

                let label = p.provider_name;
                if (isSoldOut) {
                    label += '<br><small class="text-danger">(نفذ)</small>';
                    btn.style.opacity = '0.6';
                    btn.disabled = true;
                } else {
                    label += `<br><small>${remaining}${unitText}</small>`;
                }

                btn.innerHTML = label;
                btn.type = 'button';

                if (!isSoldOut) {
                    btn.onclick = () => nextStep(2, {
                        id: p.id,
                        name: p.provider_name,
                        unit_type: p.unit_type,
                        remaining_units: p.remaining_units || 0
                    });
                }

                grid.appendChild(btn);
            });
        }
    }

    // Weight Sync Logic
    function syncWeight(source) {
        const grams = document.getElementById('m_grams');
        const kg = document.getElementById('m_kg');

        if (source === 'g') {
            kg.value = (grams.value / 1000).toFixed(3);
        } else {
            grams.value = (kg.value * 1000).toFixed(0);
        }
    }

    function confirmManualWeight() {
        const grams = document.getElementById('m_grams').value;
        if (grams > 0) {
            nextStep(4, grams);
        } else {
            alert('الرجاء إدخال الوزن');
        }
    }

    function confirmManualUnits() {
        const units = document.getElementById('m_units_val').value;
        if (units > 0) {
            nextStep(4.5, units);
        } else {
            alert('الرجاء إدخال العدد');
        }
    }

    // Confirm manual price (#11)
    function confirmManualPrice() {
        const price = document.getElementById('m_price_val').value;
        if (price > 0) {
            nextStep(5, price);
        } else {
            alert('الرجاء إدخال السعر');
        }
    }

    // Toggle other company input (#14)
    function toggleOtherCompany(sel) {
        const otherInput = document.getElementById('t_company_other');
        if (otherInput) {
            otherInput.classList.toggle('d-none', sel.value !== 'أخرى');
            if (sel.value !== 'أخرى') otherInput.value = '';
        }
    }

    // Customer Logic
    function showCustList() {
        document.getElementById('custList').classList.remove('d-none');
        document.getElementById('newCustForm').classList.add('d-none');
        renderCustList(allCustomers);
    }

    function filterCust() {
        const term = document.getElementById('cSearch').value.toLowerCase();
        const filtered = allCustomers.filter(c => {
            const nameMatch = c.name && c.name.toLowerCase().includes(term);
            const phoneMatch = c.phone && String(c.phone).includes(term);
            return nameMatch || phoneMatch;
        });
        renderCustList(filtered);
    }

    function renderCustList(list) {
        const div = document.getElementById('cListGroup');
        div.innerHTML = '';
        list.forEach(c => {
            const a = document.createElement('a');
            a.className = 'list-group-item list-group-item-action text-end';
            a.style.cursor = 'pointer';
            a.innerHTML = `<b>${c.name}</b> <small>${c.phone || ''}</small>`;
            a.onclick = () => nextStep(3, {
                id: c.id,
                name: c.name
            });
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

        // Use existing saveNewCust logic but for t_name
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
        if (!name) return alert("الاسم مطلوب"); // Name required

        // Simple AJAX to add customer
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
                    // Add to local list and select
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

<!-- Quick Report Link -->
<div class="text-center mt-4 mb-5 no-print">
    <a href="reports.php?report_type=Daily" class="btn btn-outline-secondary">
        <i class="fas fa-file-invoice me-2"></i> تقرير اليوم المفصل
    </a>
</div>

<?php include 'includes/footer.php'; ?>