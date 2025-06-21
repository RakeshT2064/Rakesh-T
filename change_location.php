<?php
session_start();
if (isset($_POST['city'])) {
    $_SESSION['selected_city'] = $_POST['city'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>