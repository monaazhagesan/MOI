<?php
include "config.php";

if (isset($_POST['contactNumber'])) {
    $contactNumber = $_POST['contactNumber'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT name, profession, spouse_name, profession1, relative_name, other_relative , place FROM mrg WHERE contactNumber = ?");
    $stmt->bind_param("s", $contactNumber);
    $stmt->execute();
    $stmt->store_result();

    $response = array("exists" => false);

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($name, $profession, $spouse_name, $profession1, $relative_name, $other_relative , $place);
        $stmt->fetch();

        $response = array(
            "exists" => true,
            "name" => $name,
            "profession" => $profession,
            "spouse_name" => $spouse_name,
            "profession1" => $profession1,
            "relative_name" => $relative_name,
            "other_relative" => $other_relative,
            "place" => $place
        );
    }

    $stmt->close();
    $conn->close();

    // Send response in JSON format
    echo json_encode($response);
}
?>
