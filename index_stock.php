<?php
include "api.php";

// ດຶງຂໍ້ມູນທັງໝົດຈາກ API
$data = getStockFromAPI();
$items = $data["datas"] ?? [];

// ** ການແກ້ໄຂ PHP: ກັ່ນຕອງລາຍການທີ່ມີ LocationID ເປັນ "Loc-Data Problem" **
$filteredItems = [];
foreach ($items as $item) {
    if (($item['LocationID'] ?? '') !== 'Loc-Data Problem') {
        $filteredItems[] = $item;
    }
}
$items = $filteredItems;

// ** ການແກ້ໄຂ PHP: Sort ຂໍ້ມູນຕາມ StockQty ຈາກຫຼາຍໄປຫານ້ອຍ **
usort($items, function($a, $b) {
    return (int)$b['StockQty'] - (int)$a['StockQty'];
});

// ແປງຂໍ້ມູນເປັນ JSON ສຳລັບ JavaScript
$jsonItems = json_encode($items);

// ຕຽມ Dropdown
$locations = array_unique(array_column($items, "LocationID"));
$brands = array_unique(array_column($items, "BrandID"));
$itemtypes = array_unique(array_column($items, "ItemTypeID"));
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Stock LTH</title>
<link rel="stylesheet" href="style.css">
<style>
table th, table td { text-align: center; }
.lowstock { color: red; font-weight: bold; }
mark { background-color: yellow; color: black; padding: 0; }
.action-btn {
    color: white;
    border: none;
    padding: 8px 15px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    margin: 0 5px;
    cursor: pointer;
    border-radius: 4px;
}
#downloadBtn { background-color: #4CAF50; }
#refreshBtn { background-color: #008CBA; }
.total-sum {
    text-align: center;
    font-size: 1.2em;
    font-weight: bold;
    margin-bottom: 15px;
    padding: 10px;
    background-color: #f0f0f0;
    border-radius: 5px;
    color: #333;
}
</style>
</head>
<body>

<h1>ຫນ້າຄົ້ນຫາສິນຄ້າ (Stock) 🔍</h1>

<input type="text" id="descInput" class="search" placeholder="ພິມ Description ...">

<div class="filter-box">
    <input list="listLocation" id="locationFilter" class="select-search" placeholder="-- LocationID --">
    <datalist id="listLocation">
        <?php foreach ($locations as $loc): ?>
            <option value="<?= htmlspecialchars($loc) ?>"></option>
        <?php endforeach; ?>
    </datalist>

    <input list="listBrand" id="brandFilter" class="select-search" placeholder="-- BrandID --">
    <datalist id="listBrand">
        <?php foreach ($brands as $br): ?>
            <option value="<?= htmlspecialchars($br) ?>"></option>
        <?php endforeach; ?>
    </datalist>

    <input list="listType" id="typeFilter" class="select-search" placeholder="-- ItemTypeID --">
    <datalist id="listType">
        <?php foreach ($itemtypes as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>"></option>
        <?php endforeach; ?>
    </datalist>
</div>

<div style="text-align:center; margin-bottom:10px;">
    <button id="refreshBtn" class="action-btn">🔄 ເລີ່ມໃຫມ່</button>
    <button id="downloadBtn" class="action-btn">⬇️ Download Excel</button>
</div>

<div class="total-sum">
    ຈຳນວນໃນຄັງທີ່ກັ່ນຕອງ: <span id="currentTotal" style="color: #c00000;"></span>
</div>

<table id="stockTable">
    <thead>
        <tr>
            <th>ລະຫັດສາຂາ</th>
            <th>Code ສິນຄ້າ</th>
            <th>ຊະນິດສິນຄ້າ</th>
            <th>ຄຳອະທິບາຍ</th>
            <th>ຈຳນວນໃນຄັງ</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $row): ?>
        <tr 
            data-location="<?= htmlspecialchars($row['LocationID']) ?>"
            data-brand="<?= htmlspecialchars($row['BrandID']) ?>"
            data-type="<?= htmlspecialchars($row['ItemTypeID']) ?>"
            data-stockqty="<?= htmlspecialchars($row['StockQty']) ?>" >
            <td><?= htmlspecialchars($row['LocationID']) ?></td>
            <td><?= htmlspecialchars($row['ItemCode']) ?></td>
            <td><?= htmlspecialchars($row['ItemTypeID']) ?></td>
            <td><?= htmlspecialchars($row['Description']) ?></td>
            <td class="<?= ($row['StockQty'] < 1 ? 'lowstock' : '') ?>"><?= htmlspecialchars($row['StockQty']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
let items = <?= $jsonItems ?>;
const totalElement = document.getElementById('currentTotal');

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function applyFilters() {
    let loc = document.getElementById('locationFilter').value.toLowerCase().trim();
    let brand = document.getElementById('brandFilter').value.toLowerCase().trim();
    let type = document.getElementById('typeFilter').value.toLowerCase().trim();
    let keyword = document.getElementById('descInput').value.toLowerCase().trim();

    let rows = document.querySelectorAll('#stockTable tbody tr');
    let currentTotalSum = 0;

    rows.forEach(row => {
        let rowLocation = row.dataset.location.toLowerCase();
        let rowBrand = row.dataset.brand.toLowerCase();
        let rowType = row.dataset.type.toLowerCase(); 
        let rowStockQty = parseInt(row.dataset.stockqty) || 0;

        let descCell = row.children[3];
        let descText = descCell.getAttribute('data-original') || descCell.textContent;
        if (!descCell.getAttribute('data-original')) descCell.setAttribute('data-original', descText);

        // ✅ ItemTypeID ตรงคำเป๊ะ
        let passType = true;
        if (type !== '') {
            passType = (rowType === type);
        }

        let passDropdown = (loc === '' || rowLocation.includes(loc)) &&
                           (brand === '' || rowBrand.includes(brand)) &&
                           passType;

        let passDesc = keyword === '' || descText.toLowerCase().includes(keyword);

        if (passDropdown && passDesc) {
            row.style.display = '';
            currentTotalSum += rowStockQty;
            
            if(keyword !== '') {
                let safe = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); 
                let re = new RegExp('(' + safe + ')', 'ig');
                descCell.innerHTML = descText.replace(re, '<mark>$1</mark>');
            } else {
                descCell.innerHTML = descText;
            }
        } else {
            row.style.display = 'none';
        }
    });

    totalElement.textContent = formatNumber(currentTotalSum);
}

function downloadTableAsCSV() {
    let table = document.getElementById('stockTable');
    let csv = [];
    
    let headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    csv.push(headers.join(','));

    let rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            let rowData = [];
            Array.from(row.querySelectorAll('td')).forEach(cell => {
                let data = cell.textContent.trim();
                data = data.replace(/"/g, '""');
                if (data.includes(',') || data.includes('\n')) {
                    data = `"${data}"`;
                }
                rowData.push(data);
            });
            csv.push(rowData.join(','));
        }
    });

    let BOM = "\ufeff"; 
    let blob = new Blob([BOM + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    let link = document.createElement("a");
    let url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", "Stock_Export_" + new Date().toISOString().slice(0, 10) + ".csv");
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

['descInput','locationFilter','brandFilter','typeFilter'].forEach(id => {
    document.getElementById(id).addEventListener('input', applyFilters);
});

document.getElementById('refreshBtn').addEventListener('click', function(){
    location.reload(true);
});

document.getElementById('downloadBtn').addEventListener('click', downloadTableAsCSV);

applyFilters();
</script>

</body>
</html>