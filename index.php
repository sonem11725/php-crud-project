<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "employeedb"; // Change as needed

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission for adding employee
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_employee'])) {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $sql = "INSERT INTO employee (name, gender, phone, email, address, is_active) 
            VALUES ('$name', '$gender', '$phone', '$email', '$address', $is_active)";
    
    if ($conn->query($sql) === TRUE) {
        $success_message = "New employee added successfully";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Process delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM employee WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $success_message = "Employee deleted successfully";
    } else {
        $error_message = "Error deleting employee: " . $conn->error;
    }
}

// Process update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_employee'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $sql = "UPDATE employee SET 
            name = '$name', 
            gender = '$gender', 
            phone = '$phone', 
            email = '$email', 
            address = '$address', 
            is_active = $is_active 
            WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $success_message = "Employee updated successfully";
    } else {
        $error_message = "Error updating employee: " . $conn->error;
    }
}

// Get employee data
$sql = "SELECT * FROM employee ORDER BY id DESC";
$result = $conn->query($sql);

// Get employee by ID for editing
$edit_employee = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_sql = "SELECT * FROM employee WHERE id = $edit_id";
    $edit_result = $conn->query($edit_sql);
    if ($edit_result && $edit_result->num_rows > 0) {
        $edit_employee = $edit_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <style>
        .status-active {
            color: #198754;
        }
        .status-inactive {
            color: #dc3545;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Employee Management System</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo isset($edit_employee) ? 'Edit Employee' : 'Add New Employee'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <?php if (isset($edit_employee)): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_employee['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required 
                                           value="<?php echo isset($edit_employee) ? $edit_employee['name'] : ''; ?>">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Gender</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="gender_male" value="Male" 
                                                <?php echo (isset($edit_employee) && $edit_employee['gender'] == 'Male') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="gender_male">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="gender_female" value="Female"
                                                <?php echo (isset($edit_employee) && $edit_employee['gender'] == 'Female') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="gender_female">Female</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required
                                           value="<?php echo isset($edit_employee) ? $edit_employee['phone'] : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                           value="<?php echo isset($edit_employee) ? $edit_employee['email'] : ''; ?>">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                           value="<?php echo isset($edit_employee) ? $edit_employee['address'] : ''; ?>">
                                </div>
                                
                                <!-- <div class="col-md-4 mb-3">
                                    <label class="form-label d-block">Status</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                               <?php echo (!isset($edit_employee) || (isset($edit_employee) && $edit_employee['is_active'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div> -->
                            </div>
                            
                            <div class="d-flex justify-content-end mt-3">
                                <?php if (isset($edit_employee)): ?>
                                    <a href="index.php" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" name="update_employee" class="btn btn-success">Update Employee</button>
                                <?php else: ?>
                                    <button type="submit" name="add_employee" class="btn btn-primary">Add Employee</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Employee Table -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Employee List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <!-- <th>Status</th> -->
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["id"] . "</td>";
                                    echo "<td>" . $row["name"] . "</td>";
                                    echo "<td>" . $row["gender"] . "</td>";
                                    echo "<td>" . $row["phone"] . "</td>";
                                    echo "<td>" . $row["email"] . "</td>";
                                    echo "<td>" . (empty($row["address"]) ? "<em class='text-muted'>Not provided</em>" : $row["address"]) . "</td>";
                                    // echo "<td>" . ($row["is_active"] ? 
                                    //       "<span class='badge bg-success'>Active</span>" : 
                                    //       "<span class='badge bg-danger'>Inactive</span>") . "</td>";
                                    echo "<td>" . date('M d, Y', strtotime($row["created_at"])) . "</td>";
                                    echo "<td class='action-buttons'>
                                          <a href='index.php?edit=" . $row["id"] . "' class='btn btn-sm btn-warning me-1'><i class='fas fa-edit'></i></a>
                                          <a href='index.php?delete=" . $row["id"] . "' class='btn btn-sm btn-danger' 
                                             onclick='return confirm(\"Are you sure you want to delete this employee?\")'><i class='fas fa-trash'></i></a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No employees found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>