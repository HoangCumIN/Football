<!-- includes/delete_team.php -->
<?php
include 'db.php';

$team_id = $_GET['team_id'];

try {
    // Bắt đầu transaction
    $conn->beginTransaction();

    // Xóa toàn bộ cầu thủ thuộc đội
    $stmt = $conn->prepare("DELETE FROM players WHERE team_id = ?");
    $stmt->execute([$team_id]);

    // Xóa đội bóng
    $stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
    $stmt->execute([$team_id]);

    // Commit transaction
    $conn->commit();

    echo "Đã xóa đội bóng!";
    header("Location: view_teams.php");
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollBack();
    echo "Có lỗi xảy ra khi xóa đội bóng: " . $e->getMessage();
}
?>
