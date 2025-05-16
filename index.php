<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$database = "employeedb"; // Change as needed

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

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

// Count total employees
$count_sql = "SELECT COUNT(*) as total FROM employee";
$count_result = $conn->query($count_sql);
$total_employees = 0;
if ($count_result && $count_result->num_rows > 0) {
    $total_employees = $count_result->fetch_assoc()['total'];
}

// Count active employees
$active_sql = "SELECT COUNT(*) as active FROM employee WHERE is_active = 1";
$active_result = $conn->query($active_sql);
$active_employees = 0;
if ($active_result && $active_result->num_rows > 0) {
    $active_employees = $active_result->fetch_assoc()['active'];
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
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            padding-top: 60px;
            padding-bottom: 60px;
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 40px 0;
            color: white;
            border-radius: 0 0 20px 20px;
            margin-bottom: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            border: none;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .status-active {
            color: var(--success-color);
        }
        
        .status-inactive {
            color: var(--danger-color);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 8px;
        }
        
        .card {
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .card-header {
            border-bottom: none;
            padding: 20px;
        }
        
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
        }
        
        footer {
            background-color: var(--dark-bg);
            color: white;
            padding: 30px 0;
            border-radius: 20px 20px 0 0;
            margin-top: 40px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            margin-right: 15px;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-icons a {
            color: white;
            margin-right: 15px;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .social-icons a:hover {
            transform: translateY(-3px);
        }
        
        @media (max-width: 768px) {
            .hero-section {
                border-radius: 0;
            }
            
            footer {
                border-radius: 0;
            }
        }
    </style>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-users-cog me-2"></i>EMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-home me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user-plus me-1"></i> Employees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-chart-line me-1"></i> Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-cog me-1"></i> Settings</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="#" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-user-circle me-1"></i> Admin
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-users me-2"></i>Employee Management System</h1>
                    <p class="lead">Streamline your workforce management with our modern EMS solution</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-inline-block bg-white p-3 rounded-3 text-dark">
                        <h4 class="mb-0"><i class="fas fa-user-tie me-2"></i><?php echo $total_employees; ?> Employees</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-<?php echo isset($edit_employee) ? 'edit' : 'plus-circle'; ?> me-2"></i><?php echo isset($edit_employee) ? 'Edit Employee' : 'Add New Employee'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <?php if (isset($edit_employee)): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_employee['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" required 
                                               value="<?php echo isset($edit_employee) ? $edit_employee['name'] : ''; ?>" placeholder="Full Name">
                                    </div>
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
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone" required
                                               value="<?php echo isset($edit_employee) ? $edit_employee['phone'] : ''; ?>" placeholder="Phone Number">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" required
                                               value="<?php echo isset($edit_employee) ? $edit_employee['email'] : ''; ?>" placeholder="Email Address">
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" id="address" name="address"
                                               value="<?php echo isset($edit_employee) ? $edit_employee['address'] : ''; ?>" placeholder="Full Address">
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label d-block">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                               <?php echo (!isset($edit_employee) || (isset($edit_employee) && $edit_employee['is_active'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-3">
                                <?php if (isset($edit_employee)): ?>
                                    <a href="index.php" class="btn btn-secondary me-2"><i class="fas fa-times me-1"></i>Cancel</a>
                                    <button type="submit" name="update_employee" class="btn btn-success"><i class="fas fa-save me-1"></i>Update Employee</button>
                                <?php else: ?>
                                    <button type="submit" name="add_employee" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i>Add Employee</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Employee Table -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Employee List</h5>
                <div class="input-group input-group-sm w-auto">
                    <input type="text" class="form-control" placeholder="Search employee..." id="employeeSearch">
                    <button class="btn btn-outline-light" type="button"><i class="fas fa-search"></i></button>
                </div>
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
                                <th>Status</th>
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
                                    echo "<td>" . ($row["is_active"] ? 
                                          "<span class='badge bg-success'><i class='fas fa-check-circle me-1'></i>Active</span>" : 
                                          "<span class='badge bg-danger'><i class='fas fa-times-circle me-1'></i>Inactive</span>") . "</td>";
                                    echo "<td>" . date('M d, Y', strtotime($row["created_at"])) . "</td>";
                                    echo "<td class='action-buttons'>
                                          <a href='index.php?edit=" . $row["id"] . "' class='btn btn-sm btn-warning me-1' data-bs-toggle='tooltip' title='Edit'><i class='fas fa-edit'></i></a>
                                          <a href='index.php?delete=" . $row["id"] . "' class='btn btn-sm btn-danger' data-bs-toggle='tooltip' title='Delete' 
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
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Showing <?php echo $result ? $result->num_rows : 0; ?> employees</small>
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modern Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Employee Management</h5>
                    <p class="text-muted">A comprehensive solution for managing your organization's workforce efficiently and effectively.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Dashboard</a></li>
                        <li><a href="#">Employees</a></li>
                        <li><a href="#">Reports</a></li>
                        <li><a href="#">Settings</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h6 class="mb-3">Support</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-3">Newsletter</h6>
                    <p class="text-muted">Subscribe to our newsletter for updates</p>
                    <form class="mb-3">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email Address">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> Employee Management System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-muted">Designed with <i class="fas fa-heart text-danger"></i> by Your Team</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Simple search functionality
        document.getElementById('employeeSearch').addEventListener('keyup', function() {
            let input = this.value.toLowerCase();
            let tbody = document.querySelector('table tbody');
            let rows = tbody.querySelectorAll('tr');
            
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                if(text.indexOf(input) === -1) {
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }
            });
        });
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>