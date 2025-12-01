<?php
// ไฟล์: test_api.php
require_once 'api_sellout.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$testResult = testAPIConnection();
?>
<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ທົດສອບການເຊື່ອມຕໍ່ API - Debug Mode</title>
<style>
body {
    font-family: 'Noto Sans Lao', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    margin: 0;
}
.container {
    max-width: 1200px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}
h1, h2, h3 {
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}
.test-result {
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    font-size: 14px;
    line-height: 1.8;
    white-space: pre-line;
    font-family: monospace;
}
.success { background: #d4edda; border: 2px solid #28a745; color: #155724; }
.error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; }
.warning { background: #fff3cd; border: 2px solid #ffc107; color: #856404; }
.info { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; }
.btn {
    display: inline-block;
    padding: 12px 24px;
    background: #667eea;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    margin: 5px;
    transition: all 0.3s;
}
.btn:hover { background: #5568d3; transform: translateY(-2px); }
.debug-section {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin: 15px 0;
    max-height: 400px;
    overflow-y: auto;
}
.debug-title {
    font-weight: bold;
    color: #667eea;
    margin-bottom: 10px;
}
pre {
    background: #272822;
    color: #f8f8f2;
    padding: 15px;
    border-radius: 6px;
    overflow-x: auto;
    font-size: 12px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}
th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #dee2e6;
}
th {
    background: #667eea;
    color: white;
}
.status-ok { color: #28a745; font-weight: bold; }
.status-fail { color: #dc3545; font-weight: bold; }
.status-warning { color: #ffc107; font-weight: bold; }
</style>
</head>
<body>

<div class="container">
    <h1>🔧 ທົດສອບການເຊື່ອມຕໍ່ API - Debug Mode</h1>
    
    <h2>📊 ສະຫຼຸບຜົນການທົດສອບ</h2>
    <table>
        <tr>
            <th>ການທົດສອບ</th>
            <th>ສະຖານະ</th>
            <th>ລາຍລະອຽດ</th>
        </tr>
        <tr>
            <td>1. Login API</td>
            <td class="<?= $testResult['login'] ? 'status-ok' : 'status-fail' ?>">
                <?= $testResult['login'] ? '✅ ສຳເລັດ' : '❌ ລົ້ມເຫລວ' ?>
            </td>
            <td><?= $testResult['token'] ?? '-' ?></td>
        </tr>
        <tr>
            <td>2. Get Sell Out Data</td>
            <td class="<?= $testResult['getSell'] ? 'status-ok' : ($testResult['login'] ? 'status-warning' : 'status-fail') ?>">
                <?= $testResult['getSell'] ? '✅ ສຳເລັດ' : ($testResult['login'] ? '⚠️ ຕ້ອງກວດສອບ' : '❌ ລົ້ມເຫລວ') ?>
            </td>
            <td>ກວດສອບລາຍລະອຽດດ້ານລຸ່ມ</td>
        </tr>
    </table>
    
    <div class="test-result <?= $testResult['login'] ? ($testResult['getSell'] ? 'success' : 'warning') : 'error' ?>">
        <?= htmlspecialchars($testResult['message']) ?>
    </div>
    
    <?php if ($testResult['login']): ?>
        <h2>🔍 ລາຍລະອຽດການທົດສອບແຕ່ລະ Format</h2>
        
        <?php foreach ($testResult['debug'] as $formatName => $debugInfo): ?>
            <?php if (strpos($formatName, 'format_') === 0): ?>
                <div class="debug-section">
                    <div class="debug-title">
                        📋 <?= strtoupper($formatName) ?>: 
                        <?php if ($debugInfo['success']): ?>
                            <?php if ($debugInfo['hasData']): ?>
                                <span class="status-ok">✅ ມີຂໍ້ມູນ <?= $debugInfo['dataCount'] ?> ລາຍການ</span>
                            <?php else: ?>
                                <span class="status-warning">⚠️ Response ສຳເລັດແຕ່ບໍ່ມີຂໍ້ມູນ</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="status-fail">❌ <?= htmlspecialchars($debugInfo['error'] ?? 'Unknown error') ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($debugInfo['responseKeys'])): ?>
                        <p><strong>Response Keys:</strong> <?= implode(", ", $debugInfo['responseKeys']) ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($debugInfo['sampleResponse'])): ?>
                        <details>
                            <summary style="cursor: pointer; color: #667eea; font-weight: bold;">👁️ ເບິ່ງ Sample Response</summary>
                            <pre><?= htmlspecialchars($debugInfo['sampleResponse']) ?></pre>
                        </details>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <?php if (!empty($testResult['debug']['loginResponse'])): ?>
            <h2>🔐 Login Response</h2>
            <div class="debug-section">
                <pre><?= htmlspecialchars(json_encode($testResult['debug']['loginResponse'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <hr style="margin: 30px 0;">
    
    <h3>💡 แนะนำการแก้ไข</h3>
    <div class="info test-result">
        <?php if ($testResult['getSell']): ?>
            ✅ <strong>ສຳເລັດ!</strong> ສາມາດໄປໃຊ້ງານຫນ້າລາຍງານໄດ້<br>
            ກະລຸນາຈົດຈຳ Format ທີ່ເຮັດວຽກໄດ້ແລະແຈ້ງໃຫ້ຂ້ອຍຮູ້ເພື່ອອັບເດດ sellout.php
        <?php elseif ($testResult['login']): ?>
            ⚠️ <strong>Login ສຳເລັດແຕ່ດຶງຂໍ້ມູນບໍ່ໄດ້</strong><br><br>
            ກະລຸນາກວດສອບ:<br>
            1. ເບິ່ງ Response Keys ຂອງແຕ່ລະ Format ຂ້າງເທິງ<br>
            2. ກວດສອບວ່າ API ຕ້ອງການ Field ອື່ນເພີ່ມເຕີມຫຼືບໍ່<br>
            3. ລອງດູ Sample Response ເບິ່ງວ່າມີ Error Message ບໍ່<br>
            4. ຖ້າ Response Keys ບໍ່ມີ 'datas' ໃຫ້ແຈ້ງຂ້ອຍຮູ້ວ່າມີ key ອັນໃດແທນ
        <?php else: ?>
            ❌ <strong>Login ລົ້ມເຫລວ</strong><br><br>
            ກະລຸນາກວດສອບ:<br>
            1. Username ແລະ Password ຖືກຕ້ອງຫຼືບໍ່<br>
            2. Server ສາມາດເຂົ້າເຖິງ lth.com.la ໄດ້ຫຼືບໍ່<br>
            3. PHP Extension: curl, json ຕິດຕັ້ງແລ້ວຫຼືຍັງ
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 30px;">
        <button onclick="location.reload()" class="btn" style="background: #6c757d;">
            🔄 ທົດສອບໃໝ່
        </button>
        
        <?php if ($testResult['getSell']): ?>
            <a href="sellout.php" class="btn">📊 ໄປຫນ້າລາຍງານ</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>