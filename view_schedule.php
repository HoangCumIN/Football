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
        $stmt = $conn->query("SELECT * FROM schedule");
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
            echo "<td>" . htmlspecialchars($match['team1_id']) . "</td>";
            echo "<td>" . htmlspecialchars($match['team2_id']) . "</td>";
            echo "<td>" . htmlspecialchars($match['date']) . "</td>";
            echo "<td>" . htmlspecialchars($match['time']) . "</td>";
            echo "<td>" . htmlspecialchars($match['stadium']) . "</td>";
            echo "<td><a href='edit_match.php?match_id=" . $match['id'] . "'>Sửa</a> | <a href='delete_match.php?match_id=" . $match['id'] . "' onclick=\"return confirm('Bạn có chắc chắn muốn xóa lịch thi đấu này?');\">Xóa</a></td>";
            echo "</tr>";
        }
    }
    ?>
</table>
