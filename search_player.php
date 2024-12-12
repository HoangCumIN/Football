<!-- templates/search_player.php -->
<h2>Tra cứu cầu thủ</h2>
<form action="search_player.php" method="get">
    <label for="team">Đội bóng:</label>
    <select id="team" name="team">
        <option value="">Tất cả</option>
        <!-- Lấy danh sách đội bóng từ cơ sở dữ liệu -->
        <?php
        include 'db.php';
        $stmt = $conn->query("SELECT id, team_name FROM teams");
        $teams = $stmt->fetchAll();
        foreach ($teams as $team) {
            echo "<option value='" . $team['id'] . "'>" . $team['team_name'] . "</option>";
        }
        ?>
    </select>

    <label for="player_type">Loại cầu thủ:</label>
    <select id="player_type" name="player_type">
        <option value="">Tất cả</option>
        <option value="domestic">Trong nước</option>
        <option value="foreign">Ngoài nước</option>
    </select>

    <input type="submit" value="Tra cứu">
</form>
<!-- includes/search_player.php -->
<!-- includes/search_player.php -->
<?php
include 'db.php';

// Nhận giá trị từ form
$team_id = isset($_GET['team']) ? $_GET['team'] : '';
$player_type = isset($_GET['player_type']) ? $_GET['player_type'] : '';

// Câu truy vấn cơ bản
$query = "SELECT p.name, t.team_name, p.type, 
                 (SELECT COUNT(*) FROM goals g WHERE g.player_name = p.name) AS total_goals 
          FROM players p
          JOIN teams t ON p.team_id = t.id
          WHERE 1=1"; // Điều kiện mặc định để dễ dàng thêm các điều kiện khác

// Nếu có chọn đội bóng, thêm điều kiện vào truy vấn
if (!empty($team_id)) {
    $query .= " AND p.team_id = :team_id";
}

// Nếu có chọn loại cầu thủ, thêm điều kiện vào truy vấn
if (!empty($player_type)) {
    $query .= " AND p.type = :player_type";
}

// Chuẩn bị câu truy vấn
$stmt = $conn->prepare($query);

// Gán giá trị cho tham số nếu có
if (!empty($team_id)) {
    $stmt->bindParam(':team_id', $team_id);
}

if (!empty($player_type)) {
    $stmt->bindParam(':player_type', $player_type);
}

// Thực thi truy vấn
$stmt->execute();
$players = $stmt->fetchAll();
?>

<h2>Kết quả tra cứu cầu thủ</h2>

<?php if (count($players) > 0): ?>
<table>
    <tr>
        <th>Xếp hạng</th>
        <th>Tên cầu thủ</th>
        <th>Đội bóng</th>
        <th>Loại cầu thủ</th>
        <th>Tổng số bàn thắng</th>
    </tr>
    <?php
    $rank = 1;
    usort($players, function($a, $b) {
        return $b['total_goals'] <=> $a['total_goals'];
    });
    foreach ($players as $player) {
        echo "<tr>";
        echo "<td>" . $rank . "</td>";
        echo "<td>" . htmlspecialchars($player['name']) . "</td>";
        echo "<td>" . htmlspecialchars($player['team_name']) . "</td>";
        echo "<td>" . ($player['type'] == 'domestic' ? 'Trong nước' : 'Ngoài nước') . "</td>";
        echo "<td>" . htmlspecialchars($player['total_goals']) . "</td>";
        echo "</tr>";
        $rank++;
    }
    ?>
</table>
<?php else: ?>
<p>Không có cầu thủ nào phù hợp với tiêu chí tìm kiếm.</p>
<?php endif; ?>