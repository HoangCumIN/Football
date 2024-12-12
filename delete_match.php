<!-- includes/delete_match.php -->
<?php
include 'db.php';
$match_id = $_GET['match_id'];

try {
    // Xóa lịch thi đấu khỏi cơ sở dữ liệu
    $stmt = $conn->prepare("DELETE FROM schedule WHERE id = ?");
    $stmt->execute([$match_id]);

    echo "Lịch thi đấu đã được xóa!";
    header("Location: view_schedule.php");
} catch (Exception $e) {
    echo "Có lỗi xảy ra khi xóa lịch thi đấu: " . $e->getMessage();
}
?>
