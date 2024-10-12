<?php
include "config.php";
include 'header.php';

// Message to display for user actions
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get the user ID from session
$uid = $_SESSION['id'];
$role = $_SESSION['role']; // Assuming role is stored in the session

// Fetch festivals based on user role (admin vs user)
// Fetch festivals based on user role (admin vs user)
if ($role === "admin") {
    // Retrieve all festivals for both admin and user
    $res = mysqli_query($conn, "SELECT * FROM festival WHERE status = 0;");
}
 else {
    // Non-admin: Retrieve only the festivals assigned to the user
    $res = mysqli_query($conn, "SELECT ufa.festival_id as id, f.name, f.spouse_name, f.occupation, f.festival_name, f.date, f.place 
                                FROM festival f 
                                JOIN user_festival_assignment ufa ON ufa.festival_id = f.id 
                                JOIN users u ON u.id = ufa.user_id 
                                WHERE u.id='$uid' AND f.status = 0");
}


// Handle new festival registration
if (isset($_POST["submit"])) {
    $name = $_POST['name'];
    $spouse_name = $_POST['spouse_name'];
    $occupation = $_POST['occupation'];
    $festival_name = $_POST['festival_name'];
    $date = $_POST['date'];
    $place = $_POST['place'];

    $query = $conn->prepare("INSERT INTO festival (name, spouse_name, occupation, festival_name, date, place) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssssss", $name, $spouse_name, $occupation, $festival_name, $date, $place);

    if ($query->execute()) {
        $_SESSION['message'] = "<div class='alert alert-success'>Registration successful!</div>";
        header("Location: display.php");
        exit;
    } else {
        $error_message = $query->error;
        $_SESSION['message'] = "<div class='alert alert-danger'>Registration failed. Error: $error_message</div>";
        header("Location: display.php");
        exit;
    }

    $query->close();
    $conn->close();
}

// Handle festival deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $delete_query = "DELETE FROM festival WHERE id = $delete_id";
    $result = mysqli_query($conn, $delete_query);

    if ($result) {
        $_SESSION['message'] = "<div class='alert alert-success'>Record deleted successfully!</div>";
        header("Location: display.php");
        exit;
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>Error deleting record</div>";
        header("Location: display.php");
        exit;
    }
}

// Handle festival closing
if (isset($_GET['close_id'])) {
    $close_id = $_GET['close_id'];

    $close_query = "UPDATE festival SET status = 1 WHERE id = $close_id";
    $result = mysqli_query($conn, $close_query);

    if ($result) {
        $_SESSION['message'] = "<div class='alert alert-success'>Festival closed successfully!</div>";
        header("Location: display.php");
        exit;
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>Error closing the festival</div>";
        header("Location: display.php");
        exit;
    }
}

// Handle assigning user to festival
if (isset($_POST['assign_festival'])) {
    $festival_id = $_POST['festival_id'];
    $user_id = $_POST['user_id'];

    // Assign user to the selected festival
    $assign_query = "INSERT INTO user_festival_assignment (user_id, festival_id) VALUES ('$user_id', '$festival_id')";
    if (mysqli_query($conn, $assign_query)) {
        $_SESSION['message'] = "<div class='alert alert-success'>User assigned successfully!</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>Error assigning user</div>";
    }
    header("Location: display.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Festival Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate festival_id in the assign modal
        function assignUser(festival_id) {
            document.getElementById('festival_id').value = festival_id;
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFestivalModal">Add New Function</button>
            <?php if ($role === "admin") { ?>
                &emsp;&emsp;
                <a href="close_function.php" class="btn btn-primary">Close Function</a>
            <?php } ?>
        </div>
        <br />
        <h3 class="mb-3">விழாக்கள் பட்டியல்</h3>
        <?php if ($message): ?>
            <div id="message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">பெயர்</th>
                    <th scope="col">துணைவி பெயர்</th>
                    <th scope="col">தொழில்</th>
                    <th scope="col">விழா பெயர்</th>
                    <th scope="col">தேதி</th>
                    <th scope="col">இடம்</th>
                    <th scope="col">செயல்</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                if (mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['spouse_name']; ?></td>
                            <td><?php echo $row['occupation']; ?></td>
                            <td><?php echo $row['festival_name']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['place']; ?></td>
                            <td>
    <a href="moi.php?moi_id=<?php echo $row['id']; ?>" class="btn btn-primary">Form</a>
    <?php if ($role === "admin" || $role === "user") { // Allow delete for both admin and user ?>
        <a href="edit.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-info">Edit</a>
        
        <!-- Delete Button with Confirmation -->
        <a href="display.php?delete_id=<?php echo $row['id']; ?>" 
           class="btn btn-danger" 
           onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
        
        <?php if ($role === "admin") { ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" 
                    data-bs-target="#assignModal" onclick="assignUser(<?php echo $row['id']; ?>)">Assign</button>
            <a href="display.php?close_id=<?php echo $row['id']; ?>" class="btn btn-secondary">Close</a>
        <?php } ?>
        <a href="moidisplay.php?festival_id=<?php echo $row['id']; ?>" class="btn btn-warning">List</a>
    <?php } ?>
</td>

                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="8">தகவல் கிடைக்கவில்லை</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Festival Modal -->
    <div class="modal fade" id="addFestivalModal" tabindex="-1" aria-labelledby="addFestivalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFestivalModalLabel">புதிய விழா சேர்க்க</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="display.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">பெயர்:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="spouse_name" class="form-label">துணைவி பெயர்:</label>
                            <input type="text" class="form-control" id="spouse_name" name="spouse_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="occupation" class="form-label">தொழில்:</label>
                            <input type="text" class="form-control" id="occupation" name="occupation" required>
                        </div>
                        <div class="mb-3">
                            <label for="festival_name" class="form-label">விழா பெயர்:</label>
                            <input type="text" class="form-control" id="festival_name" name="festival_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">தேதி:</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="place" class="form-label">இடம்:</label>
                            <input type="text" class="form-control" id="place" name="place" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Modal -->
    <!-- Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Active User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignForm" method="POST">
                        <input type="hidden" name="festival_id" id="festival_id">
                        <div class="form-group">
                            <label for="user_id">Select Active User:</label>
                            <select id="user_id" name="user_id" class="form-control">
                                <?php
                                $user_res = mysqli_query($conn, "SELECT id, username FROM users WHERE status = '1'");
                                while ($user_row = mysqli_fetch_assoc($user_res)) { ?>
                                    <option value="<?php echo $user_row['id']; ?>"><?php echo $user_row['username']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Assign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass festival_id to the modal when the Assign button is clicked
        var assignModal = document.getElementById('assignModal');
        assignModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var festivalId = button.getAttribute('data-id');
            var modalInput = assignModal.querySelector('#festival_id');
            modalInput.value = festivalId;
        });

        // Handle form submission (optional: you can handle it via AJAX)
        document.getElementById('assignForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var festivalId = form.festival_id.value;
            var userId = form.user_id.value;

            // AJAX request to handle the assignment
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "assign.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert('User assigned successfully!');
                    // Optionally close the modal
                    var modal = bootstrap.Modal.getInstance(assignModal);
                    modal.hide();
                }
            };
            xhr.send("festival_id=" + festivalId + "&user_id=" + userId);
        });

        // Automatically hide the success message after 20 seconds
        setTimeout(function() {
            var message = document.getElementById('message');
            if (message) {
                message.style.display = 'none';
            }
        }, 10000); // 20 seconds in milliseconds
    </script>
</body>

</html>