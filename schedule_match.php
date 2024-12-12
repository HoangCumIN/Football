<!-- templates/schedule_match.php -->
<form action="schedule_match.php" method="post">
    <label for="round">Vòng thi đấu:</label>
    <select id="round" name="round">
        <option value="1">Lượt đi (Vòng 1)</option>
        <option value="2">Lượt về (Vòng 2)</option>
    </select>

    <label for="team1">Đội 1:</label>
    <select id="team1" name="team1">
        <!-- Tải danh sách đội từ cơ sở dữ liệu -->
        <?php
        include 'db.php';
        $stmt = $conn->query("SELECT id, team_name FROM teams");
        $teams = $stmt->fetchAll();
        foreach ($teams as $team) {
            echo "<option value='" . $team['id'] . "'>" . $team['team_name'] . "</option>";
        }
        ?>
    </select>

    <label for="team2">Đội 2:</label>
    <select id="team2" name="team2">
        <?php
        foreach ($teams as $team) {
            echo "<option value='" . $team['id'] . "'>" . $team['team_name'] . "</option>";
        }
        ?>
    </select>

    <label for="date">Ngày thi đấu:</label>
    <input type="date" id="date" name="date" required>

    <label for="time">Giờ thi đấu:</label>
    <input type="time" id="time" name="time" required>

    <label for="stadium">Sân thi đấu:</label>
    <input type="text" id="stadium" name="stadium" required>

    <input type="submit" value="Lập lịch thi đấu">
</form>
<!-- includes/schedule_match.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $round = $_POST['round'];
    $team1_id = $_POST['team1'];
    $team2_id = $_POST['team2'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $stadium = $_POST['stadium'];

    // Kiểm tra xem đội 1 và đội 2 có khác nhau hay không
    if ($team1_id == $team2_id) {
        echo "Không thể lập lịch thi đấu cho cùng một đội.";
    } else {
        // Lưu lịch thi đấu vào cơ sở dữ liệu
        $stmt = $conn->prepare("INSERT INTO schedule (round, team1_id, team2_id, date, time, stadium) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$round, $team1_id, $team2_id, $date, $time, $stadium]);

        echo "Lịch thi đấu đã được lập thành công!";
    }
}
?>
<!-- includes/schedule_match.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $round = $_POST['round'];
    $team1_id = $_POST['team1'];
    $team2_id = $_POST['team2'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $stadium = $_POST['stadium'];

    // Kiểm tra xem đội 1 và đội 2 có khác nhau hay không
    if ($team1_id == $team2_id) {
        echo "Không thể lập lịch thi đấu cho cùng một đội.";
    } else {
        // Kiểm tra xem đội 1 hoặc đội 2 đã có lịch thi đấu trong cùng ngày hay chưa
        $stmt = $conn->prepare("SELECT COUNT(*) FROM schedule WHERE date = ? AND (team1_id = ? OR team2_id = ? OR team1_id = ? OR team2_id = ?)");
        $stmt->execute([$date, $team1_id, $team1_id, $team2_id, $team2_id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "Một trong hai đội đã có lịch thi đấu vào ngày này.";
        } else {
            // Lưu lịch thi đấu vào cơ sở dữ liệu
            $stmt = $conn->prepare("INSERT INTO schedule (round, team1_id, team2_id, date, time, stadium) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$round, $team1_id, $team2_id, $date, $time, $stadium]);

            echo "Lịch thi đấu đã được lập thành công!";
        }
    }
}
?>
<!-- templates/view_schedule.php -->
<!-- templates/view_schedule.php -->
<h2>Danh sách lịch thi đấu</h2>
<table>
    <tr>
        <th>Đội 1</th>
        <th>Đội 2</th>
        <th>Ngày thi đấu</th>
        <th>Giờ thi đấu</th>
        <th>Sân vận động</th>
        <th>Chức năng</th>
    </tr>

    <?php
    include 'db.php';

    try {
        $stmt = $conn->query("SELECT s.id, s.round, s.team1_id, s.team2_id, s.date, s.time, s.stadium, t1.team_name AS team1_name, t2.team_name AS team2_name 
                              FROM schedule s 
                              JOIN teams t1 ON s.team1_id = t1.id 
                              JOIN teams t2 ON s.team2_id = t2.id");
        $matches = $stmt->fetchAll();
    } catch (Exception $e) {
        echo "Có lỗi xảy ra: " . $e->getMessage();
        exit;
    }

    if (count($matches) === 0) {
        echo "<tr><td colspan='6'>Chưa có lịch thi đấu nào được lập.</td></tr>";
    } else {
        foreach ($matches as $match) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($match['team1_name']) . "</td>";
            echo "<td>" . htmlspecialchars($match['team2_name']) . "</td>";
            echo "<td>" . htmlspecialchars($match['date']) . "</td>";
            echo "<td>" . htmlspecialchars($match['time']) . "</td>";
            echo "<td>" . htmlspecialchars($match['stadium']) . "</td>";
            echo "<td><a href='edit_match.php?match_id=" . $match['id'] . "'>Sửa</a> | <a href='delete_match.php?match_id=" . $match['id'] . "' onclick=\"return confirm('Bạn có chắc chắn muốn xóa lịch thi đấu này?');\">Xóa</a></td>";
            echo "</tr>";
        }
    }
    ?>
</table>