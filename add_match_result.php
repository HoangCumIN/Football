<!-- templates/add_match_result.php -->
<h2>Ghi nhận kết quả trận đấu</h2>
<form method="post" action="add_match_result.php">
    <!-- Thông tin trận đấu -->
    <label for="team1">Đội 1:</label>
    <select id="team1" name="team1" required>
        <!-- Lấy danh sách các đội bóng từ cơ sở dữ liệu -->
        <?php
        include 'db.php';
        $stmt = $conn->query("SELECT id, team_name FROM teams");
        $teams = $stmt->fetchAll();
        foreach ($teams as $team) {
            echo "<option value='" . $team['id'] . "'>" . htmlspecialchars($team['team_name']) . "</option>";
        }
        ?>
    </select>

    <label for="team2">Đội 2:</label>
    <select id="team2" name="team2" required>
        <?php
        foreach ($teams as $team) {
            echo "<option value='" . $team['id'] . "'>" . htmlspecialchars($team['team_name']) . "</option>";
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

    <input type="submit" name="submit_match" value="Ghi nhận kết quả">

    <!-- Chi tiết cầu thủ ghi bàn -->
    <div id="goalDetails">
        <h3>Chi tiết cầu thủ ghi bàn</h3>
        <table id="goals_table">
            <tr>
                <th>Tên cầu thủ</th>
                <th>Đội bóng</th>
                <th>Loại bàn thắng (A/B/C)</th>
                <th>Thời điểm (phút)</th>
            </tr>
            <tr>
                <td><input type="text" name="player_name[]" required></td>
                <td>
                    <select name="team[]" required>
                        <?php foreach ($teams as $team): ?>
                        <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <select name="goal_type[]" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </td>
                <td><input type="number" name="goal_time[]" min="0" max="90" required></td>
            </tr>
        </table>

        <button type="button" onclick="addGoalRow()">Thêm cầu thủ ghi bàn</button>
    </div>
</form>

<!-- Hiển thị kết quả trận đấu -->
<?php if (isset($resultMessage)): ?>
    <div id="resultOutput">
        <?php echo $resultMessage; ?>
    </div>
<?php endif; ?>

<script>
// Hàm thêm cầu thủ ghi bàn mới vào bảng
function addGoalRow() {
    const table = document.getElementById('goals_table');
    const row = table.insertRow();
    row.innerHTML = `
        <td><input type="text" name="player_name[]" required></td>
        <td>
            <select name="team[]" required>
                <?php foreach ($teams as $team): ?>
                <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="goal_type[]" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
            </select>
        </td>
        <td><input type="number" name="goal_time[]" min="0" max="90" required></td>
    `;
}
</script>
<?php
include 'db.php';

if (isset($_POST['submit_match'])) {
    $team1 = $_POST['team1'];
    $team2 = $_POST['team2'];
    $score = $_POST['score'];
    $stadium = $_POST['stadium'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $player_names = $_POST['player_name'];
    $teams = $_POST['team'];
    $goal_types = $_POST['goal_type'];
    $goal_times = $_POST['goal_time'];

    // Kiểm tra xem đội 1 và đội 2 có khác nhau không
    if ($team1 == $team2) {
        $resultMessage = "Hai đội phải khác nhau!";
    } else {
        try {
            // Bắt đầu transaction để đảm bảo tất cả thay đổi diễn ra hoặc không thay đổi gì
            $conn->beginTransaction();

            // Lưu kết quả trận đấu
            $stmt = $conn->prepare("INSERT INTO matches (team1, team2, score, stadium, date, time) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$team1, $team2, $score, $stadium, $date, $time]);

            // Lấy ID trận đấu vừa được lưu
            $match_id = $conn->lastInsertId();

            // Lưu thông tin các bàn thắng
            $stmt = $conn->prepare("INSERT INTO goals (match_id, player_name, team, goal_type, goal_time) VALUES (?, ?, ?, ?, ?)");
            for ($i = 0; $i < count($player_names); $i++) {
                $stmt->execute([$match_id, $player_names[$i], $teams[$i], $goal_types[$i], $goal_times[$i]]);
            }

            // Commit transaction nếu tất cả các thao tác thành công
            $conn->commit();

            // Lấy tên đội bóng từ cơ sở dữ liệu
            $stmt = $conn->prepare("SELECT team_name FROM teams WHERE id = ?");
            $stmt->execute([$team1]);
            $team1_name = $stmt->fetchColumn();

            $stmt->execute([$team2]);
            $team2_name = $stmt->fetchColumn();

            // Hiển thị kết quả trận đấu
            $resultMessage = "Kết quả trận đấu: $team1_name $score $team2_name tại $stadium vào ngày $date, lúc $time.";
        } catch (Exception $e) {
            // Rollback nếu có lỗi xảy ra
            $conn->rollBack();
            $resultMessage = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
}

// Hiển thị form và kết quả

?>
