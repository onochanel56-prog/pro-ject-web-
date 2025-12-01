<?php
// ไฟล์: sellout.php
// หน้ารายงาน Sell Out พร้อมใช้งาน

// Include API Function
require_once 'api_sellout.php';

// ตั้งค่า timezone
date_default_timezone_set('Asia/Vientiane');

// เริ่มจับเวลา
$startTime = microtime(true);

// ดึงข้อมูลจาก API (730 วัน = 2 ปี)
$data = getSellOutFromAPI(730);

// คำนวณเวลาที่ใช้
$loadTime = round(microtime(true) - $startTime, 2);

// ตรวจสอบข้อมูล
$isError = ($data === null);
$items = $isError ? [] : ($data['datas'] ?? []); 

// ดึงรายการสำหรับ Dropdown Filters
$brands = [];
$itemTypes = [];
$customers = [];

if (!$isError && !empty($items)) {
    // Brand Filter
    $brandsRaw = array_filter(array_column($items, "BrandID"));
    $brands = array_values(array_unique($brandsRaw));
    sort($brands);
    
    // ItemType Filter
    $itemTypesRaw = array_filter(array_column($items, "ItemTypeID"));
    $itemTypes = array_values(array_unique($itemTypesRaw));
    sort($itemTypes);
    
    // Customer Filter (เอาแค่ 100 รายแรก)
    $customersRaw = array_filter(array_column($items, "CustomerName"));
    $customersUnique = array_unique($customersRaw);
    sort($customersUnique);
    $customers = array_slice($customersUnique, 0, 100);
}

// คำนวณยอดรวม
$totalNetPrice = 0;
if (!empty($items)) {
    foreach ($items as $item) {
        $totalNetPrice += floatval($item['NetPrice'] ?? 0);
    }
}

// วันที่อัพเดทล่าสุด
$lastUpdate = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>รายงาน Sell Out - LTH</title>
<style>
* { box-sizing: border-box; }

body { 
    font-family: 'Noto Sans Lao', 'Phetsarath OT', Arial, sans-serif; 
    margin: 0;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.container { 
    max-width: 1600px; 
    margin: auto; 
    background: #fff; 
    padding: 30px; 
    border-radius: 12px; 
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); 
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

h1 { 
    color: #333; 
    border-bottom: 3px solid #667eea; 
    padding-bottom: 15px; 
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 28px;
}

.update-info {
    background: #e7f3ff;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 13px;
    color: #0c5460;
}

.error-message { 
    padding: 20px; 
    background-color: #f8d7da; 
    color: #721c24; 
    border: 1px solid #f5c6cb; 
    border-radius: 8px; 
    margin-bottom: 20px;
    font-weight: 500;
}

.summary-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.summary-item {
    text-align: center;
    padding: 10px;
}

.summary-label {
    font-size: 14px;
    opacity: 0.95;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.summary-value {
    font-size: 32px;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.filter-box { 
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px; 
    margin-bottom: 25px; 
    padding: 25px; 
    background: #f8f9fa;
    border-radius: 12px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    font-size: 13px;
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-box select,
.filter-box input {
    padding: 12px 15px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
    font-family: inherit;
}

.filter-box select:hover,
.filter-box select:focus,
.filter-box input:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.button-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 15px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: inherit;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
    transform: translateY(-2px);
}

.table-wrapper {
    overflow-x: auto;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

table { 
    width: 100%; 
    border-collapse: collapse; 
    background: white;
}

th, td { 
    border: 1px solid #dee2e6; 
    padding: 14px 16px; 
    text-align: left;
    font-size: 14px;
}

th { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

tbody tr {
    transition: all 0.2s;
}

tbody tr:hover { 
    background-color: #f1f3f5;
    transform: scale(1.01);
}

tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

tbody tr:nth-child(even):hover {
    background-color: #e9ecef;
}

td:nth-child(1) {
    text-align: center;
    font-weight: 600;
    color: #6c757d;
}

td:nth-child(7) { 
    text-align: right;
    font-weight: 700;
    color: #28a745;
    font-size: 15px;
}

.no-data {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
    font-style: italic;
    font-size: 16px;
}

.loading {
    display: none;
    text-align: center;
    padding: 40px;
}

.spinner {
    display: inline-block;
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .container { padding: 15px; }
    h1 { font-size: 20px; }
    .filter-box { grid-template-columns: 1fr; }
    .summary-box { grid-template-columns: 1fr; }
    table { font-size: 12px; }
    th, td { padding: 8px; }
    .btn { padding: 10px 16px; font-size: 13px; }
}

/* Print Styles */
@media print {
    body { background: white; padding: 0; }
    .filter-box, .button-group, .btn { display: none; }
    .container { box-shadow: none; padding: 20px; }
    .summary-box { background: #667eea; }
}

.footer {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-size: 13px;
    border-top: 1px solid #dee2e6;
    margin-top: 30px;
}
</style>
</head>
<body>

<div class="container">

<div class="header">
    <h1>
        <span>📈</span>
        <span>ລາຍງານຂໍ້ມູນ Sell Out</span>
    </h1>
    <div class="update-info">
        <strong>ອັບເດດລ່າສຸດ:</strong> <?= $lastUpdate ?><br>
        <strong>ໃຊ້ເວລາໂຫຼດ:</strong> <?= $loadTime ?> ວິນາທີ
    </div>
</div>

<?php if ($isError): ?>
    <div class="error-message">
        ⚠️ <strong>ເກີດຂໍ້ຜິດພາດ:</strong> ບໍ່ສາມາດດຶງຂໍ້ມູນການຂາຍຈາກ API ໄດ້. 
        ກະລຸນາກວດສອບການເຊື່ອມຕໍ່ ຫຼື <a href="test_api.php">ທົດສອບການເຊື່ອມຕໍ່</a>
    </div>
<?php else: ?>
    
    <!-- Summary Box -->
    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-label">📊 ລວມທັງໝົດ</div>
            <div class="summary-value" id="totalRecords"><?= number_format(count($items)) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">💰 ຍອດລວມ</div>
            <div class="summary-value">₭<?= number_format($totalNetPrice, 2) ?></div>
        </div>
        <div class="summary-item">
            <div class="summary-label">🔍 ສະຖານະ</div>
            <div class="summary-value" id="filterStatus">ທັງໝົດ</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">💵 ຍອດສະແດງ</div>
            <div class="summary-value" id="visibleTotal">₭<?= number_format($totalNetPrice, 2) ?></div>
        </div>
    </div>

    <!-- Filter Box -->
    <div class="filter-box">
        <div class="filter-group">
            <label class="filter-label">🏷️ Brand</label>
            <select id="brandFilter">
                <option value="">-- ທັງໝົດ --</option>
                <?php foreach ($brands as $br): ?>
                    <option value="<?= htmlspecialchars($br) ?>"><?= htmlspecialchars($br) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">📦 ItemType</label>
            <select id="itemTypeFilter">
                <option value="">-- ທັງໝົດ --</option>
                <?php foreach ($itemTypes as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="filter-label">👤 ລູກຄ້າ</label>
            <select id="customerFilter">
                <option value="">-- ທັງໝົດ --</option>
                <?php foreach ($customers as $cust): ?>
                    <option value="<?= htmlspecialchars($cust) ?>"><?= htmlspecialchars($cust) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="filter-label">🔍 ຄົ້ນຫາ Invoice</label>
            <input type="text" id="searchInvoice" placeholder="ໃສ່ເລກໃບບິນ...">
        </div>
    </div>
    
    <div class="button-group">
        <button class="btn btn-primary" onclick="filterTable()">
            🔍 ກັ່ນຕອງ
        </button>
        <button class="btn btn-secondary" onclick="resetFilter()">
            🔄 ລ້າງຕົວກັ່ນຕອງ
        </button>
        <button class="btn btn-success" onclick="exportToCSV()">
            📥 Export CSV
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ ພິມລາຍງານ
        </button>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table id="sellOutTable">
            <thead>
                <tr>
                    <th>ລຳດັບ</th>
                    <th>Invoice No</th>
                    <th>Invoice Date</th>
                    <th>Customer Name</th>
                    <th>ItemType ID</th>
                    <th>Description</th>
                    <th>Net Price (₭)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="no-data">
                            ບໍ່ພົບຂໍ້ມູນການຂາຍ
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $index => $row): 
                        $netPrice = floatval($row['NetPrice'] ?? 0);
                    ?>
                    <tr 
                        data-brand="<?= htmlspecialchars($row['BrandID'] ?? '') ?>"
                        data-itemtype="<?= htmlspecialchars($row['ItemTypeID'] ?? '') ?>"
                        data-customer="<?= htmlspecialchars($row['CustomerName'] ?? '') ?>"
                        data-invoice="<?= htmlspecialchars($row['InvoiceNo'] ?? '') ?>"
                        data-netprice="<?= $netPrice ?>"
                    >
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['InvoiceNo'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['InvoiceDate'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['CustomerName'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['ItemTypeID'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['Description'] ?? '') ?></td>
                        <td><?= number_format($netPrice, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <strong>LTH Retail Operations</strong> | 
        ຈຳນວນຂໍ້ມູນທັງໝົດ: <?= number_format(count($items)) ?> ລາຍການ | 
        ຊ່ວງເວລາ: <?= date("Y-m-d", strtotime("-730 days")) ?> ຫາ <?= date("Y-m-d") ?>
    </div>

<?php endif; ?>

</div>

<script>
// ฟังก์ชันกรองตาราง
function filterTable() {
    const brand = document.getElementById("brandFilter").value.toLowerCase();
    const itemType = document.getElementById("itemTypeFilter").value.toLowerCase();
    const customer = document.getElementById("customerFilter").value.toLowerCase();
    const searchInvoice = document.getElementById("searchInvoice").value.toLowerCase();
    const rows = document.querySelectorAll("#sellOutTable tbody tr");
    
    let visibleCount = 0;
    let visibleTotal = 0;
    
    rows.forEach(row => {
        if (row.querySelector('.no-data')) return;
        
        const matchBrand = (brand === "" || row.dataset.brand.toLowerCase() === brand);
        const matchItemType = (itemType === "" || row.dataset.itemtype.toLowerCase() === itemType);
        const matchCustomer = (customer === "" || row.dataset.customer.toLowerCase() === customer);
        const matchInvoice = (searchInvoice === "" || row.dataset.invoice.toLowerCase().includes(searchInvoice));
        
        const match = matchBrand && matchItemType && matchCustomer && matchInvoice;
        
        row.style.display = match ? "" : "none";
        
        if (match) {
            visibleCount++;
            visibleTotal += parseFloat(row.dataset.netprice || 0);
        }
    });
    
    // Update summary
    document.getElementById("totalRecords").textContent = visibleCount.toLocaleString();
    document.getElementById("visibleTotal").textContent = "₭" + visibleTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Update filter status
    let statusText = "ທັງໝົດ";
    if (brand !== "" || itemType !== "" || customer !== "" || searchInvoice !== "") {
        statusText = "ກັ່ນຕອງແລ້ວ";
    }
    document.getElementById("filterStatus").textContent = statusText;
}

// ฟังก์ชันรีเซ็ตฟิลเตอร์
function resetFilter() {
    document.getElementById("brandFilter").value = "";
    document.getElementById("itemTypeFilter").value = "";
    document.getElementById("customerFilter").value = "";
    document.getElementById("searchInvoice").value = "";
    filterTable();
}

// ฟังก์ชัน Export CSV
function exportToCSV() {
    const rows = document.querySelectorAll("#sellOutTable tbody tr");
    let csv = "\ufeffInvoiceNo,InvoiceDate,CustomerName,ItemTypeID,Description,NetPrice\n";
    
    rows.forEach(row => {
        if (row.style.display !== "none" && !row.querySelector('.no-data')) {
            const cols = row.querySelectorAll("td");
            if (cols.length > 1) {
                const rowData = [
                    cols[1].textContent.trim(),
                    cols[2].textContent.trim(),
                    cols[3].textContent.trim(),
                    cols[4].textContent.trim(),
                    cols[5].textContent.trim(),
                    cols[6].textContent.replace(/,/g, '').replace('₭', '').trim()
                ];
                csv += rowData.map(val => `"${val}"`).join(",") + "\n";
            }
        }
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    const filename = "LTH_SellOut_" + new Date().toISOString().slice(0,10) + ".csv";
    
    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto-filter on change
document.getElementById("brandFilter").addEventListener("change", filterTable);
document.getElementById("itemTypeFilter").addEventListener("change", filterTable);
document.getElementById("customerFilter").addEventListener("change", filterTable);
document.getElementById("searchInvoice").addEventListener("keyup", filterTable);

// Initial load message
console.log("✅ Loaded <?= number_format(count($items)) ?> records in <?= $loadTime ?> seconds");
</script>

</body>
</html>
