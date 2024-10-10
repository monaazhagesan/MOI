<?php include 'header.php'; ?>
<div class="container mt-5">
    <?php
    // // Check if the session is set and the user is logged in
    // if (isset($_SESSION['username'])) {
    //     // Check the user's role
    //     if ($_SESSION['role'] === 'admin') {
    //         echo "<h1>Welcome,  " . htmlspecialchars($_SESSION['username']) . "</h1>";
    //     } else if ($_SESSION['role'] === 'user') {
    //         echo "<h1>Welcome, User ID: " . htmlspecialchars($_SESSION['user_id']) . "</h1>";
    //     } else {
    //         echo "<h1>Welcome!</h1>";
    //     }
    // } else {
    //     echo "<h1>Welcome, Guest!</h1>";
    // }
    // ?>
     <div class="container mt-5">
        <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
    </div>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>



