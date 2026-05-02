<?php
/**
 * Cleanup script to automatically delete expired food items from surplus_food table
 * This script should be run periodically via cron job or scheduled task
 */

include 'config.php';

// Get current timestamp
$current_time = date('Y-m-d H:i:s');

echo "Starting cleanup process at: " . date('Y-m-d H:i:s') . "\n";

// Find expired items
$sql = "SELECT id, food_name, expiry_time FROM surplus_food WHERE expiry_time < '$current_time'";
$result = mysqli_query($conn, $sql);

$expired_count = 0;
$deleted_items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $expired_count++;
    $deleted_items[] = $row['food_name'] . " (ID: " . $row['id'] . ", Expired: " . $row['expiry_time'] . ")";
}

// Delete expired items
if ($expired_count > 0) {
    $delete_sql = "DELETE FROM surplus_food WHERE expiry_time < '$current_time'";
    if (mysqli_query($conn, $delete_sql)) {
        echo "Successfully deleted $expired_count expired food items:\n";
        foreach ($deleted_items as $item) {
            echo "- $item\n";
        }
    } else {
        echo "Error deleting expired items: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "No expired food items found.\n";
}

// Also clean up related orders for deleted food items (optional - depends on business logic)
// You might want to mark orders as cancelled instead of deleting them
// For now, we'll leave orders as is since they represent historical transactions

echo "Cleanup process completed at: " . date('Y-m-d H:i:s') . "\n";
echo "----------------------------------------\n";

mysqli_close($conn);
?>