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

    // Sửa lỗi: Kiểm tra kết nối và truy vấn
    try {
        $stmt = $conn->query("SELECT t.id, t.team_name, t.stadium, COUNT(p.id) AS player_count 
                              FROM teams t 
                              LEFT JOIN players p ON t.id = p.team_id 
                              GROUP BY t.id");
        $teams = $stmt->fetchAll();
    } catch (Exception $e) {
        echo "Có lỗi xảy ra: " . $e->getMessage();
        exit;
    }

    // Sửa lỗi: Kiểm tra nếu không có đội bóng nào được đăng ký
    if (count($teams) === 0) {
        echo "<tr><td colspan='4'>Chưa có đội bóng nào được đăng ký.</td></tr>";
    } else {
        foreach ($teams as $team) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($team['team_name']) . "</td>";
            echo "<td>" . htmlspecialchars($team['stadium']) . "</td>";
            echo "<td>" . htmlspecialchars($team['player_count']) . "</td>";
            echo "<td><a href='edit_team.php?team_id=" . $team['id'] . "'>Sửa</a> | <a href='delete_team.php?team_id=" . $team['id'] . "' onclick=\"return confirm('Bạn có chắc chắn muốn xóa đội bóng này?');\">Xóa</a></td>";
            echo "</tr>";
        }
    }
    ?>
</table>
