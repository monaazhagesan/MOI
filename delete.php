<?php
include "config.php";

if(isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    $delete_query = "DELETE FROM festival WHERE id = $delete_id";
    $result = mysqli_query($conn, $delete_query);
    
    if($result) {
        echo "<script>alert('Record deleted successfully');</script>";
        echo '<script>window.location.replace("display.php");</script>';
        exit;
    } else {
        echo "<script>alert('Error deleting record');</script>";
        echo '<script>window.location.replace("display.php");</script>';
        exit;
    }
}
    