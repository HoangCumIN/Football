<!-- includes/delete_result.php -->
<?php
include 'db.php';
$match_id = $_GET['match_id'];

try {
    // Bắt đầu transaction để xóa trận đấu và bàn thắng liên quan
    $conn->beginTransaction();

    // Xóa các bàn thắng liên quan đến trận đấu
    $stmt = $conn->prepare("DELETE FROM goals WHERE match_id = ?");
    $stmt->execute([$match_id]);

    // Xóa kết quả trận đấu
    $stmt = $conn->prepare("DELETE FROM matches WHERE id = ?");
    $stmt->execute([$match_id]);

    // Commit transaction nếu tất cả các thao tác thành công
    $conn->commit();
    echo "Kết quả trận đấu đã được xóa!";
    header("Location: view_results.php");
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollBack();
    echo "Có lỗi xảy ra khi xóa kết quả trận đấu: " . $e->getMessage();
}
?>
