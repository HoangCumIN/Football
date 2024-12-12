<!-- includes/update_result.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $match_id = $_POST['match_id'];
    $score = $_POST['score'];
    $player_names = $_POST['player_name'];
    $goal_types = $_POST['goal_type'];
    $goal_times = $_POST['goal_time'];

    $new_player_names = $_POST['new_player_name'];
    $new_teams = $_POST['new_team'];
    $new_goal_types = $_POST['new_goal_type'];
    $new_goal_times = $_POST['new_goal_time'];

    try {
        // Bắt đầu transaction để đảm bảo tất cả thay đổi diễn ra hoặc không thay đổi gì
        $conn->beginTransaction();

        // Cập nhật kết quả trận đấu
        $stmt = $conn->prepare("UPDATE matches SET score = ? WHERE id = ?");
        $stmt->execute([$score, $match_id]);

        // Cập nhật thông tin các bàn thắng đã có
        foreach ($player_names as $goal_id => $player_name) {
            $goal_type = $goal_types[$goal_id];
            $goal_time = $goal_times[$goal_id];

            $stmt = $conn->prepare("UPDATE goals SET player_name = ?, goal_type = ?, goal_time = ? WHERE id = ?");
            $stmt->execute([$player_name, $goal_type, $goal_time, $goal_id]);
        }

        // Thêm cầu thủ ghi bàn mới
        if (!empty($new_player_names)) {
            $stmt = $conn->prepare("INSERT INTO goals (match_id, player_name, team, goal_type, goal_time) VALUES (?, ?, ?, ?, ?)");

            for ($i = 0; $i < count($new_player_names); $i++) {
                if (!empty($new_player_names[$i])) {  // Kiểm tra tên cầu thủ mới không rỗng
                    $stmt->execute([$match_id, $new_player_names[$i], $new_teams[$i], $new_goal_types[$i], $new_goal_times[$i]]);
                }
            }
        }

        // Commit transaction nếu tất cả các thao tác thành công
        $conn->commit();
        echo "Kết quả trận đấu và cầu thủ ghi bàn đã được cập nhật!";
        header("Location: view_results.php");
    } catch (Exception $e) {
        // Rollback nếu có lỗi xảy ra
        $conn->rollBack();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>
