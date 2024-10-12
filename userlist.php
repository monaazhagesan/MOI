<?php
$page_title = "User List";
include('user_index.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "moi";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, username, mobile_number, status FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>User List</h2>
        <!-- Button to download user list as PDF -->
        <a href="download_user_pdf.php" class="btn btn-success">Download PDF</a>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Mobile Number</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['mobile_number']) . "</td>";
                    echo "<td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>";
                    echo "<td>
                        <button onclick='openEditUserModal(" . $row['id'] . ")' class='btn btn-warning btn-sm'>Edit</button>
                        <a href='delete_user.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</a>
                        <a href='toggle_user.php?id=" . $row['id'] . "' class='btn btn-secondary btn-sm'>" . ($row['status'] == 1 ? 'Deactivate' : 'Activate') . "</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No users found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" action="edit_user.php" method="post">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_mobile_number" class="form-label">Mobile Number</label>
                        <input type="text" name="mobile_number" id="edit_mobile_number" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    function openEditUserModal(id) {
        fetch('get_user.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                // Populate the form fields
                document.getElementById('edit_user_id').value = data.id;
                document.getElementById('edit_username').value = data.username;
                document.getElementById('edit_mobile_number').value = data.mobile_number;

                // Show the modal
                var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editUserModal.show();
            })
            .catch(error => console.error('Error:', error));
    }
</script>
</body>

</html>

<?php
$conn->close();
?>