<!-- templates/match_result.php -->
<h2>Ghi nhận kết quả trận đấu</h2>
<form action="match_result.php" method="post">
    <label for="team1">Đội 1:</label>
    <select id="team1" name="team1">
        <!-- Lấy danh sách các đội bóng từ cơ sở dữ liệu -->
        <?php
        include 'db.php';
        $stmt = $conn->query("SELECT id, team_name FROM teams");
        $teams = $stmt->fetchAll();
        foreach ($teams as $team) {
            echo "<option value='" . $team['team_name'] . "'>" . $team['team_name'] . "</option>";
        }
        ?>
    </select>

    <label for="team2">Đội 2:</label>
    <select id="team2" name="team2">
        <?php
        foreach ($teams as $team) {
            echo "<option value='" . $team['team_name'] . "'>" . $team['team_name'] . "</option>";
        }
        ?>
    </select>

    <label for="score">Tỷ số:</label>
    <input type="text" id="score" name="score" placeholder="VD: 2-1" required>

    <label for="stadium">Sân thi đấu:</label>
    <input type="text" id="stadium" name="stadium" required>

    <label for="date">Ngày thi đấu:</label>
    <input type="date" id="date" name="date" required>

    <label for="time">Giờ thi đấu:</label>
    <input type="time" id="time" name="time" required>

    <!-- Cầu thủ ghi bàn -->
    <h3>Chi tiết cầu thủ ghi bàn</h3>
    <table id="goal_table">
        <tr>
            <th>Tên cầu thủ</th>
            <th>Đội</th>
            <th>Loại bàn thắng (A/B/C)</th>
            <th>Thời điểm (phút)</th>
        </tr>
        <tr>
            <td><input type="text" name="player_name[]" required></td>
            <td>
                <select name="team[]">
                    <?php
                    foreach ($teams as $team) {
                        echo "<option value='" . $team['team_name'] . "'>" . $team['team_name'] . "</option>";
                    }
                    ?>
                </select>
            </td>
            <td>
                <select name="goal_type[]">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </td>
            <td><input type="number" name="goal_time[]" min="0" max="90" required></td>
        </tr>
    </table>

    <!-- Nút thêm cầu thủ ghi bàn -->
    <button type="button" onclick="addGoalRow()">Thêm cầu thủ ghi bàn</button>

    <input type="submit" value="Ghi nhận kết quả">
</form>

<script>
function addGoalRow() {
    const table = document.getElementById('goal_table');
    const row = table.insertRow();
    row.innerHTML = `
        <td><input type="text" name="player_name[]" required></td>
        <td>
            <select name="team[]">
                <?php foreach ($teams as $team) {
                    echo "<option value='" . $team['team_name'] . "'>" . $team['team_name'] . "</option>";
                } ?>
            </select>
        </td>
        <td>
            <select name="goal_type[]">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
            </select>
        </td>
        <td><input type="number" name="goal_time[]" min="0" max="90" required></td>`;
}
</script>
<!-- includes/match_result.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team1 = $_POST['team1'];
    $team2 = $_POST['team2'];
    $score = $_POST['score'];
    $stadium = $_POST['stadium'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Cầu thủ ghi bàn
    $player_names = $_POST['player_name'];
    $teams = $_POST['team'];
    $goal_types = $_POST['goal_type'];
    $goal_times = $_POST['goal_time'];

    // Kiểm tra xem hai đội có khác nhau hay không
    if ($team1 == $team2) {
        echo "Hai đội bóng phải khác nhau!";
        exit;
    }

    // Lưu kết quả trận đấu
    $stmt = $conn->prepare("INSERT INTO matches (team1, team2, score, stadium, date, time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$team1, $team2, $score, $stadium, $date, $time]);

    // Lấy ID trận đấu vừa lưu
    $match_id = $conn->lastInsertId();

    // Lưu thông tin các bàn thắng vào cơ sở dữ liệu
    $stmt = $conn->prepare("INSERT INTO goals (match_id, player_name, team, goal_type, goal_time) VALUES (?, ?, ?, ?, ?)");
    for ($i = 0; $i < count($player_names); $i++) {
        $stmt->execute([$match_id, $player_names[$i], $teams[$i], $goal_types[$i], $goal_times[$i]]);
    }

    echo "Kết quả trận đấu đã được ghi nhận thành công!";
}
?>
<!-- templates/view_match_results.php -->
<!-- templates/view_results.php -->
<h2>Danh sách kết quả các trận đấu</h2>
<table>
    <tr>
        <th>Đội 1</th>
        <th>Đội 2</th>
        <th>Tỷ số</th>
        <th>Ngày thi đấu</th>
        <th>Chức năng</th>
    </tr>

    <?php
    include 'db.php';

    try {
        $stmt = $conn->query("SELECT * FROM matches");
        $matches = $stmt->fetchAll();
    } catch (Exception $e) {
        echo "Có lỗi xảy ra: " . $e->getMessage();
        exit;
    }

    if (count($matches) === 0) {
        echo "<tr><td colspan='5'>Chưa có kết quả trận đấu nào được ghi nhận.</td></tr>";
    } else {
        foreach ($matches as $match) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($match['team1']) . "</td>";
            echo "<td>" . htmlspecialchars($match['team2']) . "</td>";
            echo "<td>" . htmlspecialchars($match['score']) . "</td>";
            echo "<td>" . htmlspecialchars($match['date']) . "</td>";
            echo "<td><a href='edit_result.php?match_id=" . $match['id'] . "'>Sửa</a> | <a href='delete_result.php?match_id=" . $match['id'] . "' onclick=\"return confirm('Bạn có chắc chắn muốn xóa kết quả trận đấu này?');\">Xóa</a></td>";
            echo "</tr>";
        }
    }
    ?>
</table>

