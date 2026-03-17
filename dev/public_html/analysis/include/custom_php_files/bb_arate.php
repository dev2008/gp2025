<?php
// Ensure the page is accessed through the proper inclusion method
if(!defined('custom_page_from_inclusion')) { die(); }

// Include the various functions
require_once 'bb_functions.php'; // Assumes bb_functions.php is in the same directory. Adjust the path if needed.
require_once 'g_functions.php'; // Assumes g_functions.php is in the same directory. Adjust the path if needed.

// Connect to the database
try {
    // PDO connection should already be available as $conn
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Output the HTML div with custom styling
echo "<br />";
echo "<div class='w3-container $mycolour15 w3-round-xxlarge'>\n";

// Fetch the players data
try {
    $query = "SELECT dp_id, dp_level, dp_pot, dp_hand, dp_pos, dp_best, dp_val, arate FROM bb_dplayers WHERE 1";
    $stmt = $conn->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Call the draft_asm function from bb_functions.php to calculate arate
        $new_arate = draft_asm(
            $row['dp_id'],
            $row['dp_level'],
            $row['dp_pot'],
            $row['dp_hand'],
            $row['dp_pos'],
            $row['dp_best'],
            $row['dp_val']
        );

        // Compare the new arate with the existing one to see if an update is needed
        if (round($new_arate, 2) != round($row['arate'], 2)) {
            // Update the arate value in the database only if it has changed
            $updateQuery = "UPDATE bb_dplayers SET arate = :arate WHERE dp_id = :dp_id";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([':arate' => $new_arate, ':dp_id' => $row['dp_id']]);
        }
    }

    echo "Player ratings updated successfully.";
} catch (PDOException $e) {
    echo "Error updating player ratings: " . $e->getMessage();
}

// Close the HTML div container
echo "</div>";
?>
