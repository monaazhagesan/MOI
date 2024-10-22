<?php
session_start();
include 'config.php'; // Include the database connection

$currentUserId = isset($_SESSION['id']) ? intval($_SESSION['id']) : null;

// Default user name to empty
$userName = "";

if ($currentUserId !== null) {
    // Fetch the current logged-in user's username
    $query = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $query->bind_param("i", $currentUserId);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $userName = $result->fetch_assoc()['username']; // Store the user's username
    }
    $query->close();
}

// Get the values from the URL parameters
$id = isset($_GET['id']) ? $_GET['id'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$profession = isset($_GET['profession']) ? $_GET['profession'] : '';
$spouse_name = isset($_GET['spouse_name']) ? $_GET['spouse_name'] : '';
$profession1 = isset($_GET['profession1']) ? $_GET['profession1'] : '';
$relative_name = isset($_GET['relative_name']) ? $_GET['relative_name'] : '';
$other_relative = isset($_GET['other_relative']) ? $_GET['other_relative'] : '';
$place = isset($_GET['place']) ? $_GET['place'] : '';
$contactNumber = isset($_GET['contactNumber']) ? $_GET['contactNumber'] : '';
$amount = isset($_GET['amount']) ? $_GET['amount'] : '';
$festival_id = isset($_GET['festival_id']) ? $_GET['festival_id'] : '';

// Fetch festival details based on festival_id
$festivalDetails = [];
if ($festival_id > 0) {
    $query = $conn->prepare("
        SELECT f.name, f.spouse_name, f.festival_name, f.place, f.date
        FROM festival AS f
        WHERE f.id = ?
    ");
    $query->bind_param("i", $festival_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $festivalDetails = $result->fetch_assoc();
    } else {
        echo "<p>No festival found with this ID.</p>";
        exit;
    }
    $query->close();
}

// Fetch company details
$query = $conn->prepare("SELECT * FROM company_details");
$query->execute();
$result = $query->get_result();
$companyData = $result->fetch_assoc();
$query->close();

// Fetch receipt denominations if applicable (e.g. fivehundred, twohundred, etc.)
$query = $conn->prepare("SELECT * FROM mrg WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$receiptData = $result->fetch_assoc();
$query->close();

$currentDateTime = date("Y-m-d h:i A");

$query = $conn->prepare("SELECT COUNT(*) AS mrg_count FROM mrg WHERE festival_id = $festival_id 
AND id >= (SELECT MIN(id) FROM mrg WHERE festival_id = $festival_id LIMIT 1) 
AND id <= $id LIMIT 1");
$query->execute();
$result = $query->get_result()->fetch_assoc();
$no = $result['mrg_count'];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt</title>
    <style>
        /* General styles */
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

        <?php if (!empty($festivalDetails)): ?>
            <div class="festival-details">
                <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($festivalDetails['name']); ?>
                        <?php echo htmlspecialchars($festivalDetails['spouse_name']); ?></strong></p><br>
                <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($festivalDetails['festival_name']); ?></strong></p><br>
                <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($festivalDetails['place']); ?></strong></p><br>
                <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($festivalDetails['date']); ?></strong></p><br>
            </div>
        <?php endif; ?>

        <hr>

        <div class="details">
            <p style="line-height: 0.9;"><strong><?php echo $currentDateTime; ?></strong>&emsp;&emsp;&emsp;<strong>#<?php echo $no; ?></strong>
            </p><br>
            <p style="line-height: 0.9;"><?php echo htmlspecialchars($place); ?></p><br>
            <p style="line-height: 0.9;">
                    <strong><?php echo htmlspecialchars($receiptData['name']); ?></strong>
                    <?php if (!empty($receiptData['profession'])): ?>
                        (<?php echo htmlspecialchars($receiptData['profession']); ?>)
                    <?php endif; ?>
                </p><br>

                <?php if (!empty($receiptData['spouse_name'])): ?>
                    <p style="line-height: 0.9;">
                        <strong><?php echo htmlspecialchars($receiptData['spouse_name']); ?></strong>
                        <?php if (!empty($receiptData['profession1'])): ?>
                            (<?php echo htmlspecialchars($receiptData['profession1']); ?>)
                        <?php endif; ?>
                    </p><br>
                <?php endif; ?>
            <p style="line-height: 0.9;"><strong><?php echo htmlspecialchars($contactNumber); ?></strong></p><br>
            <!-- <p style="line-height: 0.9;"><?php echo htmlspecialchars($relative_name); ?></p> -->
        </div>

        <hr>

        <!-- Denomination table -->
        <table id="amountTable">
            <thead>
                <tr>
                    <th colspan="3" style="text-align:center">Denomination</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($receiptData['fivehundred']) && $receiptData['fivehundred'] !== ""): ?>
                    <tr>
                        <td class="amount">500</td>
                        <td><input class="quantity" value="<?php echo htmlspecialchars($receiptData['fivehundred']); ?>"
                                disabled></td>
                        <td class="total"></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($receiptData['twohundred']) && $receiptData['twohundred'] !== ""): ?>
                    <tr>
                        <td class="amount">200</td>
                        <td><input class="quantity" value="<?php echo htmlspecialchars($receiptData['twohundred']); ?>"
                                disabled></td>
                        <td class="total"></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($receiptData['hundred']) && $receiptData['hundred'] !== ""): ?>
                    <tr>
                        <td class="amount">100</td>
                        <td><input class="quantity" value="<?php echo htmlspecialchars($receiptData['hundred']); ?>"
                                disabled></td>
                        <td class="total"></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($receiptData['fiftyrupees']) && $receiptData['fiftyrupees'] !== ""): ?>
                    <tr>
                        <td class="amount">50</td>
                        <td><input class="quantity" value="<?php echo htmlspecialchars($receiptData['fiftyrupees']); ?>"
                                disabled></td>
                        <td class="total"></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($receiptData['twentyrupees']) && $receiptData['twentyrupees'] !== ""): ?>
                    <tr>
                        <td class="amount">20</td>
                        <td><input class="quantity" value="<?php echo htmlspecialchars($receiptData['twentyrupees']); ?>"
                                disabled></td>
                        <td class="total"></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($receiptData['tenrupees']) && $receiptData['tenrupees'] !== ""): ?>
                    <tr>
                        <td class="amount">10</td>
                        <td><input class="quantity" value="<?php echo htmlspecialchars($receiptData['tenrupees']); ?>"
                                disabled></td>
                        <td class="total"></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($receiptData['onerupees']) && $receiptData['onerupees'] !== ""): ?>
                    <tr>
                        <td class="amount">1</td>
                        <td><input class="quantity" value="<?php echo htmlspecialchars($receiptData['onerupees']); ?>"
                                disabled></td>
                        <td class="total"></td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>

        <!-- Total amount -->
        <div class="total">
            <p><strong>ரூ. <?php echo number_format($amount, 0); ?></strong></p>
        </div>

        <div>
            <p style="line-height: 0.5;"><strong>எழுத்தர்:</strong> <?php echo $userName; ?></p>
        </div>
        <div>
            <p style="line-height: 0.5;">தங்கள் நல்வரவுக்கு <br><br><br>நன்றி</p>
        </div>

        <div class="print-button">
            <button type="button" class="btn btn-primary" onclick="printAndRedirect()">Print Receipt</button>
        </div>
    </div>

    <script>
        function printAndRedirect() {
            window.print();
            setTimeout(function () {
                window.location.href = 'moidisplay.php?festival_id=<?php echo $festival_id; ?>';
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
                    totalCell.textContent = parseInt(total);
                    grandTotal += total;
                }
            });

            document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
        }

        // Perform update on page load
        updateTable();
    </script>
</body>

</html>