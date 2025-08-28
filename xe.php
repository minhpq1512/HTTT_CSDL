<?php
include 'db_config.php';

// Xử lý thêm, sửa, xóa LOAI_XE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loai_xe_action'])) {
    if ($_POST['loai_xe_action'] == 'add') {
        $ten_loai_xe = $_POST['ten_loai_xe'];
        $so_ghe = $_POST['so_ghe'];
        $sql = "INSERT INTO LOAI_XE (TenLoaiXe, SoGhe) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $ten_loai_xe, $so_ghe);
        $stmt->execute();
    } elseif ($_POST['loai_xe_action'] == 'update') {
        $id = $_POST['loai_xe_id'];
        $ten_loai_xe = $_POST['ten_loai_xe'];
        $so_ghe = $_POST['so_ghe'];
        $sql = "UPDATE LOAI_XE SET TenLoaiXe=?, SoGhe=? WHERE LoaiXeID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $ten_loai_xe, $so_ghe, $id);
        $stmt->execute();
    } elseif ($_POST['loai_xe_action'] == 'delete') {
        $id = $_POST['loai_xe_id'];
        $sql = "DELETE FROM LOAI_XE WHERE LoaiXeID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: index.php?page=xe");
    exit();
}

// Xử lý thêm, sửa, xóa XE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['xe_action'])) {
    if ($_POST['xe_action'] == 'add') {
        $bien_so_xe = $_POST['bien_so_xe'];
        $loai_xe_id = $_POST['loai_xe_id'];
        $ngay_mua = $_POST['ngay_mua'];
        $tong_so_km = $_POST['tong_so_km'];
        $ngay_bao_duong_cuoi = $_POST['ngay_bao_duong_cuoi'];
        $han_kiem_dinh = $_POST['han_kiem_dinh'];

        $sql = "INSERT INTO XE (BienSoXe, LoaiXeID, NgayMua, TongSoKm, NgayBaoDuongCuoi, HanKiemDinh) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisiss", $bien_so_xe, $loai_xe_id, $ngay_mua, $tong_so_km, $ngay_bao_duong_cuoi, $han_kiem_dinh);
        $stmt->execute();
        header("Location: index.php?page=xe");
        exit();
    } elseif ($_POST['xe_action'] == 'update') {
        $id = $_POST['xe_id'];
        $bien_so_xe = $_POST['bien_so_xe'];
        $loai_xe_id = $_POST['loai_xe_id'];
        $ngay_mua = $_POST['ngay_mua'];
        $tong_so_km = $_POST['tong_so_km'];
        $ngay_bao_duong_cuoi = $_POST['ngay_bao_duong_cuoi'];
        $han_kiem_dinh = $_POST['han_kiem_dinh'];
        
        $sql = "UPDATE XE SET BienSoXe=?, LoaiXeID=?, NgayMua=?, TongSoKm=?, NgayBaoDuongCuoi=?, HanKiemDinh=? WHERE XeID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisissi", $bien_so_xe, $loai_xe_id, $ngay_mua, $tong_so_km, $ngay_bao_duong_cuoi, $han_kiem_dinh, $id);
        $stmt->execute();
        header("Location: index.php?page=xe");
        exit();
    } elseif (isset($_POST['delete_xe'])) {
        $id = $_POST['xe_id'];
        $sql = "DELETE FROM XE WHERE XeID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: index.php?page=xe");
        exit();
    }
}

// Lấy danh sách LOAI_XE
$sql_loai_xe = "SELECT * FROM LOAI_XE";
$result_loai_xe = $conn->query($sql_loai_xe);

// Lấy danh sách XE
$sql_xe = "SELECT XE.*, LOAI_XE.TenLoaiXe FROM XE JOIN LOAI_XE ON XE.LoaiXeID = LOAI_XE.LoaiXeID";
$result_xe = $conn->query($sql_xe);

?>

<h2 class="text-2xl font-semibold mb-4 text-gray-700">Quản lý Loại xe</h2>

<div class="bg-gray-100 p-6 rounded-lg mb-6 shadow-inner">
    <h3 class="text-xl font-medium mb-4">Thêm Loại xe mới</h3>
    <form method="POST" action="xe.php">
        <input type="hidden" name="loai_xe_action" value="add">
        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="ten_loai_xe" placeholder="Tên loại xe" class="p-2 border rounded-md" required>
            <input type="number" name="so_ghe" placeholder="Số ghế" class="p-2 border rounded-md" required>
        </div>
        <button type="submit" class="mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">Thêm Loại xe</button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-xl font-medium mb-4">Danh sách Loại xe</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên loại xe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số ghế</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while($row = $result_loai_xe->fetch_assoc()): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['TenLoaiXe']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['SoGhe']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="editLoaiXe(<?= htmlspecialchars(json_encode($row)) ?>)" class="text-indigo-600 hover:text-indigo-900">Sửa</button> |
                    <form method="POST" action="xe.php" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                        <input type="hidden" name="loai_xe_action" value="delete">
                        <input type="hidden" name="loai_xe_id" value="<?= $row['LoaiXeID'] ?>">
                        <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Sửa Loại xe -->
<div id="editLoaiXeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-xl font-medium mb-4">Sửa Loại xe</h3>
        <form id="editLoaiXeForm" method="POST" action="xe.php">
            <input type="hidden" name="loai_xe_action" value="update">
            <input type="hidden" name="loai_xe_id" id="edit-loai-xe-id">
            <input type="text" name="ten_loai_xe" id="edit-ten-loai-xe" placeholder="Tên loại xe" class="p-2 border rounded-md w-full mb-3" required>
            <input type="number" name="so_ghe" id="edit-so-ghe" placeholder="Số ghế" class="p-2 border rounded-md w-full mb-3" required>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeLoaiXeModal()" class="bg-gray-500 text-white py-2 px-4 rounded-md mr-2">Hủy</button>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    const editLoaiXeModal = document.getElementById('editLoaiXeModal');
    function editLoaiXe(loai_xe) {
        document.getElementById('edit-loai-xe-id').value = loai_xe.LoaiXeID;
        document.getElementById('edit-ten-loai-xe').value = loai_xe.TenLoaiXe;
        document.getElementById('edit-so-ghe').value = loai_xe.SoGhe;
        editLoaiXeModal.classList.remove('hidden');
    }
    function closeLoaiXeModal() {
        editLoaiXeModal.classList.add('hidden');
    }
</script>


<h2 class="text-2xl font-semibold mb-4 text-gray-700">Quản lý Xe</h2>

<div class="bg-gray-100 p-6 rounded-lg mb-6 shadow-inner">
    <h3 class="text-xl font-medium mb-4">Thêm Xe mới</h3>
    <form method="POST" action="xe.php">
        <input type="hidden" name="xe_action" value="add">
        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="bien_so_xe" placeholder="Biển số xe" class="p-2 border rounded-md" required>
            <select name="loai_xe_id" class="p-2 border rounded-md" required>
                <option value="">-- Chọn Loại Xe --</option>
                <?php 
                // Lấy lại danh sách loại xe cho dropdown
                $result_loai_xe_dropdown = $conn->query("SELECT * FROM LOAI_XE");
                while($row = $result_loai_xe_dropdown->fetch_assoc()): ?>
                    <option value="<?= $row['LoaiXeID'] ?>"><?= htmlspecialchars($row['TenLoaiXe']) ?> (<?= $row['SoGhe'] ?> ghế)</option>
                <?php endwhile; ?>
            </select>
            <input type="date" name="ngay_mua" placeholder="Ngày mua" class="p-2 border rounded-md" required>
            <input type="number" name="tong_so_km" placeholder="Tổng số km" class="p-2 border rounded-md" required>
            <input type="date" name="ngay_bao_duong_cuoi" placeholder="Ngày bảo dưỡng cuối" class="p-2 border rounded-md" required>
            <input type="date" name="han_kiem_dinh" placeholder="Hạn kiểm định" class="p-2 border rounded-md" required>
        </div>
        <button type="submit" class="mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">Thêm Xe</button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-medium mb-4">Danh sách Xe</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biển số</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại xe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày mua</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng km</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày bảo dưỡng cuối</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hạn kiểm định</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php
            if ($result_xe->num_rows > 0) {
                while($row = $result_xe->fetch_assoc()) {
                    $alert_class = '';
                    $today = new DateTime();
                    $han_kiem_dinh = new DateTime($row['HanKiemDinh']);
                    $ngay_bao_duong_cuoi = new DateTime($row['NgayBaoDuongCuoi']);
                    
                    if ($han_kiem_dinh < $today || $ngay_bao_duong_cuoi < $today) {
                        $alert_class = 'bg-red-100 text-red-800 font-semibold';
                    }

                    echo "<tr class='$alert_class'>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['BienSoXe']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['TenLoaiXe']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['NgayMua']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['TongSoKm']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['NgayBaoDuongCuoi']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($row['HanKiemDinh']) . "</td>";
                    echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>";
                    echo "<button onclick='editXe(" . json_encode($row) . ")' class='text-indigo-600 hover:text-indigo-900'>Sửa</button> | ";
                    echo "<form method='POST' action='xe.php' class='inline-block' onsubmit='return confirm(\"Bạn có chắc muốn xóa?\");'>";
                    echo "<input type='hidden' name='xe_id' value='" . $row['XeID'] . "'>";
                    echo "<input type='hidden' name='xe_action' value='delete'>";
                    echo "<button type='submit' name='delete_xe' class='text-red-600 hover:text-red-900'>Xóa</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-500'>Không có dữ liệu xe.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal để sửa thông tin -->
<div id="editXeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-xl font-medium mb-4">Sửa Xe</h3>
        <form id="editXeForm" method="POST" action="xe.php">
            <input type="hidden" name="xe_id" id="edit-xe-id">
            <input type="hidden" name="xe_action" value="update">
            <input type="text" name="bien_so_xe" id="edit-bien-so-xe" placeholder="Biển số xe" class="p-2 border rounded-md w-full mb-3" required>
            <select name="loai_xe_id" id="edit-loai-xe-id" class="p-2 border rounded-md w-full mb-3" required>
                <option value="">-- Chọn Loại Xe --</option>
                <?php 
                $result_loai_xe_dropdown_edit = $conn->query("SELECT * FROM LOAI_XE");
                while($row = $result_loai_xe_dropdown_edit->fetch_assoc()): ?>
                    <option value="<?= $row['LoaiXeID'] ?>"><?= htmlspecialchars($row['TenLoaiXe']) ?> (<?= $row['SoGhe'] ?> ghế)</option>
                <?php endwhile; ?>
            </select>
            <input type="date" name="ngay_mua" id="edit-ngay-mua" placeholder="Ngày mua" class="p-2 border rounded-md w-full mb-3" required>
            <input type="number" name="tong_so_km" id="edit-tong-so-km" placeholder="Tổng số km" class="p-2 border rounded-md w-full mb-3" required>
            <input type="date" name="ngay_bao_duong_cuoi" id="edit-ngay-bao-duong-cuoi" placeholder="Ngày bảo dưỡng cuối" class="p-2 border rounded-md w-full mb-3" required>
            <input type="date" name="han_kiem_dinh" id="edit-han-kiem-dinh" placeholder="Hạn kiểm định" class="p-2 border rounded-md w-full mb-3" required>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeXeModal()" class="bg-gray-500 text-white py-2 px-4 rounded-md mr-2">Hủy</button>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    const editXeModal = document.getElementById('editXeModal');
    
    function editXe(xe) {
        document.getElementById('edit-xe-id').value = xe.XeID;
        document.getElementById('edit-bien-so-xe').value = xe.BienSoXe;
        document.getElementById('edit-loai-xe-id').value = xe.LoaiXeID;
        document.getElementById('edit-ngay-mua').value = xe.NgayMua;
        document.getElementById('edit-tong-so-km').value = xe.TongSoKm;
        document.getElementById('edit-ngay-bao-duong-cuoi').value = xe.NgayBaoDuongCuoi;
        document.getElementById('edit-han-kiem-dinh').value = xe.HanKiemDinh;
        editXeModal.classList.remove('hidden');
    }

    function closeXeModal() {
        editXeModal.classList.add('hidden');
    }
</script>

<?php
$conn->close();
?>
