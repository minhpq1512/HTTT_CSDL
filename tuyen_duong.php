<?php
include 'db_config.php';

// Xử lý thêm, sửa, xóa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_tuyen'])) {
        $diem_di = $_POST['diem_di'];
        $diem_den = $_POST['diem_den'];
        $do_dai = $_POST['do_dai'];
        $he_so_phuc_tap = $_POST['he_so_phuc_tap'];
        $gia_ve = $_POST['gia_ve'];

        // Thêm vào bảng TUYEN_DUONG
        $sql_tuyen = "INSERT INTO TUYEN_DUONG (DiemDi, DiemDen, DoDai, HeSoPhucTap) VALUES (?, ?, ?, ?)";
        $stmt_tuyen = $conn->prepare($sql_tuyen);
        $stmt_tuyen->bind_param("sssd", $diem_di, $diem_den, $do_dai, $he_so_phuc_tap);
        $stmt_tuyen->execute();
        $tuyen_duong_id = $stmt_tuyen->insert_id;

        // Thêm vào bảng BANG_GIA
        $sql_gia = "INSERT INTO BANG_GIA (TuyenDuongID, GiaVe) VALUES (?, ?)";
        $stmt_gia = $conn->prepare($sql_gia);
        $stmt_gia->bind_param("id", $tuyen_duong_id, $gia_ve);
        $stmt_gia->execute();

        header("Location: index.php?page=tuyen_duong");
        exit();

    } elseif (isset($_POST['update_tuyen'])) {
        $id = $_POST['id'];
        $diem_di = $_POST['diem_di'];
        $diem_den = $_POST['diem_den'];
        $do_dai = $_POST['do_dai'];
        $he_so_phuc_tap = $_POST['he_so_phuc_tap'];
        $gia_ve = $_POST['gia_ve'];
        
        // Cập nhật bảng TUYEN_DUONG
        $sql_tuyen = "UPDATE TUYEN_DUONG SET DiemDi=?, DiemDen=?, DoDai=?, HeSoPhucTap=? WHERE TuyenDuongID=?";
        $stmt_tuyen = $conn->prepare($sql_tuyen);
        $stmt_tuyen->bind_param("sssdi", $diem_di, $diem_den, $do_dai, $he_so_phuc_tap, $id);
        $stmt_tuyen->execute();

        // Cập nhật bảng BANG_GIA (Giả định 1 tuyến chỉ có 1 giá hiện tại)
        $sql_gia = "UPDATE BANG_GIA SET GiaVe=? WHERE TuyenDuongID=?";
        $stmt_gia = $conn->prepare($sql_gia);
        $stmt_gia->bind_param("di", $gia_ve, $id);
        $stmt_gia->execute();

        header("Location: index.php?page=tuyen_duong");
        exit();
    } elseif (isset($_POST['delete_tuyen'])) {
        $id = $_POST['id'];

        // Xóa các bản ghi liên quan trong BANG_GIA trước
        $sql_delete_gia = "DELETE FROM BANG_GIA WHERE TuyenDuongID=?";
        $stmt_delete_gia = $conn->prepare($sql_delete_gia);
        $stmt_delete_gia->bind_param("i", $id);
        $stmt_delete_gia->execute();

        // Sau đó xóa tuyến đường
        $sql_delete_tuyen = "DELETE FROM TUYEN_DUONG WHERE TuyenDuongID=?";
        $stmt_delete_tuyen = $conn->prepare($sql_delete_tuyen);
        $stmt_delete_tuyen->bind_param("i", $id);
        $stmt_delete_tuyen->execute();

        header("Location: index.php?page=tuyen_duong");
        exit();
    }
}

// Lấy danh sách tuyến đường và giá vé tương ứng
$sql = "SELECT td.*, bg.GiaVe FROM TUYEN_DUONG td LEFT JOIN BANG_GIA bg ON td.TuyenDuongID = bg.TuyenDuongID";
$result = $conn->query($sql);

?>

<h2 class="text-2xl font-semibold mb-4 text-gray-700">Quản lý Tuyến đường & Giá vé</h2>

<div class="bg-gray-100 p-6 rounded-lg mb-6 shadow-inner">
    <h3 class="text-xl font-medium mb-4">Thêm Tuyến đường mới</h3>
    <form method="POST" action="tuyen_duong.php">
        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="diem_di" placeholder="Điểm đi" class="p-2 border rounded-md" required>
            <input type="text" name="diem_den" placeholder="Điểm đến" class="p-2 border rounded-md" required>
            <input type="number" name="do_dai" placeholder="Độ dài (km)" class="p-2 border rounded-md" required>
            <input type="number" name="he_so_phuc_tap" placeholder="Hệ số phức tạp" step="0.01" class="p-2 border rounded-md" required>
            <input type="number" name="gia_ve" placeholder="Giá vé (VND)" step="1000" class="p-2 border rounded-md" required>
        </div>
        <button type="submit" name="add_tuyen" class="mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">Thêm Tuyến</button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-medium mb-4">Danh sách Tuyến đường</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Điểm đi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Điểm đến</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Độ dài (km)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hệ số phức tạp</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá vé (VND)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['DiemDi']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['DiemDen']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['DoDai']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['HeSoPhucTap']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . number_format($row['GiaVe'], 0, '.', ',') . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>";
                    echo "<button onclick='editTuyen(" . json_encode($row) . ")' class='text-indigo-600 hover:text-indigo-900'>Sửa</button> | ";
                    echo "<form method='POST' action='tuyen_duong.php' class='inline-block' onsubmit='return confirm(\"Bạn có chắc muốn xóa?\");'>";
                    echo "<input type='hidden' name='id' value='" . $row['TuyenDuongID'] . "'>";
                    echo "<button type='submit' name='delete_tuyen' class='text-red-600 hover:text-red-900'>Xóa</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-500'>Không có dữ liệu tuyến đường.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal để sửa thông tin -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-xl font-medium mb-4">Sửa Tuyến đường</h3>
        <form id="editForm" method="POST" action="tuyen_duong.php">
            <input type="hidden" name="id" id="edit-id">
            <input type="text" name="diem_di" id="edit-diem_di" placeholder="Điểm đi" class="p-2 border rounded-md w-full mb-3" required>
            <input type="text" name="diem_den" id="edit-diem_den" placeholder="Điểm đến" class="p-2 border rounded-md w-full mb-3" required>
            <input type="number" name="do_dai" id="edit-do_dai" placeholder="Độ dài (km)" class="p-2 border rounded-md w-full mb-3" required>
            <input type="number" name="he_so_phuc_tap" id="edit-he_so_phuc_tap" placeholder="Hệ số phức tạp" step="0.01" class="p-2 border rounded-md w-full mb-3" required>
            <input type="number" name="gia_ve" id="edit-gia_ve" placeholder="Giá vé (VND)" step="1000" class="p-2 border rounded-md w-full mb-3" required>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeModal()" class="bg-gray-500 text-white py-2 px-4 rounded-md mr-2">Hủy</button>
                <button type="submit" name="update_tuyen" class="bg-blue-600 text-white py-2 px-4 rounded-md">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    const editModal = document.getElementById('editModal');
    
    function editTuyen(tuyen) {
        document.getElementById('edit-id').value = tuyen.TuyenDuongID;
        document.getElementById('edit-diem_di').value = tuyen.DiemDi;
        document.getElementById('edit-diem_den').value = tuyen.DiemDen;
        document.getElementById('edit-do_dai').value = tuyen.DoDai;
        document.getElementById('edit-he_so_phuc_tap').value = tuyen.HeSoPhucTap;
        document.getElementById('edit-gia_ve').value = tuyen.GiaVe;
        editModal.classList.remove('hidden');
    }

    function closeModal() {
        editModal.classList.add('hidden');
    }
</script>

<?php
$conn->close();
?>
