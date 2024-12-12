<!-- includes/update_match.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $match_id = $_POST['match_id'];
    $team1 = $_POST['team1'];
    $team2 = $_POST['team2'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $stadium = $_POST['stadium'];

    // Kiểm tra xem đội 1 và đội 2 có khác nhau không
    if ($team1 == $team2) {
        echo "Hai đội phải khác nhau!";
        exit;
    }

    try {
        // Cập nhật thông tin lịch thi đấu
        $stmt = $conn->prepare("UPDATE schedule SET team1_id = ?, team2_id = ?, date = ?, time = ?, stadium = ? WHERE id = ?");
        $stmt->execute([$team1, $team2, $date, $time, $stadium, $match_id]);

        echo "Lịch thi đấu đã được cập nhật thành công!";
        header("Location: view_schedule.php");
    } catch (Exception $e) {
        echo "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>
