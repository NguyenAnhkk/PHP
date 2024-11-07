<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

// Kết nối đến cơ sở dữ liệu
require_once "database.php";

// Lấy thông tin dự án hiện tại
if (isset($_GET["id"])) {
    $projectId = $_GET["id"];
    $sql = "SELECT * FROM projects WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $projectId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $project = mysqli_fetch_assoc($result);

        if (!$project) {
            echo "<div class='alert alert-danger'>Không tìm thấy dự án.</div>";
            exit();
        }
    }
}

// Cập nhật thông tin dự án
if (isset($_POST["update_project"])) {
    $projectCode = $_POST["project_code"];
    $projectName = $_POST["project_name"];
    $template = $_POST["template"];
    $endDate = $_POST["end_date"];

    $sql = "UPDATE projects SET code = ?, name = ?, template = ?, end_date = ? WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $projectCode, $projectName, $template, $endDate, $projectId);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='alert alert-success'>Cập nhật dự án thành công.</div>";
        } else {
            echo "<div class='alert alert-danger'>Có lỗi xảy ra khi cập nhật dự án.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <title>Chỉnh Sửa Dự Án</title>
</head>
<body>
    <div class="container">
        <h1>Chỉnh Sửa Dự Án</h1>

        <form method="POST">
            <div class="mb-3">
                <label for="project_code" class="form-label">Mã số:</label>
                <input type="text" class="form-control" name="project_code" value="<?php echo $project['code']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="project_name" class="form-label">Tên Dự Án:</label>
                <input type="text" class="form-control" name="project_name" value="<?php echo $project['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="template" class="form-label">Từ Mẫu:</label>
                <select class="form-select" name="template" required>
                    <option value="Mẫu 1" <?php if ($project['template'] == 'Mẫu 1') echo 'selected'; ?>>Mẫu 1</option>
                    <option value="Mẫu 2" <?php if ($project['template'] == 'Mẫu 2') echo 'selected'; ?>>Mẫu 2</option>
                    <option value="Mẫu 3" <?php if ($project['template'] == 'Mẫu 3') echo 'selected'; ?>>Mẫu 3</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">Ngày dự kiến kết thúc:</label>
                <input type="text" class="form-control datepicker" name="end_date" value="<?php echo $project['end_date']; ?>" required>
            </div>
            <button type="submit" name="update_project" class="btn btn-primary">Cập Nhật Dự Án</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
        });
    </script>
</body>
</html>
