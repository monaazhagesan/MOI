<?php
include "config.php";
include 'header.php'; 

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$uid = $_SESSION['id'];

if ($role === "admin") {
    // Admin: Retrieve all festivals
    $res = mysqli_query($conn, "SELECT * FROM festival where status = 1;");
    
} 

?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Page</title>
    <!-- Include Bootstrap CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    /* Add your existing styles here */
</style>
<body>
<div class="container mt-5">
    <br/>
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
                <th scope="col">NAME</th>  
                <th scope="col">SPOUSE_NAME</th>
                <th scope="col">OCCUPATION</th>
                <th scope="col">FESTIVAL_NAME</th>
                <th scope="col">DATE</th>
                <th scope="col">PLACE</th>
                <th scope="col">ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $counter = 1;
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
            ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo $row['name'];?></td>
                    <td><?php echo $row['spouse_name'];?></td>
                    <td><?php echo $row['occupation'];?></td>
                    <td><?php echo $row['festival_name'];?></td>
                    <td><?php echo $row['date'];?></td>
                    <td><?php echo $row['place'];?></td>

                    <td>
                        <?php if ($role === "admin") { ?>
                            <a href="edit.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-info">Edit</a>
                            <a href="display.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>                        
                        <?php } ?>
                        <a href="moidisplay.php?festival_id=<?php echo $row['id']; ?>" class="btn btn-warning">List</a> 
                    </td>
                </tr>
            <?php
                    }
                } else {
            ?>
                <tr>
                    <td colspan="8">No data available</td>
                </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>

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
                    while($user_row = mysqli_fetch_assoc($user_res)) { ?>
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

<?php include 'footer.php'; ?>
