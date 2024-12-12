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
