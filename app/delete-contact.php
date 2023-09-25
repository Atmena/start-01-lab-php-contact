<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['contact_id'])) {
    $contact_id = $_GET['contact_id'];

    $host = 'db';
    $dbname = getenv('MYSQL_DATABASE');
    $username = getenv('MYSQL_USER');
    $passwd = getenv('MYSQL_PASSWORD');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwd);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }

    $sql = "DELETE FROM contact WHERE idcontact = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$contact_id]);

    header("Location: dashbord.php");
    exit;
}
?>