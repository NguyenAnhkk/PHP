<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

// Kết nối đến cơ sở dữ liệu
require_once "database.php";

// Thêm dự án
if (isset($_POST["add_project"])) {
    $projectCode = $_POST["project_code"];
    $projectName = $_POST["project_name"];
    $template = $_POST["template"];
    $endDate = $_POST["end_date"];

    $currentDate = date('Y-m-d');
    if ($endDate < $currentDate) {
        echo "<div class='alert alert-danger'>Ngày dự kiến kết thúc không được trước ngày hiện tại.</div>";
    } else {
        if (!empty($projectCode) && !empty($projectName) && !empty($template) && !empty($endDate)) {
            $sql = "INSERT INTO projects (code, name, template, end_date) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssss", $projectCode, $projectName, $template, $endDate);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>Project added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Something went wrong.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>All fields are required.</div>";
        }
    }
}

// Xóa dự án
if (isset($_GET["delete"])) {
    $projectId = $_GET["delete"];
    $sql = "DELETE FROM projects WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $projectId);
        mysqli_stmt_execute($stmt);
        echo "<div class='alert alert-success'>Project deleted successfully.</div>";
    }
}

// Lấy danh sách dự án
$sql = "SELECT * FROM projects";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <title>User Dashboard</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        .container {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .table {
            flex-grow: 1;
            overflow-y: auto; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>DỰ ÁN</h1>
        <a href="logout.php" class="btn btn-warning">Logout</a>

        <!-- Nút Thêm Dự Án -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">Thêm Dự Án</button>

        <!-- Modal Thêm Dự Án -->
        <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProjectModalLabel">Thêm Dự Án</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="project_code" class="form-label">Mã số:</label>
                                <input type="text" class="form-control" name="project_code" required>
                            </div>
                            <div class="mb-3">
                                <label for="project_name" class="form-label">Tên Dự Án:</label>
                                <input type="text" class="form-control" name="project_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="template" class="form-label">Từ Mẫu:</label>
                                <select class="form-select" name="template" required>
                                    <option value="">Chọn mẫu dự án</option>
                                    <option value="Mẫu 1">Mẫu 1</option>
                                    <option value="Mẫu 2">Mẫu 2</option>
                                    <option value="Mẫu 3">Mẫu 3</option>
                                    <option value="add_new">Thêm mẫu mới</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Ngày dự kiến kết thúc:</label>
                                <input type="text" class="form-control datepicker" name="end_date" required>
                            </div>
                            <button type="submit" name="add_project" class="btn btn-primary">Thêm Dự Án</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách dự án -->
        <h2 class="mt-5">Danh Sách Dự Án Hiện Tại</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Mã số</th>
                    <th>Tên Dự Án</th>
                    <th>Từ Mẫu</th>
                    <th>Ngày Kết Thúc</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($project = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $project['code']; ?></td>
                    <td><?php echo $project['name']; ?></td>
                    <td><?php echo $project['template']; ?></td>
                    <td><?php echo $project['end_date']; ?></td>
                    <td>
                    <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-warning">Sửa</a>
                        <a href="?delete=<?php echo $project['id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này?');">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
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
