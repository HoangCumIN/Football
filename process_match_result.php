<!-- includes/process_match_result.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        echo "Hai đội phải khác nhau!";
        exit;
    }

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
        echo "Kết quả trận đấu và chi tiết cầu thủ ghi bàn đã được lưu!";
        header("Location: view_results.php");
    } catch (Exception $e) {
        // Rollback nếu có lỗi xảy ra
        $conn->rollBack();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>
