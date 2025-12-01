<?php
// ตรวจสอบว่าเริ่ม session แล้วหรือยัง
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// รวมไฟล์เชื่อมต่อฐานข้อมูล
include "config.php";
$current_user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
    body { font-family: 'Noto Sans Lao', Arial, sans-serif; }

    .card-scroll-container::-webkit-scrollbar { height: 8px; }
    .card-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .card-scroll-container::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .modal { display: none; }
    .modal.active { display: flex; }
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">

</head>

<body class="bg-gray-100 min-h-screen">

<div id="announcement-modal" class="modal fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-11/12 md:w-1/3">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-xl font-bold text-red-600">🚨 ປະກາດ</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                ✖
            </button>
        </div>
        <p class="text-gray-700 mb-4">
            รายละเอียดประกาศ: เราจะมีการปรับปรุงระบบครั้งใหญ่ในวันศุกร์นี้ เวลา 22:00 น. – 24:00 น.
        </p>
        <p class="text-sm text-gray-500">โพสต์โดย: Admin | วันที่: 28 พ.ย. 2568</p>
    </div>
</div>

<nav class="bg-[#156B32] shadow-md sticky top-0 z-40 border-b-4 border-[#07E34E]"> <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">

            <div class="flex items-center">
                <span class="text-2xl font-extrabold text-white">ຫນ້າ Dashboard</span>
            </div>

            <div class="flex items-center space-x-4">

                <button onclick="openModal()" class="relative text-white">
                    🔔
                    <span class="absolute top-0 right-0 w-3 h-3 bg-red-400 rounded-full animate-pulse"></span>
                </button>

                <span class="px-4 py-2 bg-green-700 rounded-full text-sm text-white">
                    <span class="font-bold">User:</span>
                    <?= htmlspecialchars($current_user) ?>
                </span>

                <a href="logout.php" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Logout
                </a>

            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto py-6">

    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-lg shadow">
        <a href="#" onclick="openModal(); return false;" class="text-red-800 font-medium">
            📢 ประกาศ: คลิกเพื่ออ่านข้อความล่าสุดจาก Admin
        </a>
    </div>

    <h3 class="text-xl font-semibold mb-3">✨ โฆษณาและโปรโมชั่น</h3>
    <div class="flex overflow-x-auto space-x-4 p-4 bg-white rounded-xl shadow-inner card-scroll-container mb-10">
        <div class="w-64 h-32 bg-yellow-400 rounded-lg shadow text-white flex items-center justify-center font-bold">
            โปรโมชั่นสิ้นปี
        </div>
        <div class="w-64 h-32 bg-pink-500 rounded-lg shadow text-white flex items-center justify-center font-bold">
            อัปเดตสินค้าใหม่
        </div>
        <div class="w-64 h-32 bg-purple-500 rounded-lg shadow text-white flex items-center justify-center font-bold">
            เทรนนิ่งรอบใหม่
        </div>
        <div class="w-64 h-32 bg-green-500 rounded-lg shadow text-white flex items-center justify-center font-bold">
            นโยบายใหม่
        </div>
    </div>

    <h3 class="text-xl font-semibold mb-4">📂 เมนูหลักสำหรับพนักงาน</h3>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">

        <a href="index_stock.php" class="bg-white p-6 rounded-xl shadow hover:shadow-lg border-t-4 border-blue-500 flex flex-col items-center">
            <svg class="w-8 h-8 text-blue-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="font-bold text-gray-700">check stock</span>
        </a>

        <a href="sellout.php" class="bg-white p-6 rounded-xl shadow hover:shadow-lg border-t-4 border-red-500 flex flex-col items-center">
            <svg class="w-8 h-8 text-red-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="font-bold text-gray-700">Sell Report</span>
        </a>

        <div class="col-span-2 md:col-span-3 bg-white p-4 rounded-xl shadow border-t-4 border-green-500">
            <h4 class="font-semibold text-gray-700 mb-3">ข้อมูลสรุปและรายงาน</h4>

            <div class="flex overflow-x-auto space-x-4 card-scroll-container">

                <div class="w-48 h-32 bg-blue-100 rounded-xl flex items-center justify-center font-semibold text-blue-700">
                    report 1
                </div>

                <div class="w-48 h-32 bg-orange-100 rounded-xl flex items-center justify-center font-semibold text-orange-700">
                    report 2
                </div>

                <div class="w-48 h-32 bg-teal-100 rounded-xl flex items-center justify-center font-semibold text-teal-700">
                    report 3
                </div>

            </div>
        </div>
    </div>

</main>

<script>
function openModal() {
    document.getElementById('announcement-modal').classList.add('active');
}
function closeModal() {
    document.getElementById('announcement-modal').classList.remove('active');
}
</script>

</body>
</html>