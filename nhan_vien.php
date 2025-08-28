<?php
include 'db_config.php';

// Xử lý thêm, sửa, xóa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_nv'])) {
        $ho_ten = $_POST['ho_ten'];
        $ngay_sinh = $_POST['ngay_sinh'];
        $chuc_vu = $_POST['chuc_vu'];
        $he_so_luong_kinh_nghiem = $_POST['he_so_luong_kinh_nghiem'];

        $sql = "INSERT INTO NHAN_VIEN (HoTen, NgaySinh, ChucVu, HeSoLuongKinhNghiem) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssd", $ho_ten, $ngay_sinh, $chuc_vu, $he_so_luong_kinh_nghiem);
        $stmt->execute();
        header("Location: index.php?page=nhan_vien");
        exit();
    } elseif (isset($_POST['update_nv'])) {
        $id = $_POST['id'];
        $ho_ten = $_POST['ho_ten'];
        $ngay_sinh = $_POST['ngay_sinh'];
        $chuc_vu = $_POST['chuc_vu'];
        $he_so_luong_kinh_nghiem = $_POST['he_so_luong_kinh_nghiem'];
        
        $sql = "UPDATE NHAN_VIEN SET HoTen=?, NgaySinh=?, ChucVu=?, HeSoLuongKinhNghiem=? WHERE NhanVienID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdi", $ho_ten, $ngay_sinh, $chuc_vu, $he_so_luong_kinh_nghiem, $id);
        $stmt->execute();
        header("Location: index.php?page=nhan_vien");
        exit();
    } elseif (isset($_POST['delete_nv'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM NHAN_VIEN WHERE NhanVienID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: index.php?page=nhan_vien");
        exit();
    }
}

// Lấy danh sách nhân viên
$sql = "SELECT * FROM NHAN_VIEN";
$result = $conn->query($sql);

?>

<h2 class="text-2xl font-semibold mb-4 text-gray-700">Quản lý Nhân viên</h2>

<div class="bg-gray-100 p-6 rounded-lg mb-6 shadow-inner">
    <h3 class="text-xl font-medium mb-4">Thêm Nhân viên mới</h3>
    <form method="POST" action="nhan_vien.php">
        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="ho_ten" placeholder="Họ tên" class="p-2 border rounded-md" required>
            <input type="date" name="ngay_sinh" placeholder="Ngày sinh" class="p-2 border rounded-md">
            <input type="text" name="chuc_vu" placeholder="Chức vụ" class="p-2 border rounded-md" required>
            <input type="number" name="he_so_luong_kinh_nghiem" placeholder="Hệ số lương/kinh nghiệm" step="0.01" class="p-2 border rounded-md" required>
        </div>
        <button type="submit" name="add_nv" class="mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">Thêm Nhân viên</button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-medium mb-4">Danh sách Nhân viên</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ tên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày sinh</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chức vụ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hệ số lương</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['HoTen']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['NgaySinh']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['ChucVu']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['HeSoLuongKinhNghiem']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>";
                    echo "<button onclick='editNV(" . json_encode($row) . ")' class='text-indigo-600 hover:text-indigo-900'>Sửa</button> | ";
                    echo "<form method='POST' action='nhan_vien.php' class='inline-block' onsubmit='return confirm(\"Bạn có chắc muốn xóa?\");'>";
                    echo "<input type='hidden' name='id' value='" . $row['NhanVienID'] . "'>";
                    echo "<button type='submit' name='delete_nv' class='text-red-600 hover:text-red-900'>Xóa</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='px-6 py-4 text-center text-gray-500'>Không có dữ liệu nhân viên.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal để sửa thông tin -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-xl font-medium mb-4">Sửa Nhân viên</h3>
        <form id="editForm" method="POST" action="nhan_vien.php">
            <input type="hidden" name="id" id="edit-id">
            <input type="text" name="ho_ten" id="edit-ho-ten" placeholder="Họ tên" class="p-2 border rounded-md w-full mb-3" required>
            <input type="date" name="ngay_sinh" id="edit-ngay-sinh" placeholder="Ngày sinh" class="p-2 border rounded-md w-full mb-3">
            <input type="text" name="chuc_vu" id="edit-chuc-vu" placeholder="Chức vụ" class="p-2 border rounded-md w-full mb-3" required>
            <input type="number" name="he_so_luong_kinh_nghiem" id="edit-he-so-luong-kinh-nghiem" placeholder="Hệ số lương/kinh nghiệm" step="0.01" class="p-2 border rounded-md w-full mb-3" required>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeModal()" class="bg-gray-500 text-white py-2 px-4 rounded-md mr-2">Hủy</button>
                <button type="submit" name="update_nv" class="bg-blue-600 text-white py-2 px-4 rounded-md">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    const editModal = document.getElementById('editModal');
    
    function editNV(nv) {
        document.getElementById('edit-id').value = nv.NhanVienID;
        document.getElementById('edit-ho-ten').value = nv.HoTen;
        document.getElementById('edit-ngay-sinh').value = nv.NgaySinh;
        document.getElementById('edit-chuc-vu').value = nv.ChucVu;
        document.getElementById('edit-he-so-luong-kinh-nghiem').value = nv.HeSoLuongKinhNghiem;
        editModal.classList.remove('hidden');
    }

    function closeModal() {
        editModal.classList.add('hidden');
    }
</script>

<?php
$conn->close();
?>
s