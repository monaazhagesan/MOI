<?php
require 'config.php';
include "header.php";

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the data for the record to be edited
    $query = "SELECT * FROM festival WHERE id='$id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Record not found.'); window.location.href='display.php';</script>";
        exit;
    }
}

if(isset($_POST["submit"])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $spouse_name = $_POST['spouse_name'];
    $occupation = $_POST['occupation'];
    $festival_name = $_POST['festival_name'];
    $date = $_POST['date'];
    $place = $_POST['place'];

    $update = "UPDATE festival SET name='$name', spouse_name='$spouse_name', occupation='$occupation', festival_name='$festival_name', date='$date', place='$place' WHERE id='$id'";

    if(mysqli_query($conn, $update)){
        echo "<script>alert('Updated successfully!'); window.location.href='display.php';</script>";
    } else {
        echo "<script>alert('Update failed.'); window.location.href='edit.php?id=$id';</script>";
    }
}
?>
<style>
    .container-moi {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
</style>
<body>
    <div class="container-moi mt-5">
    <?php
            $id=$_GET['edit_id'];
                $res = mysqli_query($conn, "SELECT * FROM festival where id='$id'");
                 while($row = mysqli_fetch_assoc($res)) {
            ?>
        <h2 class="mb-4 text-center">விழாக்கள்</h2>
        <form action="edit.php?id=<?php echo $id; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">பெயர்:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="spouse_name" class="form-label">துணைவி பெயர்:</label>
                <input type="text" class="form-control" id="spouse_name" name="spouse_name" value="<?php echo $row['spouse_name'] ?>">
            </div>
            <div class="mb-3">
                <label for="occupation" class="form-label">தொழில்:</label>
                <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo $row['occupation'] ?>">
            </div>
            <div class="mb-3">
                <label for="festival_name" class="form-label">விழாவின் பெயர்:</label>
                <input type="text" class="form-control" id="festival_name" name="festival_name" value="<?php echo $row['festival_name'] ?>">
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">தேதி:</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $row['date'] ?>">
            </div>
            <div class="mb-3">
                <label for="place" class="form-label">இடம்:</label>
                <input type="text" class="form-control" id="place" name="place" value="<?php echo $row['place'] ?>">
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">சமர்ப்பிக்கவும்</button>
        </form>
        <?php
                    }
                ?>
    </div>
</body>
</html>
