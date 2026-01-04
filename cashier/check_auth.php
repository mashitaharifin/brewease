<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] != 2) {
    header('Location: index.php');
    exit;
}