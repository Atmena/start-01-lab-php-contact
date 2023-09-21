<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $name = $_POST['prenom'];
    $surname = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Valider et traiter les données (ajoutez ici vos validations)
    $erreurs = [];

    if (empty($name)) {
        $erreurs[] = "Le prénom est requis.";
    }

    if (empty($surname)) {
        $erreurs[] = "Le nom est requis.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse e-mail est invalide.";
    }

    if (empty($password)) {
        $erreurs[] = "Le mot de passe est requis.";
    }

    if ($_POST['password'] !== $_POST['password_repeat']) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier si l'adresse e-mail existe déjà dans la base de données
    // Créez une connexion PDO (remplacez ces informations par les vôtres)
    $host = 'db'; // Le nom du service Docker MySQL
    $dbname = getenv('MYSQL_DATABASE');
    $username = getenv('MYSQL_USER');
    $passwd = getenv('MYSQL_PASSWORD');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwd);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }

    $existingEmail = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
    $existingEmail->execute([$email]);
    $count = $existingEmail->fetchColumn();

    if ($count > 0) {
        $erreurs[] = "Un compte est déjà enregistré sous cette adresse mail.";
    }

    // Si aucune erreur, insérer l'utilisateur dans la base de données
    if (empty($erreurs)) {
        // Insertion dans la base de données

        // Rediriger l'utilisateur vers une page de confirmation
        header("Location: confirmation.php");
        exit;
    } else {
        // Stocker les erreurs dans une session
        $_SESSION['erreurs'] = $erreurs;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Formulaire d'Inscription</h2>
    <?php
    $erreurs = isset($_SESSION['erreurs']) ? $_SESSION['erreurs'] : [];
    unset($_SESSION['erreurs']); // Effacez les erreurs de la session

    // Afficher les erreurs
    if (!empty($erreurs)) {
        echo '<div class="alert alert-danger">';
        foreach ($erreurs as $erreur) {
            echo "<p>$erreur</p>";
        }
        echo '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <!-- Champ : Prénom -->
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>

        <!-- Champ : Nom -->
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>

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

        <!-- Champ : Répéter le mot de passe -->
        <div class="form-group">
            <label for="password_repeat">Répéter le mot de passe</label>
            <input type="password" class="form-control" id="password_repeat" name="password_repeat" required>
        </div>

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</div>
<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>