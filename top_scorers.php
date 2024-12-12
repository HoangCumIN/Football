<!-- includes/top_scorers.php -->
<?php
include 'db.php';

// Truy vấn danh sách cầu thủ ghi bàn và tổng số bàn thắng của từng cầu thủ
$query = "
    SELECT p.name, t.team_name, p.type, COUNT(g.id) AS total_goals
    FROM players p
    JOIN teams t ON p.team_id = t.id
    LEFT JOIN goals g ON g.player_name = p.name
    GROUP BY p.name, t.team_name, p.type
    HAVING total_goals > 0
    ORDER BY total_goals DESC
";

$stmt = $conn->prepare($query);
$stmt->execute();
$scorers = $stmt->fetchAll();
?>

<h2>Danh sách các cầu thủ ghi bàn</h2>
<table>
    <tr>
        <th>Tên cầu thủ</th>
        <th>Đội bóng</th>
        <th>Loại cầu thủ</th>
        <th>Số bàn thắng</th>
    </tr>
    <?php
    foreach ($scorers as $scorer) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($scorer['name']) . "</td>";
        echo "<td>" . htmlspecialchars($scorer['team_name']) . "</td>";
        echo "<td>" . ($scorer['type'] == 'domestic' ? 'Trong nước' : 'Ngoài nước') . "</td>";
        echo "<td>" . htmlspecialchars($scorer['total_goals']) . "</td>";
        echo "</tr>";
    }
    ?>
</table>

<?php if (count($scorers) == 0): ?>
<p>Chưa có cầu thủ nào ghi bàn.</p>
<?php endif; ?>
