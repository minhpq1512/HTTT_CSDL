<?php
// Bắt đầu session nếu cần
session_start();

// Giao diện người dùng
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Lý Vận Tải</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            color: #1a202c;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .menu a {
            transition: all 0.2s ease-in-out;
            border-bottom: 2px solid transparent;
        }
        .menu a:hover {
            border-color: #4c51bf;
            color: #4c51bf;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="text-3xl font-bold text-gray-800">Hệ Thống Quản Lý Vận Tải</h1>
            <nav class="menu">
                <a href="index.php?page=tuyen_duong" class="mr-6 text-gray-600 font-medium hover:text-indigo-700">Quản lý Tuyến đường</a>
                <a href="index.php?page=xe" class="mr-6 text-gray-600 font-medium hover:text-indigo-700">Quản lý Xe</a>
                <a href="index.php?page=nhan_vien" class="text-gray-600 font-medium hover:text-indigo-700">Quản lý Nhân viên</a>
            </nav>
        </header>

        <main>
            <?php
            // Định tuyến trang
            $page = isset($_GET['page']) ? $_GET['page'] : 'tuyen_duong';
            
            switch ($page) {
                case 'tuyen_duong':
                    include 'tuyen_duong.php';
                    break;
                case 'xe':
                    include 'xe.php';
                    break;
                case 'nhan_vien':
                    include 'nhan_vien.php';
                    break;
                default:
                    echo "<h2 class='text-2xl font-semibold mb-4 text-gray-700'>Chào mừng bạn đến với hệ thống quản lý!</h2>";
                    echo "<p class='text-gray-600'>Vui lòng chọn một mục từ menu để bắt đầu.</p>";
                    break;
            }
            ?>
        </main>
    </div>
</body>
</html>
