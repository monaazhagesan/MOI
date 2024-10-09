<?php include 'header.php'; ?>
    <div class="container mt-5">
        <h2>Create New User</h2>
        <form action="create_user.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">                                        
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required>
                    </div>
                   
                    <div class="mb-3">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender:</label>
                        <select id="gender" name="gender" class="form-select" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="email_id" class="form-label">Email ID:</label>
                        <input type="email" id="email_id" name="email_id" class="form-control" required>
                    </div>
                                       
                    <div class="mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <textarea id="address" name="address" class="form-control" required></textarea>
                    </div>
                    
                </div>
                
                <div class="col-md-6">
                <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" class="form-control" required>
                    </div> 
                    <div class="mb-3">
                        <label for="mobile_number" class="form-label">Mobile Number:</label>
                        <input type="text" id="mobile_number" name="mobile_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_proof" class="form-label">ID Proof:</label>
                        <input type="file" id="id_proof" name="id_proof" class="form-control" required>
                    </div>
                    
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create User</button>
        </form>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
