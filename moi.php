<?php
include "config.php"; // Include your database connection
include "header.php"; // Include your header
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

// Get the moi_id from the URL, if available
$moi_id = isset($_GET['moi_id']) ? intval($_GET['moi_id']) : null;

if (isset($_POST["submit"])) {
    // Retrieve form inputs with default values
    $contactNumber = $_POST['contactNumber'] ?? '';
    $name = $_POST['name'] ?? '';
    $profession = $_POST['profession'] ?? '';
    $spouse_name = $_POST['spouse_name'] ?? '';
    $profession1 = $_POST['profession1'] ?? '';
    $relative_name = $_POST['relative_name'] ?? '';
    $other_relative = $_POST['other_relative'] ?? ''; // Handle undefined index
    $place = $_POST['place'] ?? '';

    // Retrieve the amounts as integers, defaulting to 0
    $fivehundred = (int) ($_POST['fivehundred'] ?? 0);
    $twohundred = (int) ($_POST['twohundred'] ?? 0);
    $hundred = (int) ($_POST['hundred'] ?? 0);
    $fiftyrupees = (int) ($_POST['fiftyrupees'] ?? 0);
    $twentyrupees = (int) ($_POST['twentyrupees'] ?? 0);
    $tenrupee = (int) ($_POST['tenrupee'] ?? 0);
    $onerupee = (int) ($_POST['onerupee'] ?? 0);
    $amount = (int) ($_POST['amount'] ?? 0);

    // Validate festival_id
    if (isset($_POST['festival_id']) && is_numeric($_POST['festival_id']) && $_POST['festival_id'] > 0) {
        $festival_id = (int) $_POST['festival_id'];
    } else {
        echo '<script>alert("Missing or invalid Festival ID."); window.location.href="moi.php";</script>';
        exit();
    }

    // Retrieve user_id from session
    $user_id = $_SESSION['id'] ?? null;

  // Check if at least one rupee denomination is entered
  if ($fivehundred > 0 || $twohundred > 0 || $hundred > 0 || $fiftyrupees > 0 || $twentyrupees > 0 || $tenrupee > 0 || $onerupee > 0) {
    $query = $conn->prepare("INSERT INTO mrg(contactNumber, name, profession, spouse_name, profession1, relative_name, place, fivehundred, twohundred, hundred, fiftyrupees, twentyrupees, tenrupee, onerupee, amount, user_id, festival_id , other_relative) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?)");

    // Updated bind_param with correct number of 's' and 'i'
    $query->bind_param("ssssssssssssssiiis", $contactNumber, $name, $profession, $spouse_name, $profession1, $relative_name, $place, $fivehundred, $twohundred, $hundred, $fiftyrupees, $twentyrupees, $tenrupee, $onerupee, $amount, $user_id, $festival_id , $other_relative);
    
    // Execute and handle result
    try {
        if ($query->execute()) {
            $inserted_id = $conn->insert_id;
            // Redirect to print_receipt.php with festival_id and moi_id
            header("Location: print_receipt.php?festival_id=$festival_id&moi_id=$moi_id&id=$inserted_id");
            exit();
        } else {
            // Show error message if insertion fails
            $error_message = $query->error;
            echo '<script>alert("Registration failed. Error: '.$error_message.'"); window.location.href="moi.php";</script>';
        }
    } catch (\Throwable $th) {
        // Handle any unexpected errors
        $error_message = $query->error;
        echo '<script>alert("Registration failed. Error: '.$error_message.'"); window.location.href="moi.php";</script>';
    }

    // Close query and connection
    $query->close();
    $conn->close();
} else {
    // Alert for at least one rupee denomination
    echo '<script>alert("Please enter at least one rupee denomination!"); window.location.href="moi.php";</script>';
}
}



// Fetch company details for display
$admin_res = mysqli_query($conn, "SELECT * FROM company_details");
$admin = mysqli_fetch_assoc($admin_res);
$currentUsername = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$festival_id = isset($_GET['moi_id']) ? intval($_GET['moi_id']) : 0;

// SQL Query to sum the total amount for the selected festival_id
$sql = "SELECT 
    (SUM(fivehundred) * 500) + 
    (SUM(twohundred) * 200) + 
    (SUM(hundred) * 100) + 
    (SUM(fiftyrupees) * 50) + 
    (SUM(twentyrupees) * 20) + 
    (SUM(tenrupee) * 10) + 
    (SUM(onerupee) * 1) AS total_amount,
    SUM(fivehundred) AS total_fivehundred, 
    SUM(twohundred) AS total_twohundred, 
    SUM(hundred) AS total_hundred, 
    SUM(fiftyrupees) AS total_fiftyrupees, 
    SUM(twentyrupees) AS total_twentyrupees, 
    SUM(tenrupee) AS total_tenrupee, 
    SUM(onerupee) AS total_onerupee 
FROM mrg 
WHERE festival_id = ?";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $festival_id);
$stmt->execute();
$stmt->bind_result($total_amount, $total_fivehundred, $total_twohundred, $total_hundred, $total_fiftyrupees, $total_twentyrupees, $total_tenrupee, $total_onerupee);
$stmt->fetch();
$stmt->close();

// Set default values to 0 if no records found
if ($total_amount === null) {
    $total_amount = 0;
    $total_fivehundred = 0;
    $total_twohundred = 0;
    $total_hundred = 0;
    $total_fiftyrupees = 0;
    $total_twentyrupees = 0;
    $total_tenrupee = 0;
    $total_onerupee = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form in Tamil</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styles for screen (default) */
        .container-moi {
            max-width: 800px;
            margin: auto;
            padding: 10px;
            border: 2px solid #007bff;
            border-radius: 10px;
            background-color: #f8f9fa;
        }

        h2 {
            margin-bottom: 20px;
            color: #007bff;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input[type="number"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .divider {
            margin: 20px 0;
            border-top: 2px solid #007bff;
        }

        .result {
            font-size: 18px;
            color: #007bff;
            font-weight: bold;
            text-align: right;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-moi">
        <h2 class="text-center">தகவலுக்கு விண்ணப்பம்</h2>
        <form action="#" method="POST">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <input type="hidden" name="festival_id" value="<?php echo $moi_id; ?>">

                    <div class="form-group">
                        <label for="contactNumber">தொடர்புஎண்</label>
                        <input type="text" class="form-control" id="contactNumber" name="contactNumber" onblur="checkMobileNumber()">
                    </div>
                    <div class="form-group">
                        <label for="name">பெயர்</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="profession">தொழில்</label>
                        <input type="text" class="form-control" id="profession" name="profession">
                    </div>
                    <div class="form-group">
                        <label for="spouse_name">துணைவி பெயர்</label>
                        <input type="text" class="form-control" id="spouse_name" name="spouse_name">
                    </div>
                    <div class="form-group">
                        <label for="profession1">தொழில்</label>
                        <input type="text" class="form-control" id="profession1" name="profession1">
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <label for="relative_name">உறவுமுறை பெயர்</label>
                            <div class="input-group">
                                <select class="form-control custom-select-with-icon" id="relative_name" name="relative_name" onchange="checkOtherOption()">
                                    <option value="" disabled selected>உறவுமுறை தேர்வு செய்க</option> <!-- Placeholder option -->
                                    <option value="உறவுமுறை">உறவுமுறை</option>
                                    <option value="தாய்மாமன்">தாய்மாமன்</option>
                                    <option value="other">மற்றவை</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="otherRelativeInput" style="display: none;">
                            <label for="otherRelative">மேலும் தகவல்</label>
                            <select class="form-control custom-select-with-icon" id="   " name="relative_name" onchange="checkOtherOption()">
                                <option value="" disabled selected>உறவுமுறை தேர்வு செய்க</option>
                                <option value="அப்பா (தாய்மாமன்)">அப்பா (தாய்மாமன்)</option>
                                <option value="அம்மா (தாய்மாமன்)">அம்மா (தாய்மாமன்)</option>
                                <option value="சகோதரர் (தாய்மாமன்)">சகோதரர் (தாய்மாமன்)</option>
                                <option value="சகோதரி (தாய்மாமன்)">சகோதரி (தாய்மாமன்)</option>
                                <option value="மாமா (தாய்மாமன்)">மாமா (தாய்மாமன்)</option>
                                <option value="மாமி (தாய்மாமன்)">மாமி (தாய்மாமன்)</option>
                                <option value="அண்ணன் (தாய்மாமன்)">அண்ணன் (தாய்மாமன்)</option>
                                <option value="அக்கா (தாய்மாமன்)">அக்கா (தாய்மாமன்)</option>
                                <option value="தங்கை (தாய்மாமன்)">தங்கை (தாய்மாமன்)</option>
                                <option value="தம்பி (தாய்மாமன்)">தம்பி (தாய்மாமன்)</option>
                                <option value="other">மற்றவை</option>
                            </select>
                        </div>

                        <style>
                            /* Add custom icon to the select box */
                            .custom-select-with-icon {
                                appearance: none;
                                background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16"><path d="M7.247 11.14l-4.796-5.481c-.566-.648-.106-1.659.796-1.659h9.608c.902 0 1.362 1.01.796 1.659l-4.796 5.481a1 1 0 0 1-1.408 0z"/></svg>') no-repeat right 10px center;
                                background-size: 16px 16px;
                                padding-right: 30px;
                            }
                        </style>

                    </div>

                    <style>
                        /* Add custom icon to the select box */
                        .custom-select-with-icon {
                            appearance: none;
                            background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16"><path d="M7.247 11.14l-4.796-5.481c-.566-.648-.106-1.659.796-1.659h9.608c.902 0 1.362 1.01.796 1.659l-4.796 5.481a1 1 0 0 1-1.408 0z"/></svg>') no-repeat right 10px center;
                            background-size: 16px 16px;
                            padding-right: 30px;
                        }
                    </style>

                    <div class="form-group">
                        <label for="place">ஊர்</label>
                        <input type="text" class="form-control" id="place" name="place" required>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fivehundred">500 ரூபாய்:</label>
                        <input type="number" id="fivehundred" name="fivehundred" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="twohundred">200 ரூபாய்:</label>
                        <input type="number" id="twohundred" name="twohundred" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="hundred">100 ரூபாய்:</label>
                        <input type="number" id="hundred" name="hundred" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="fiftyrupees">50 ரூபாய்:</label>
                        <input type="number" id="fiftyrupees" name="fiftyrupees" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="twentyrupees">20 ரூபாய்:</label>
                        <input type="number" id="twentyrupees" name="twentyrupees" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="tenrupee">10 ரூபாய்:</label>
                        <input type="number" id="tenrupee" name="tenrupee" min="0" oninput="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="onerupee">1 ரூபாய்:</label>
                        <input type="number" id="onerupee" name="onerupee" min="0" oninput="calculateTotal()">
                    </div>

                    <input type="hidden" id="amount" name="amount" value=""><br>
                    <div class="divider"></div>
                    <div class="form-group result">
                        மொத்த தொகை: <span id="total">0</span> ரூபாய்
                    </div>
                </div>
                <div class="form-group result text-right mt-3">
                    <!-- Table for Denominations Count -->
                    <!-- <strong>Denominations Count for Festival ID <?php echo $festival_id; ?>:</strong><br><br> -->
                    <!-- <table class="table table-bordered">
        <thead>
            <tr>
                <th>Denomination</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>500 ரூபாய்</td>
                <td><?php echo $total_fivehundred; ?></td>
            </tr>
            <tr>
                <td>200 ரூபாய்</td>
                <td><?php echo $total_twohundred; ?></td>
            </tr>
            <tr>
                <td>100 ரூபாய்</td>
                <td><?php echo $total_hundred; ?></td>
            </tr>
            <tr>
                <td>50 ரூபாய்</td>
                <td><?php echo $total_fiftyrupees; ?></td>
            </tr>
            <tr>
                <td>20 ரூபாய்</td>
                <td><?php echo $total_twentyrupees; ?></td>
            </tr>
            <tr>
                <td>10 ரூபாய்</td>
                <td><?php echo $total_tenrupee; ?></td>
            </tr>
            <tr>
                <td>1 ரூபாய்</td>
                <td><?php echo $total_onerupee; ?></td>
            </tr>
        </tbody>
    </table> -->

                    <!-- Total amount for the same festival_id -->
                    <strong>நிகழ்வின் மொத்த தொகை:</strong> <span id="festival_total"><?php echo $total_amount; ?></span> ரூபாய்
                </div>
                <div class="d-flex">
                    <button type="submit" name="submit" class="btn btn-primary btn-block"
                        style="width: 25%;">சமர்ப்பிக்கவும்</button> &emsp;&emsp;
                    <!-- <button type="button" class="btn btn-secondary print-button" onclick="printForm()"
                        style="width: 25%;">Print</button> -->
                </div>

            </div>

        </form>
    </div>

    <script>
        function calculateTotal() {
            var fivehundred = parseInt(document.getElementById('fivehundred').value) || 0;
            var twohundred = parseInt(document.getElementById('twohundred').value) || 0;
            var hundred = parseInt(document.getElementById('hundred').value) || 0;
            var fiftyrupees = parseInt(document.getElementById('fiftyrupees').value) || 0;
            var twentyrupees = parseInt(document.getElementById('twentyrupees').value) || 0;
            var tenrupee = parseInt(document.getElementById('tenrupee').value) || 0;
            var onerupee = parseInt(document.getElementById('onerupee').value) || 0;

            var total = (fivehundred * 500) + (twohundred * 200) + (hundred * 100) +
                (fiftyrupees * 50) + (twentyrupees * 20) + (tenrupee * 10) + (onerupee * 1);

            document.getElementById('total').innerText = total;
            document.getElementById('amount').value = total;

            // Get the existing total amount for the same festival from the backend
            var festivalTotal = parseInt(document.getElementById('festival_total').innerText) || 0;
            var newFestivalTotal = festivalTotal + total;
            document.getElementById('festival_total').innerText = newFestivalTotal;
        }

        function checkOtherOption() {
            const relativeNameSelect = document.getElementById('relative_name');
            const otherRelativeInput = document.getElementById('otherRelativeInput');
            const selectedValue = relativeNameSelect.value;

            // Show the additional input field based on selection
            if (selectedValue === 'other') {
                otherRelativeInput.style.display = 'block';
            } else {
                otherRelativeInput.style.display = 'none';
            }

            // Optionally handle 'தாய்மாமன்' case
            if (selectedValue === 'தாய்மாமன்') {
                otherRelativeInput.style.display = 'block'; // Uncomment if you want it to show on 'தாய்மாமன்'
            } else {
                otherRelativeInput.style.display = 'none'; // Hide if not selected
            }
        }

        // function checkOtherOption() {
        //     var relativeNameSelect = document.getElementById('relative_name');
        //     var otherRelativeNameDiv = document.getElementById('other_relative_name_div');

        //     if (relativeNameSelect.value === 'other') {
        //         otherRelativeNameDiv.style.display = 'block';
        //     } else {
        //         otherRelativeNameDiv.style.display = 'none';
        //     }
        // }

        function checkMobileNumber() {
            const contactNumber = document.getElementById('contactNumber').value;

            if (contactNumber.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "check_mobile_number.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.exists) {
                            document.getElementById('name').value = response.name;
                            document.getElementById('profession').value = response.profession;
                            document.getElementById('spouse_name').value = response.spouse_name;
                            document.getElementById('profession1').value = response.profession1;
                            document.getElementById('relative_name').value = response.relative_name;
                            document.getElementById('relative_name').value = response.relative_name;
                            document.getElementById('place').value = response.place;
                        } else {
                            // Clear the fields if the mobile number does not exist
                            document.getElementById('name').value = '';
                            document.getElementById('profession').value = '';
                            document.getElementById('spouse_name').value = '';
                            document.getElementById('profession1').value = '';
                            document.getElementById('relative_name').value = '';
                            document.getElementById('place').value = '';
                        }
                    }
                };

                xhr.send("contactNumber=" + contactNumber);
            }
        }

        // var adminContactNumber = '<?php //echo $admin['contact_number']; 
                                        ?>';
        // var adminCompanyName = '<?php //echo $admin['company_nsame']; 
                                    ?>';
        // var currentUsername = '<?php //echo $currentUsername; 
                                    ?>';
        // function printForm() {
        //     // Create a new window for printing
        //     const printWindow = window.open('', '', 'width=570');

        //     // Get the form element
        //     const form = document.querySelector('form'); // Change selector if needed

        //     // Admin details
        //     const adminContactNumber = '<?php //echo $admin['contact_number']; 
                                            ?>';
        //     const adminCompanyName = '<?php //echo $admin['company_name']; 
                                            ?>';

        //     // Get the current date and time
        //     const now = new Date();
        //     const currentDateTime = now.toLocaleString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });

        //     // Write HTML for the print window
        //     printWindow.document.write('<html><head><title>Print Receipt</title>');
        //     printWindow.document.write('<style>@media print { body { font-family: Arial, sans-serif; font-size: 12px; width: 80mm; } table { width: 100%; border-collapse: collapse; } td, th { border: 1px solid #000; padding: 4px; text-align: left; } }</style>');
        //     printWindow.document.write('</head><body>');
        //     printWindow.document.write('<table>');

        //     // Append rows for each field
        //     printWindow.document.write('<tr><td><strong>Date & Time:</strong></td><td>' + currentDateTime + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Name:</strong></td><td>' + form.elements['name'].value + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Spouse Name:</strong></td><td>' + form.elements['spouse_name'].value + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Profession:</strong></td><td>' + form.elements['profession'].value + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Profession1:</strong></td><td>' + form.elements['profession1'].value + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Relative Name:</strong></td><td>' + form.elements['relative_name'].value + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Place:</strong></td><td>' + form.elements['place'].value + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Contact Number:</strong></td><td>' + form.elements['contactNumber'].value + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Amount:</strong></td><td>' + form.elements['amount'].value + '</td></tr>');

        //     // Include admin details
        //     printWindow.document.write('<tr><td><strong>Admin Contact Number:</strong></td><td>' + adminContactNumber + '</td></tr>');
        //     printWindow.document.write('<tr><td><strong>Admin Company Name:</strong></td><td>' + adminCompanyName + '</td></tr>');

        //     printWindow.document.write('<tr><td><strong>Logged-in User:</strong></td><td>' + currentUsername + '</td></tr>');

        //     printWindow.document.write('</table>');
        //     printWindow.document.write('</body></html>');

        //     printWindow.document.close(); // Necessary for IE >= 10
        //     printWindow.print();
        // }
    </script>
</body>

</html>