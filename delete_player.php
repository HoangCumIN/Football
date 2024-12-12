<!-- includes/delete_player.php -->
<?php
include 'db.php';

$player_id = $_GET['player_id'];

try {
    // Xóa cầu thủ khỏi cơ sở dữ liệu
    $stmt = $conn->prepare("DELETE FROM players WHERE id = ?");
    $stmt->execute([$player_id]);

    echo "Đã xóa cầu thủ!";
    header("Location: " . $_SERVER["HTTP_REFERER"]); // Quay lại trang trước
} catch (Exception $e) {
    echo "Có lỗi xảy ra khi xóa cầu thủ: " . $e->getMessage();
}
?>
