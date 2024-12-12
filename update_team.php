<!-- includes/update_team.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_id = $_POST['team_id'];
    $team_name = $_POST['team_name'];
    $stadium = $_POST['stadium'];

    $new_player_names = $_POST['new_player_name'];
    $new_dobs = $_POST['new_dob'];
    $new_player_types = $_POST['new_player_type'];

    try {
        // Bắt đầu transaction để đảm bảo tất cả thay đổi diễn ra hoặc không thay đổi gì
        $conn->beginTransaction();

        // Cập nhật thông tin đội bóng
        $stmt = $conn->prepare("UPDATE teams SET team_name = ?, stadium = ? WHERE id = ?");
        $stmt->execute([$team_name, $stadium, $team_id]);

        // Cập nhật thông tin các cầu thủ hiện có
        foreach ($_POST['player_name'] as $player_id => $player_name) {
            $dob = $_POST['dob'][$player_id];
            $player_type = $_POST['player_type'][$player_id];

            $stmt = $conn->prepare("UPDATE players SET name = ?, dob = ?, type = ? WHERE id = ?");
            $stmt->execute([$player_name, $dob, $player_type, $player_id]);
        }

        // Thêm cầu thủ mới (nếu có) và kiểm tra tuổi của cầu thủ
        $today = new DateTime();
        if (!empty($new_player_names)) {
            $stmt = $conn->prepare("INSERT INTO players (team_id, name, dob, type) VALUES (?, ?, ?, ?)");

            
                    $dob = new DateTime($new_dobs);
                    $age = $today->diff($dob)->y;

                    // Kiểm tra tuổi cầu thủ
                    if ($age < 16 || $age > 40) {
                        throw new Exception("Tuổi của cầu thủ  không hợp lệ! Tuổi phải từ 16 đến 40.");
                    }

                    // Thêm cầu thủ vào cơ sở dữ liệu nếu tuổi hợp lệ
                    
                    $stmt->execute([$team_id, $new_player_names, $new_dobs, $new_player_types]);
            
        }

        // Commit transaction nếu tất cả các thao tác thành công
        $conn->commit();
        echo "Đội bóng và cầu thủ mới đã được cập nhật thành công!";
        header("Location: view_teams.php");
    } catch (Exception $e) {
        // Rollback nếu có lỗi xảy ra
        $conn->rollBack();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>
