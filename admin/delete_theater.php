<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$theater_id = $_GET['id'];

// Delete theater
$stmt = $pdo->prepare("DELETE FROM theaters WHERE theater_id = ?");
$stmt->execute([$theater_id]);

header("Location: manage_theaters.php");
exit();
?>