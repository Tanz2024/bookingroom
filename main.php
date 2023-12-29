<?php
// Database connection details for localhost
$host = "localhost";
$username = "your_mysql_username";
$password = "your_mysql_password";
$database = "your_mysql_database";

try {
    // Create a database connection using prepared statements
    $conn = new mysqli($host, $username, $password, $database);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Ensure the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $id = intval($_POST["id"]); // Assuming you have a hidden input field for the ID
        $first_name = htmlspecialchars($_POST["first_name"]);
        $last_name = htmlspecialchars($_POST["last_name"]);
        $arrival_date = htmlspecialchars($_POST["arrival_date"]);
        $departure_date = htmlspecialchars($_POST["departure_date"]);
        $email = htmlspecialchars($_POST["email"]);
        $phone_number = htmlspecialchars($_POST["phone_number"]);
        $ic_passport = htmlspecialchars($_POST["ic_passport"]);
        $adults = intval($_POST["adults"]);
        $children = intval($_POST["children"]);
        $room_preference = htmlspecialchars($_POST["room_preference"]);

        // Validate form data (add more validation as needed)
        if (empty($first_name) || empty($last_name) || empty($arrival_date) || empty($departure_date) || empty($email) || empty($phone_number) || empty($ic_passport) || $adults <= 0 || $children < 0) {
            // Handle validation errors (redirect or display an error message)
            header("Location: error.php");
            exit();
        }

        // Update operation
        $update_sql = "UPDATE your_table_name 
                       SET first_name=?, last_name=?, arrival_date=?, departure_date=?, email=?, phone_number=?, ic_passport=?, adults=?, children=?, room_preference=?
                       WHERE id=?";

        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssssiisi", $first_name, $last_name, $arrival_date, $departure_date, $email, $phone_number, $ic_passport, $adults, $children, $room_preference, $id);

        if ($update_stmt->execute()) {
            // Record updated successfully
            header("Location: success.php");
            exit();
        } else {
            throw new Exception("Error updating data: " . $update_stmt->error);
        }
    }

    // Delete (mark as inactive) operation
    if (isset($_POST['delete'])) {
        $id_to_delete = intval($_POST["id"]); // Assuming you have a hidden input field for the ID

        $delete_sql = "UPDATE your_table_name SET status = 'inactive' WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id_to_delete);

        if ($delete_stmt->execute()) {
            // Record marked as inactive successfully
            header("Location: success.php");
            exit();
        } else {
            throw new Exception("Error marking record as inactive: " . $delete_stmt->error);
        }
    }

    // Retrieve (Read) operation
    $select_sql = "SELECT * FROM your_table_name";
    $result = $conn->query($select_sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row["id"] . " - Name: " . $row["first_name"] . " " . $row["last_name"] . "<br>";
            // Output other fields as needed
            echo '<form method="post" action="your_php_script.php">
                      <input type="hidden" name="id" value="' . $row["id"] . '">
                      <input type="submit" name="delete" value="Delete">
                  </form>';
        }
    } else {
        echo "No records found";
    }
} catch (Exception $e) {
    // Handle exceptions (redirect or display an error message)
    header("Location: error.php");
    exit();
} finally {
    // Close the database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>
