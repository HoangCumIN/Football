<!-- templates/edit_match.php -->
<?php
include 'db.php';
$match_id = $_GET['match_id'];

try {
    // Lấy thông tin lịch thi đấu
    $stmt = $conn->prepare("SELECT * FROM schedule WHERE id = ?");
    $stmt->execute([$match_id]);
    $match = $stmt->fetch();

    if (!$match) {
        echo "Không tìm thấy lịch thi đấu!";
        exit;
    }

    // Lấy danh sách đội bóng
    $stmt = $conn->query("SELECT id, team_name FROM teams");
    $teams = $stmt->fetchAll();
} catch (Exception $e) {
    echo "Có lỗi xảy ra: " . $e->getMessage();
    exit;
}
?>

<h2>Sửa lịch thi đấu</h2>
<form action="update_match.php" method="post">
    <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">

    <label for="team1">Đội 1:</label>
    <select id="team1" name="team1">
        <?php foreach ($teams as $team): ?>
        <option value="<?php echo $team['id']; ?>" <?php echo $team['id'] == $match['team1_id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($team['team_name']); ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label for="team2">Đội 2:</label>
    <select id="team2" name="team2">
        <?php foreach ($teams as $team): ?>
        <option value="<?php echo $team['id']; ?>" <?php echo $team['id'] == $match['team2_id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($team['team_name']); ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label for="date">Ngày thi đấu:</label>
    <input type="date" id="date" name="date" value="<?php echo $match['date']; ?>" required>

    <label for="time">Giờ thi đấu:</label>
    <input type="time" id="time" name="time" value="<?php echo $match['time']; ?>" required>

    <label for="stadium">Sân vận động:</label>
    <input type="text" id="stadium" name="stadium" value="<?php echo htmlspecialchars($match['stadium']); ?>" required>

    <input type="submit" value="Cập nhật lịch thi đấu">
</form>
