<!-- templates/edit_result.php -->
<?php
include 'db.php';
$match_id = $_GET['match_id'];

try {
    // Lấy thông tin kết quả trận đấu
    $stmt = $conn->prepare("SELECT * FROM matches WHERE id = ?");
    $stmt->execute([$match_id]);
    $match = $stmt->fetch();

    if (!$match) {
        echo "Không tìm thấy kết quả trận đấu!";
        exit;
    }

    // Lấy thông tin cầu thủ ghi bàn
    $stmt = $conn->prepare("SELECT * FROM goals WHERE match_id = ?");
    $stmt->execute([$match_id]);
    $goals = $stmt->fetchAll();

    // Lấy danh sách đội bóng
    $stmt = $conn->query("SELECT id, team_name FROM teams");
    $teams = $stmt->fetchAll();
} catch (Exception $e) {
    echo "Có lỗi xảy ra: " . $e->getMessage();
    exit;
}
?>

<h2>Sửa kết quả trận đấu</h2>
<form action="update_result.php" method="post">
    <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">

    <label for="team1">Đội 1:</label>
    <input type="text" id="team1" name="team1" value="<?php echo htmlspecialchars($match['team1']); ?>" readonly>

    <label for="team2">Đội 2:</label>
    <input type="text" id="team2" name="team2" value="<?php echo htmlspecialchars($match['team2']); ?>" readonly>

    <label for="score">Tỷ số:</label>
    <input type="text" id="score" name="score" value="<?php echo $match['score']; ?>" required>

    <!-- Hiển thị cầu thủ đã ghi bàn -->
    <h3>Các cầu thủ đã ghi bàn</h3>
    <table>
        <tr>
            <th>Tên cầu thủ</th>
            <th>Đội bóng</th>
            <th>Loại bàn thắng (A/B/C)</th>
            <th>Thời điểm (phút)</th>
            <th>Chức năng</th>
        </tr>
        <?php foreach ($goals as $goal): ?>
        <tr>
            <td><input type="text" name="player_name[<?php echo $goal['id']; ?>]" value="<?php echo htmlspecialchars($goal['player_name']); ?>" required></td>
            <td>
    <?php
    $team_name = '';
    foreach ($teams as $team) {
        if ($team['id'] == $goal['team']) {
            $team_name = htmlspecialchars($team['team_name']);
            break;
        }
    }
    ?>
    <input type="text" name="team[<?php echo $goal['id']; ?>]" value="<?php echo $team_name; ?>" readonly>
</td>
            <td>
                <select name="goal_type[<?php echo $goal['id']; ?>]" required>
                    <option value="A" <?php echo $goal['goal_type'] == 'A' ? 'selected' : ''; ?>>A</option>
                    <option value="B" <?php echo $goal['goal_type'] == 'B' ? 'selected' : ''; ?>>B</option>
                    <option value="C" <?php echo $goal['goal_type'] == 'C' ? 'selected' : ''; ?>>C</option>
                </select>
            </td>
            <td><input type="number" name="goal_time[<?php echo $goal['id']; ?>]" value="<?php echo $goal['goal_time']; ?>" min="0" max="90" required></td>
            <td><a href="delete_goal.php?goal_id=<?php echo $goal['id']; ?>&match_id=<?php echo $match_id; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bàn thắng này?');">Xóa</a></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Thêm phần thêm cầu thủ ghi bàn -->
    <h3>Thêm cầu thủ ghi bàn mới</h3>
    <table id="goals_table">
        <tr>
            <th>Tên cầu thủ</th>
            <th>Đội bóng</th>
            <th>Loại bàn thắng (A/B/C)</th>
            <th>Thời điểm (phút)</th>
        </tr>
        <tr>
            <td><input type="text" name="new_player_name[]" placeholder="Tên cầu thủ mới"></td>
            <td>
                <select name="new_team[]">
                    <?php foreach ($teams as $team): ?>
                    <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <select name="new_goal_type[]">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </td>
            <td><input type="number" name="new_goal_time[]" min="0" max="90"></td>
        </tr>
    </table>

    <button type="button" onclick="addGoalRow()">Thêm cầu thủ ghi bàn</button>

    <br><br>
    <input type="submit" value="Cập nhật kết quả">
</form>

<script>
// Hàm thêm cầu thủ ghi bàn mới vào bảng
function addGoalRow() {
    const table = document.getElementById('goals_table');
    const row = table.insertRow();
    row.innerHTML = `
        <td><input type="text" name="new_player_name[]" placeholder="Tên cầu thủ mới"></td>
        <td>
            <select name="new_team[]">
                <?php foreach ($teams as $team): ?>
                <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="new_goal_type[]">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
            </select>
        </td>
        <td><input type="number" name="new_goal_time[]" min="0" max="90"></td>
    `;
}
</script>
