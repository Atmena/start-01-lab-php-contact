<?php
session_start();

if (isset($_SESSION['user_email'])) {
    header("Location: dashbord.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $host = 'db';
    $dbname = getenv('MYSQL_DATABASE');
    $username = getenv('MYSQL_USER');
    $passwd = getenv('MYSQL_PASSWORD');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwd);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }

    $sql = "SELECT id, password FROM user WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if ($row) {
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            // Mot de passe correct, vérifiez si "Se souvenir de moi" est coché
            if (isset($_POST['rememberMe'])) {
                // Créez un cookie avec l'identifiant de l'utilisateur (par exemple, l'e-mail)
                $cookie_name = "user_email";
                $cookie_value = $email;
                $cookie_duration = 30 * 24 * 60 * 60; // Durée du cookie en secondes (30 jours)
                setcookie($cookie_name, $cookie_value, time() + $cookie_duration, "/");
            }

            $_SESSION['user_email'] = $email;
            $_SESSION['user_id'] = $row['id']; // Ajout de l'ID de l'utilisateur dans la session

            header("Location: dashbord.php");
            exit;
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        $erreur = "Adresse e-mail non enregistrée.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Formulaire de Connexion</h2>
    <?php
    if (isset($erreur)) {
        echo '<div class="alert alert-danger">' . $erreur . '</div>';
    }
    ?>
    <form action="#" method="POST">
        <!-- Champ : Adresse e-mail -->
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- Champ : Mot de passe -->
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <!-- Option : Se souvenir de moi -->
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
            <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
        </div>

        <!-- Lien : Mot de passe oublié -->
        <div class="form-group">
            <a href="signup.php">Créer un compte</a> &emsp;&emsp; <!-- <a href="#">Mot de passe oublié ?</a> -->
        </div>

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary" style="margin-bottom: 15px">Se connecter</button>
    </form>
</div>
<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>