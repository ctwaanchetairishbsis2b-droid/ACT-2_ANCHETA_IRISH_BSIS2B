<?php
session_start();

$conn = new mysqli("localhost", "root", "", "pink_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function h($str){
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>