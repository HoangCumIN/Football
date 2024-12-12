<!-- includes/delete_goal.php -->
<?php
include 'db.php';
$goal_id = $_GET['goal_id'];
$match_id = $_GET['match_id'];

try {
    // Xóa bàn thắng khỏi cơ sở dữ liệu
    $stmt = $conn->prepare("DELETE FROM goals WHERE id = ?");
    $stmt->execute([$goal_id]);

    echo "Bàn thắng đã được xóa!";
    header("Location: edit_result.php?match_id=" . $match_id);
} catch (Exception $e) {
    echo "Có lỗi xảy ra khi xóa bàn thắng: " . $e->getMessage();
}
?>
