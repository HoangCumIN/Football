<!-- includes/ranking.php -->
<?php
include 'db.php';

// Truy vấn tính số trận thắng, hòa, thua, và hiệu số bàn thắng của từng đội
$query = "
    SELECT 
        t.team_name,
        SUM(CASE 
                WHEN m.team1 = t.team_name AND LEFT(m.score, 1) > RIGHT(m.score, 1) THEN 1
                WHEN m.team2 = t.team_name AND RIGHT(m.score, 1) > LEFT(m.score, 1) THEN 1
                ELSE 0
            END) AS wins,
        SUM(CASE 
                WHEN LEFT(m.score, 1) = RIGHT(m.score, 1) THEN 1
                ELSE 0
            END) AS draws,
        SUM(CASE 
                WHEN m.team1 = t.team_name AND LEFT(m.score, 1) < RIGHT(m.score, 1) THEN 1
                WHEN m.team2 = t.team_name AND RIGHT(m.score, 1) < LEFT(m.score, 1) THEN 1
                ELSE 0
            END) AS losses,
        SUM(CASE 
                WHEN m.team1 = t.team_name THEN LEFT(m.score, 1) - RIGHT(m.score, 1)
                WHEN m.team2 = t.team_name THEN RIGHT(m.score, 1) - LEFT(m.score, 1)
                ELSE 0
            END) AS goal_difference
    FROM teams t
    LEFT JOIN matches m ON m.team1 = t.team_name OR m.team2 = t.team_name
    GROUP BY t.team_name
    ORDER BY wins DESC, draws DESC, goal_difference DESC
";

$stmt = $conn->prepare($query);
$stmt->execute();
$teams = $stmt->fetchAll();
?>

<h2>Bảng xếp hạng giải đấu</h2>
<table>
    <tr>
        <th>Hạng</th>
        <th>Đội bóng</th>
        <th>Thắng</th>
        <th>Hòa</th>
        <th>Thua</th>
        <th>Hiệu số bàn thắng</th>
    </tr>
    <?php
    $rank = 1;
    foreach ($teams as $team) {
        echo "<tr>";
        echo "<td>" . $rank++ . "</td>";
        echo "<td>" . htmlspecialchars($team['team_name']) . "</td>";
        echo "<td>" . htmlspecialchars($team['wins']) . "</td>";
        echo "<td>" . htmlspecialchars($team['draws']) . "</td>";
        echo "<td>" . htmlspecialchars($team['losses']) . "</td>";
        echo "<td>" . htmlspecialchars($team['goal_difference']) . "</td>";
        echo "</tr>";
    }
    ?>
</table>

<?php if (count($teams) == 0): ?>
<p>Hiện chưa có đội bóng nào được xếp hạng.</p>
<?php endif; ?>
 