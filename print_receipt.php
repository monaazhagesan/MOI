<?php
include "config.php";
include "header.php";

date_default_timezone_set('Asia/Kolkata');
// Start session to access session variables
session_start();


// Assuming you store the logged-in user's ID in the session
$currentUserId = isset($_SESSION['id']) ? intval($_SESSION['id']) : null;

$festival_id = isset($_GET['festival_id']) ? intval($_GET['festival_id']) : null;
$moi_id = isset($_GET['moi_id']) ? intval($_GET['moi_id']) : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;


$receiptData = [];
$festivalData = [];
$companyData = [];
$userName = "";

// Fetch user name based on currentUserId
if ($currentUserId !== null) {
    $query = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $query->bind_param("i", $currentUserId);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $userName = $result->fetch_assoc()['username'];
    }
    $query->close();
}
// Fetch receipt data and festival details
if ($festival_id) {
    $query = $conn->prepare("
        SELECT m.*, f.name as fname, f.spouse_name as fspouse_name, f.festival_name as ffestival_name, f.place as fplace, f.date as fdate
        FROM mrg AS m
        JOIN festival AS f ON m.festival_id = f.id
        WHERE m.festival_id = ?
        ORDER BY m.id DESC
    ");
    $query->bind_param("i", $festival_id);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $receiptData = $result->fetch_assoc();

        // Check if the logged-in user ID matches the ID from the receipt data
        if ($currentUserId !== null && $currentUserId != $receiptData['user_id']) {
            echo "<p>You are not authorized to view this receipt.</p>";
            exit;
        }
    }
    $query->close();
} else {
    echo "<p>Missing Festival ID.</p>";
}

// Fetch company details
$query = $conn->prepare("SELECT * FROM company_details");
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $companyData = $result->fetch_assoc();
}
$query->close();

$conn->close();

$currentDateTime = date("Y-m-d h:i A");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
    /* General styles */
    .receipt {
            max-width: 570px;
            margin: 0 auto;
            border: 2px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            padding: 15px;
            line-height: 0.7;
            /* Adjusted padding */
        }

        .company-details {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
            margin-bottom: 10px;            
            line-height: 1.1;
            /* Reduced margin */
        }

        .logo-section img {
            max-width: 100px;
            margin-right: 20px;
            line-height: 0.7;
        }

        .company-name,
        .contact-number {
            font-size: 18px;
            margin: 0;
            text-align: right;
        }

        .festival-details p,
        .details p {
            margin: 0;
            /* No margin to reduce space */
            padding: 2px 0;
            /* Minimal padding */
            font-size: 16px;
        }

        .details {
            margin-bottom: 10px;
            /* Reduced margin */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            line-height: 0.7;
            /* Reduced margin */
        }

        thead th {
            text-align: center;
            border-bottom: 2px solid #000;
            font-size: 16px;
            padding: 5px 0;
            line-height: 0.7;
            /* Reduced padding */
        }

        td {
            padding: 5px;
            /* Reduced padding */
            font-size: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            line-height: 0.7;
        }

        input.quantity {
            width: 100%;
            border: none;
            background-color: #fff;
            text-align: center;
        }

        .total p {
            font-size: 22px;
            text-align: right;
            margin: 0;
            line-height: 0.7;
            /* No margin */
        }

        .print-button {
            text-align: center;
            margin-top: 10px;
            /* Reduced margin */
        }

        @media print {
            .print-button {
                display: none;
            }
        }
</style>
</head>
<body>
<div class="receipt">
    <?php if (!empty($companyData)): ?>
        <div class="company-details">
            <div class="logo-section">
                <img src="img/img.jpeg" alt="Company Logo">
            </div>
            <div>
                <p class="company-name">
                    <strong><?php echo htmlspecialchars($companyData['company_name']); ?></strong>
                </p>
                <p class="contact-number">
                    <strong><?php echo htmlspecialchars($companyData['contact_number']); ?></strong>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <hr />

    <?php if (!empty($receiptData)): ?>
        <div class="festival-details">
            <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($receiptData['fname']); ?> <?php echo htmlspecialchars($receiptData['fspouse_name']); ?></strong></p><br>
            <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($receiptData['ffestival_name']); ?></strong></p><br>
            <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($receiptData['fplace']); ?></strong></p><br>
            <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($receiptData['fdate']); ?></strong></p><br>
        </div>

        <hr />

        <div class="details">
            <p style="line-height: 0.9;">
                <strong>
                    <span><?php echo $currentDateTime; ?></span>&emsp;&emsp;&emsp;<span><?php echo '#'.$id; ?></span>
                </strong>
            </p><br>
            <p style="line-height: 0.9;"><?php echo htmlspecialchars($receiptData['place']); ?></p><br>
            <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($receiptData['name']); ?></strong> (<?php echo htmlspecialchars($receiptData['profession']); ?>)</p><br>

            <?php if ($receiptData['spouse_name'] != ''): ?>
                <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($receiptData['spouse_name']); ?></strong> (<?php echo htmlspecialchars($receiptData['profession1']); ?>)</p><br>
            <?php endif; ?>

            <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($receiptData['contactNumber']); ?></strong></p><br>
            <!-- <p style="line-height: 0.9;"><?php echo htmlspecialchars($receiptData['relative_name']); ?></p> -->
        </div>

        <br />

        <!-- Denomination Table -->
        <table id="amountTable">
            <thead>
                <tr>
                    <th colspan="3" style="text-align:center">Denomination</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Array of denomination values
                $denominations = [
                    '500' => $receiptData['fivehundred'],
                    '200' => $receiptData['twohundred'],
                    '100' => $receiptData['hundred'],
                    '50' => $receiptData['fiftyrupees'],
                    '20' => $receiptData['twentyrupees'],
                    '10' => $receiptData['tenrupees'],
                    '1' => $receiptData['onerupees']
                ];
                $totalAmount = 0;

                // Loop through each denomination
                foreach ($denominations as $denomination => $quantity) {
                    if ($quantity > 0) {
                        $lineTotal = $denomination * $quantity;
                        $totalAmount += $lineTotal;
                ?>
                <tr>
                    <td><?php echo $denomination; ?></td>
                    <td><?php echo $quantity; ?></td>
                    <td><?php echo number_format($lineTotal, 2); ?></td>
                </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <div class="total">
            <p><strong>ரூ. <?php echo number_format($totalAmount, 0); ?></strong></p>
        </div><br>

        <div>
            <p style="line-height: 0.5;">&emsp;<strong>எழுத்தர்:</strong> <?php echo $userName; ?></p>
        </div><br>

        <div>
            <p style="line-height: 0.5;">&emsp;தங்கள் நல்வரவுக்கு <br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;நன்றி</p>
        </div><br>

        <div class="print-button">
            <button type="button"class="btn btn-primary" onclick="printAndRedirect()">Print Receipt</button>
        </div>
    <?php else: ?>
        <p>No receipt data available.</p>
    <?php endif; ?>
</div>

    <script>
        function printAndRedirect() {
            window.print();
            setTimeout(function() {
                window.location.href = 'moi.php?moi_id=<?php echo $moi_id; ?>';
            }, 1000); 
        }
        function updateTable() {
            const rows = document.querySelectorAll('#amountTable tbody tr');
            let grandTotal = 0;

            rows.forEach(row => {
                
                const amountCell = row.querySelector('.amount');
                const quantityInput = row.querySelector('.quantity');
                const totalCell = row.querySelector('.total');

                const quantity = parseFloat(quantityInput.value) || 0;
                const amount = parseFloat(amountCell.textContent) || 0;

                if (quantity === 0) {
                    row.remove(); // Completely remove rows with quantity 0
                } else {
                    const total = quantity * amount;
                    totalCell.textContent = total.toFixed(2);
                    grandTotal += total;
                }
            });

            document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
        }

        // Perform update on page load
        updateTable();

        // Update calculations when quantity changes
        document.querySelectorAll('.quantity').forEach(p => {
            p.addEventListener('p', updateTable);
        });
        
    </script>
    
</body>    
</html>



