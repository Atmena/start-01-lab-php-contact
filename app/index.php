<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer l'e-mail et le mot de passe soumis dans le formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Créer une connexion PDO (remplacez ces informations par les vôtres)
    $host = 'db'; // Le nom du service Docker MySQL
    $dbname = getenv('MYSQL_DATABASE');
    $username = getenv('MYSQL_USER');
    $passwd = getenv('MYSQL_PASSWORD');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwd);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }

    // Requête SQL pour récupérer le mot de passe associé à l'adresse e-mail
    $sql = "SELECT password FROM user WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    // Vérifier si l'adresse e-mail existe dans la base de données
    if ($row) {
        $hashedPassword = $row['password'];

        // Vérifier si le mot de passe soumis correspond au mot de passe stocké (utilisez password_verify)
        if (password_verify($password, $hashedPassword)) {
            // Mot de passe correct, vous pouvez rediriger l'utilisateur vers une page de succès ou effectuer d'autres actions
            $_SESSION['user_email'] = $email; // Stocker l'e-mail de l'utilisateur connecté dans une session
            header("Location: dashbord.php"); // Rediriger vers une page de succès
            exit;
        } else {
            // Mot de passe incorrect
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        // L'adresse e-mail n'existe pas dans la base de données
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
            <a href="#">Mot de passe oublié ?</a>
        </div>

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
