<!-- templates/register_team.php -->
<h2>Tiếp nhận hồ sơ đăng ký đội bóng</h2>
<form action="register_team.php" method="post">
    <label for="team_name">Tên đội:</label>
    <input type="text" id="team_name" name="team_name" required>

    <label for="stadium">Sân nhà:</label>
    <input type="text" id="stadium" name="stadium" required>

    <!-- Danh sách cầu thủ -->
    <h3>Danh sách cầu thủ</h3>
    <table id="players_table">
        <tr>
            <th>Tên cầu thủ</th>
            <th>Ngày sinh</th>
            <th>Loại cầu thủ (Trong nước/Ngoài nước)</th>
        </tr>
        <tr>
            <td><input type="text" name="player_name[]" required></td>
            <td><input type="date" name="dob[]" required></td>
            <td>
                <select name="player_type[]">
                    <option value="domestic">Trong nước</option>
                    <option value="foreign">Ngoài nước</option>
                </select>
            </td>
        </tr>
    </table>

    <!-- Nút thêm cầu thủ -->
    <button type="button" onclick="addPlayerRow()">Thêm cầu thủ</button>

    <input type="submit" value="Đăng ký đội bóng">

    <!-- Nút hiển thị danh sách đội bóng hiện tại -->
    <button type="button" onclick="window.location.href='view_teams.php';">Xem danh sách đội bóng</button>
</form>

<script>
// Chức năng thêm cầu thủ
function addPlayerRow() {
    const table = document.getElementById('players_table');
    const row = table.insertRow();
    row.innerHTML = `
        <td><input type="text" name="player_name[]" required></td>
        <td><input type="date" name="dob[]" required></td>
        <td>
            <select name="player_type[]">
                <option value="domestic">Trong nước</option>
                <option value="foreign">Ngoài nước</option>
            </select>
        </td>`;
}
</script>
<!-- templates/view_teams.php -->
<h2>Danh sách đội bóng đã đăng ký</h2>
<table>
    <tr>
        <th>Tên đội</th>
        <th>Sân nhà</th>
        <th>Số cầu thủ</th>
        <th>Chức năng</th>
    </tr>

    <?php
    include 'db.php';
    $stmt = $conn->query("SELECT t.id, t.team_name, t.stadium, COUNT(p.id) AS player_count 
                          FROM teams t 
                          JOIN players p ON t.id = p.team_id 
                          GROUP BY t.id");
    $teams = $stmt->fetchAll();

    foreach ($teams as $team) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($team['team_name']) . "</td>";
        echo "<td>" . htmlspecialchars($team['stadium']) . "</td>";
        echo "<td>" . htmlspecialchars($team['player_count']) . "</td>";
        echo "<td><a href='edit_team.php?team_id=" . $team['id'] . "'>Sửa</a> | <a href='delete_team.php?team_id=" . $team['id'] . "'>Xóa</a></td>";
        echo "</tr>";
    }
    ?>
</table>
<!-- includes/register_team.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_name = $_POST['team_name'];
    $stadium = $_POST['stadium'];
    $player_names = $_POST['player_name'];
    $dobs = $_POST['dob'];
    $player_types = $_POST['player_type'];

    // Kiểm tra tên đội bóng bị trùng
    $stmt = $conn->prepare("SELECT * FROM teams WHERE team_name = ?");
    $stmt->execute([$team_name]);
    $team = $stmt->fetch();

    if ($team) {
        echo "Tên đội bóng đã tồn tại! Vui lòng chọn tên đội bóng khác.";
        exit;
    }

    // Kiểm tra số lượng cầu thủ
    $player_count = count($player_names);
    if ($player_count < 15 || $player_count > 22) {
        echo "Số lượng cầu thủ không hợp lệ! Đội bóng phải có từ 15 đến 22 cầu thủ.";
        exit;
    }

    // Kiểm tra tuổi và loại cầu thủ
    $foreign_count = 0;
    foreach ($dobs as $key => $dob) {
        $age = date_diff(date_create($dob), date_create('today'))->y;
        if ($age < 16 || $age > 40) {
            echo "Cầu thủ có tuổi không hợp lệ! Tuổi cầu thủ phải từ 16 đến 40.";
            exit;
        }
        if ($player_types[$key] == 'foreign') {
            $foreign_count++;
        }
    }

    if ($foreign_count > 3) {
        echo "Số lượng cầu thủ nước ngoài không hợp lệ! Đội bóng chỉ được có tối đa 3 cầu thủ nước ngoài.";
        exit;
    }

    try {
        // Bắt đầu transaction
        $conn->beginTransaction();

        // Lưu thông tin đội bóng
        $stmt = $conn->prepare("INSERT INTO teams (team_name, stadium) VALUES (?, ?)");
        $stmt->execute([$team_name, $stadium]);

        // Lấy ID đội bóng vừa lưu
        $team_id = $conn->lastInsertId();

        // Lưu thông tin các cầu thủ
        $stmt = $conn->prepare("INSERT INTO players (team_id, name, dob, type) VALUES (?, ?, ?, ?)");
        for ($i = 0; $i < count($player_names); $i++) {
            $stmt->execute([$team_id, $player_names[$i], $dobs[$i], $player_types[$i]]);
        }

        // Commit transaction
        $conn->commit();
        echo "Đội bóng và các cầu thủ đã được đăng ký thành công!";
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollBack();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>