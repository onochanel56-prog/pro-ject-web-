<?php
session_start();
require 'config.php';


$error = "";

// ดึง ID ของโฆษณาที่จะแก้ไข
$ad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$result = $conn->query("SELECT * FROM advertisements WHERE ad_id = $ad_id");
$ad = $result->fetch_assoc();

if (!$ad) {
    die("ไม่พบโฆษณานี้");
}

if (isset($_POST['submit'])) {
    $ad_text = $_POST['ad_text'];
    $bg_color = $_POST['bg_color'];
    $target_url = $_POST['target_url'] ?: NULL;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE advertisements SET ad_text=?, target_url=?, bg_color=?, is_active=? WHERE ad_id=?");
    $stmt->bind_param("sssii", $ad_text, $target_url, $bg_color, $is_active, $ad_id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        $error = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขโฆษณา</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
        input, label { display:block; margin: 10px 0; }
        input[type="submit"] { padding: 6px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color:red; margin-bottom:10px; }
    </style>
</head>
<body>
<h2>✏️ แก้ไขโฆษณา</h2>
<?php if($error) echo "<div class='error'>$error</div>"; ?>
<form method="post">
    <label>ข้อความโฆษณา</label>
    <input type="text" name="ad_text" value="<?= htmlspecialchars($ad['ad_text']) ?>" required>

    <label>สีพื้นหลัง (เช่น #FFC107)</label>
    <input type="text" name="bg_color" value="<?= htmlspecialchars($ad['bg_color']) ?>" required>

    <label>ลิงก์ (URL)</label>
    <input type="text" name="target_url" value="<?= htmlspecialchars($ad['target_url']) ?>">

    <label>
        <input type="checkbox" name="is_active" <?= $ad['is_active'] ? 'checked' : '' ?>> แสดงโฆษณา
    </label>

    <input type="submit" name="submit" value="บันทึก">
</form>

<a href="admin.php">⬅ กลับ</a>
</body>
</html>
