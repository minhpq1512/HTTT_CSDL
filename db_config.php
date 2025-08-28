    <?php
    // Thông tin kết nối cơ sở dữ liệu
    $servername = "localhost";
    $username = "minhpq"; // Tên người dùng của bạn
    $password = "1"; // Mật khẩu của bạn
    $dbname = "HTTT_CSDL"; // Tên cơ sở dữ liệu của bạn

    // Tạo kết nối
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Thiết lập mã hóa UTF-8 để hiển thị tiếng Việt đúng
    $conn->set_charset("utf8mb4");
    ?>
