<!-- templates/change_rules.php -->
<h2>Thay đổi quy định</h2>
<form action="change_rules.php" method="post">
    <?php
    include 'db.php';

    // Lấy quy định hiện tại từ cơ sở dữ liệu
    $stmt = $conn->query("SELECT * FROM rules LIMIT 1");
    $rule = $stmt->fetch();
    ?>

    <label for="min_age">Tuổi tối thiểu:</label>
    <input type="number" id="min_age" name="min_age" value="<?php echo $rule['min_age']; ?>" min="0" max="99" required>

    <label for="max_age">Tuổi tối đa:</label>
    <input type="number" id="max_age" name="max_age" value="<?php echo $rule['max_age']; ?>" min="0" max="99" required>

    <label for="max_foreign_players">Số cầu thủ nước ngoài tối đa:</label>
    <input type="number" id="max_foreign_players" name="max_foreign_players" value="<?php echo $rule['max_foreign_players']; ?>" min="0" max="99" required>
    <label for="min_players">Số cầu thủ tối thiểu:</label>
<input type="number" id="min_players" name="min_players" value="<?php echo $rule['min_players']; ?>" min="0" max="99" required>

<label for="max_players">Số cầu thủ tối đa:</label>
<input type="number" id="max_players" name="max_players" value="<?php echo $rule['max_players']; ?>" min="0" max="99" required>

    <label for="win_points">Điểm thắng:</label>
    <input type="number" id="win_points" name="win_points" value="<?php echo $rule['win_points']; ?>" min="1" required>

    <label for="draw_points">Điểm hòa:</label>
    <input type="number" id="draw_points" name="draw_points" value="<?php echo $rule['draw_points']; ?>" min="0" required>

    <label for="loss_points">Điểm thua:</label>
    <input type="number" id="loss_points" name="loss_points" value="<?php echo $rule['loss_points']; ?>" min="0" required>

    <input type="submit" value="Cập nhật quy định">
</form>
<!-- includes/change_rules.php -->
<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $min_age = $_POST['min_age'];
    $max_age = $_POST['max_age'];
    $max_foreign_players = $_POST['max_foreign_players'];
    $win_points = $_POST['win_points'];
    $draw_points = $_POST['draw_points'];
    $loss_points = $_POST['loss_points'];
    $min_players = $_POST['min_players'];
     $max_players = $_POST['max_players'];

    // Kiểm tra tính hợp lệ: điểm thắng phải lớn hơn điểm hòa, và điểm hòa lớn hơn điểm thua
    if ($win_points > $draw_points && $draw_points > $loss_points) {
        // Cập nhật các quy định vào cơ sở dữ liệu
        $stmt = $conn->prepare("UPDATE rules SET min_age = ?, max_age = ?, max_foreign_players = ?, win_points = ?, draw_points = ?, loss_points = ? WHERE id = 1");
        $stmt->execute([$min_age, $max_age, $max_foreign_players, $win_points, $draw_points, $loss_points]);

        echo "Quy định đã được cập nhật thành công!";
    } else {
        echo "Điểm số không hợp lệ! Điểm thắng phải lớn hơn điểm hòa, và điểm hòa phải lớn hơn điểm thua.";
    }
}
?>
<h2>Quy định hiện tại của giải đấu</h2>
<?php
include 'db.php';

// Lấy quy định hiện tại từ cơ sở dữ liệu
$stmt = $conn->query("SELECT * FROM rules LIMIT 1");
$rule = $stmt->fetch();
?>

<ul>
<li>Tuổi tối thiểu của cầu thủ: <?php echo $rule['min_age']; ?></li>
    <li>Tuổi tối đa của cầu thủ: <?php echo $rule['max_age']; ?></li>
    <li>Số cầu thủ nước ngoài tối đa: <?php echo $rule['max_foreign_players']; ?></li>
    <li>Số cầu thủ tối thiểu: <?php echo $rule['min_players']; ?></li>
    <li>Số cầu thủ tối đa: <?php echo $rule['max_players']; ?></li>
    <li>Điểm thắng: <?php echo $rule['win_points']; ?></li>
    <li>Điểm hòa: <?php echo $rule['draw_points']; ?></li>
    <li>Điểm thua: <?php echo $rule['loss_points']; ?></li>
</ul>