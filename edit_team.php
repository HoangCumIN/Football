<!-- templates/edit_team.php -->
<?php
include 'db.php';
$team_id = $_GET['team_id'];

try {
    // Lấy thông tin đội bóng
    $stmt = $conn->prepare("SELECT * FROM teams WHERE id = ?");
    $stmt->execute([$team_id]);
    $team = $stmt->fetch();

    if (!$team) {
        echo "Không tìm thấy đội bóng này!";
        exit;
    }

    // Lấy danh sách cầu thủ của đội
    $stmt = $conn->prepare("SELECT * FROM players WHERE team_id = ?");
    $stmt->execute([$team_id]);
    $players = $stmt->fetchAll();
    // Đếm số cầu thủ hiện tại của đội
    $player_count = count($players);

} catch (Exception $e) {
    echo "Có lỗi xảy ra: " . $e->getMessage();
    exit;
}
?>

<h2>Sửa đội bóng: <?php echo htmlspecialchars($team['team_name']); ?></h2>

<form action="update_team.php" method="post" onsubmit="return validateForm()">
    <input type="hidden" name="team_id" value="<?php echo $team_id; ?>">

    <label for="team_name">Tên đội:</label>
    <input type="text" id="team_name" name="team_name" value="<?php echo htmlspecialchars($team['team_name']); ?>" required>

    <label for="stadium">Sân vận động:</label>
    <input type="text" id="stadium" name="stadium" value="<?php echo htmlspecialchars($team['stadium']); ?>" required>

    <h3>Danh sách cầu thủ (<?php echo $player_count; ?> người)</h3>
    <table>
        <tr>
            <th>Tên cầu thủ</th>
            <th>Ngày sinh</th>
            <th>Loại cầu thủ (Trong nước/Ngoài nước)</th>
            <th>Chức năng</th>
        </tr>
        <?php foreach ($players as $player): ?>
        <tr>
            <td><input type="text" name="player_name[<?php echo $player['id']; ?>]" value="<?php echo htmlspecialchars($player['name']); ?>" required></td>
            <td><input type="date" name="dob[<?php echo $player['id']; ?>]" value="<?php echo $player['dob']; ?>" required></td>
            <td>
                <select name="player_type[<?php echo $player['id']; ?>]" required>
                    <option value="domestic" <?php echo $player['type'] == 'domestic' ? 'selected' : ''; ?>>Trong nước</option>
                    <option value="foreign" <?php echo $player['type'] == 'foreign' ? 'selected' : ''; ?>>Ngoài nước</option>
                </select>
            </td>
            <td>
                <!-- Chỉ cho phép xóa nếu số cầu thủ lớn hơn 15 -->
                <?php if ($player_count > 15): ?>
                <a href="delete_player.php?player_id=<?php echo $player['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa cầu thủ này?');">Xóa</a>
                <?php else: ?>
                Không thể xóa (Đội phải có ít nhất 15 cầu thủ)
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php if ($player_count < 22): ?>
    <h3>Thêm cầu thủ mới</h3>
    <table>
        <tr>
            <td><input type="text" name="new_player_name" placeholder="Tên cầu thủ mới" required></td>
            <td><input type="date" name="new_dob" required></td>
            <td>
                <select name="new_player_type">
                    <option value="domestic">Trong nước</option>
                    <option value="foreign">Ngoài nước</option>
                </select>
            </td>
        </tr>
    </table>
    <?php else: ?>
    <p>Đội bóng đã có đủ 22 cầu thủ, không thể thêm cầu thủ.</p>
    <?php endif; ?>

    <br><br>
    <input type="submit" value="Cập nhật đội bóng">
</form>

<script>
// Thêm cầu thủ mới vào form
function addPlayerRow() {
    const table = document.querySelector('table:last-of-type');
    const row = table.insertRow();
    row.innerHTML = `
        <td><input type="text" name="new_player_name[]" placeholder="Tên cầu thủ mới" required></td>
        <td><input type="date" name="new_dob[]" required></td>
        <td>
            <select name="new_player_type[]" required>
                <option value="domestic">Trong nước</option>
                <option value="foreign">Ngoài nước</option>
            </select>
        </td>
    `;
}

// Kiểm tra tuổi cầu thủ trước khi gửi form
function validateForm() {
    const dobFields = document.querySelectorAll('input[name="new_dob[]"]');
    const today = new Date();
    for (let dobField of dobFields) {
        const dob = new Date(dobField.value);
        const age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        if (age < 16 || age > 40) {
            alert("Tuổi cầu thủ phải nằm trong khoảng từ 16 đến 40!");
            return false;
        }
    }
    return true; // Cho phép gửi form nếu tất cả cầu thủ hợp lệ
}
</script>


