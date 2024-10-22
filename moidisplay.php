<?php
include "config.php";
include "header.php";

$festival_id = $_GET['festival_id'] ?? null;
$name = $_GET['name'] ?? null;
$contactNumber = $_GET['contactNumber'] ?? null;
$id = $_GET['id'] ?? null;

$_SESSION['is_admin'] = ($user_role == 'admin');

// Base query for selecting records
$query = "SELECT * FROM mrg WHERE festival_id = $festival_id";

// Adding search filters to the query
if ($id) {
    $query .= " AND id LIKE '%$id%'";
}
if ($name) {
    $query .= " AND name LIKE '%$name%'";
}
if ($contactNumber) {
    $query .= " AND contactNumber LIKE '%$contactNumber%'";
}

$query .= " ORDER BY id DESC;";
$res = mysqli_query($conn, $query);

// Fetch admin details
$admin_res = mysqli_query($conn, "SELECT * FROM company_details");
$admin = mysqli_fetch_assoc($admin_res);

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

// If no records are found, default values should be set to 0
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
<style>
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
</style>

<div class="container mt-3">
    <br />
    <button class="btn btn-primary" onclick="window.location.href='generate_pdf.php?festival_id=<?php echo $festival_id; ?>'">
        Generate and Download PDF
    </button>

    <button class="btn btn-success" onclick="window.location.href='generate_csv.php?festival_id=<?php echo $festival_id; ?>'">
        Generate and Download CSV
    </button>

    <br />
    <h3 class="text-center mb-3">DETAILS</h3>
    <br />
    <form method="GET">
        <input type="hidden" name="festival_id" value="<?php echo $festival_id; ?>">
        <div class="row mb-3">
            <div class="col">
                <input type="text" name="id" class="form-control" placeholder="Search by ID" value="<?php echo $id; ?>">
            </div>
            <div class="col">
                <input type="text" name="name" class="form-control" placeholder="Search by Name" value="<?php echo $name; ?>">
            </div>
            <div class="col">
                <input type="text" name="contactNumber" class="form-control" placeholder="Search by Mobile Number" value="<?php echo $contactNumber; ?>">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>
    <br />
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">NAME</th>
                <th scope="col">SPOUSE_NAME</th>
                <th scope="col">Relationship</th>
                <th scope="col">contactNumber</th>
                <th scope="col">PLACE</th>
                <th scope="col">amount</th>
                <th scope="col">ACTION</th>
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
                        <td><?php echo $row['relative_name']; ?></td>
                        <td><?php echo $row['contactNumber']; ?></td>
                        <td><?php echo $row['place']; ?></td>
                        <td><?php echo $row['amount']; ?></td>
                        <td>
                            <button onclick="openEditUserModal(<?php echo $row['id']; ?>)" class='btn btn-warning btn-sm'>Edit</button>
                            <?php if ($role === 'admin') { ?>
                                <a href="delete_moi.php?festival_id=<?php echo $festival_id; ?>&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            <?php } ?>
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
    <div class="form-group result text-right mt-3">
        <!-- Table for Denominations Count -->
        <!-- <strong>Denominations Count for Festival ID <?php //echo $festival_id; 
                                                            ?>:</strong><br><br> -->
        <table class="table table-bordered">
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
        </table>

        <!-- Total amount for the same festival_id -->
        <br>
        <strong>நிகழ்வின் மொத்த தொகை:</strong> <span id="festival_total"><?php echo $total_amount; ?></span>
        ரூபாய்
    </div>
</div>
</body>

</html>
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="edit_moi.php" method="POST">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="hidden" name="user_id" id="edit-uid">
                    <input type="hidden" name="festival_id" id="edit-festival-id" value="<?php echo $festival_id; ?>">
                    <input type="hidden" name="language" value="tamil"/>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="edit-name" class="form-label">பெயர் <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit-name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-profession" class="form-label">தொழில்</label>
                                    <input type="text" class="form-control" id="edit-profession" name="profession">
                                </div>
                                <div class="mb-3">
                                    <label for="edit-spouse-name" class="form-label">துணைவி பெயர்</label>
                                    <input type="text" class="form-control" id="edit-spouse-name" name="spouse_name">
                                </div>
                                <div class="mb-3">
                                    <label for="edit-profession1" class="form-label">தொழில்</label>
                                    <input type="text" class="form-control" id="edit-profession1" name="profession1">
                                </div>
                            </div>
                            <div class="col-6">  
                                <div class="form-group">
                                    <label for="edit-relative-name" style="font-weight: normal;">உறவுமுறை பெயர்</label>
                                    <div class="input-group">
                                        <select class="form-control custom-select-with-icon" id="relative_name" name="relative_name" onchange="checkOtherOption()">
                                            <option value="" disabled selected>உறவுமுறை தேர்வு செய்க</option> <!-- Placeholder option -->
                                            <option value="உறவுமுறை">உறவுமுறை</option>
                                            <option value="தாய்மாமன்">தாய்மாமன்</option>
                                            <option value="other">மற்றவை</option>
                                        </select>
                                    </div><br>
                                    <div class="form-group" id="otherRelativeInput" style="display: none; font-weight: normal;">
                                        <label for="otherRelative" style="font-weight: normal;" >மேலும் தகவல்</label>
                                        <select class="form-control custom-select-with-icon" id="" name="relative_name" onchange="checkOtherOption()">
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
                                </div>

                                <div class="mb-3">
                                    <label for="edit-place" class="form-label">ஊர் <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit-place" name="place" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-contactnumber" class="form-label">தொடர்புஎண்</label>
                                    <input type="text" class="form-control" id="edit-contactnumber" name="contactNumber">
                                </div>
                                <div class="mb-3">
                                    <label for="edit-amount" class="form-label">மொத்தம்: ரூபாய் <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit-amount" name="amount" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button> &emsp;&emsp;

                        <button type="button" class="btn btn-secondary print-button" onclick="printReceipt()">
                            Print
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    var adminContactNumber = '<?php echo $admin['contact_number']; ?>';
    var adminCompanyName = '<?php echo $admin['company_name']; ?>';

    function openEditUserModal(id) {
        fetch('get_moi.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-uid').value = data.id;
                document.getElementById('edit-name').value = data.name;
                document.getElementById('edit-spouse-name').value = data.spouse_name;
                document.getElementById('edit-profession').value = data.profession;
                document.getElementById('edit-profession1').value = data.profession1;
                // document.getElementById('edit-relative-name').value = data.relative_name;
                document.getElementById('edit-contactnumber').value = data.contactNumber;
                document.getElementById('edit-place').value = data.place;
                document.getElementById('edit-amount').value = data.amount;

                // Show/hide the relative name select based on fetched data
                checkOtherOption(data.relative_name); // Pass the selected value to checkOtherOption

                var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editUserModal.show();
            })
            .catch(error => console.error('Error:', error));
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

    function printReceipt() {
        var id = document.getElementById('edit-id').value;
        var name = document.getElementById('edit-name').value;
        var profession = document.getElementById('edit-profession').value;
        var spouseName = document.getElementById('edit-spouse-name').value;
        var profession1 = document.getElementById('edit-profession1').value;
        // var relativeName = document.getElementById('edit-relative-name').value;
        var place = document.getElementById('edit-place').value;
        var contactNumber = document.getElementById('edit-contactnumber').value;
        var amount = document.getElementById('edit-amount').value;
        var festivalId = '<?php echo $festival_id; ?>';

        // Construct the URL for print_receipt.php
        var url = 'edit_print_receipt.php?id=' + encodeURIComponent(id) +
            '&name=' + encodeURIComponent(name) +
            '&profession=' + encodeURIComponent(profession) +
            '&spouse_name=' + encodeURIComponent(spouseName) +
            '&profession1=' + encodeURIComponent(profession1) +
            // '&relative_name=' + encodeURIComponent(relativeName) +
            '&place=' + encodeURIComponent(place) +
            '&contact_number=' + encodeURIComponent(contactNumber) +
            '&amount=' + encodeURIComponent(amount) +
            '&festival_id=' + encodeURIComponent(festivalId);

        window.open(url, '_blank');
    }
</script>
<SCRIPT language=JavaScript src="assets/js/utf.js"></SCRIPT>
    <SCRIPT language=JavaScript src="assets/js/tamil.js"></SCRIPT>
    <script type="text/javascript" src="assets/js/jquery.js"></script>
    <script>
        $(document).on('keypress', '.convertLang', function(event) {
            if ($('input[name="language"]').val() == 'tamil') {
                convertThis(event);
            }
        });
        // on click shortcut (ctrl+l) change language
        // Listen for the keydown event to detect Ctrl+L
        $(document).on('keydown', function(event) {
            console.log(event.key);
            if (event.ctrlKey && event.key === 'l') {
                event.preventDefault(); // Prevent default browser action for Ctrl+L
                //change language radio
                if ($('input[name="language"]').val() == 'tamil') {
                    $('input[name="language"]').val('english');
                } else {
                    $('input[name="language"]').val('tamil');
                }
            }
        });
        // if control s is pressed trigger save button
        $(document).on('keydown', function(event) {
            if (event.ctrlKey && event.key === 's') {
                event.preventDefault(); // Prevent default browser action for Ctrl+S
                //trigger save button
                $('#update').trigger('click');
            }
        });

        // if control p is pressed trigger print button
        $(document).on('keydown', function(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault(); // Prevent default browser action for Ctrl+P
                //trigger print button
                $('#print').trigger('click');
            }
        });
    </script>
    <style>
        /* #HelpDiv {
            display: none !important;
        } */
    </style>


<?php include('footer.php'); ?>