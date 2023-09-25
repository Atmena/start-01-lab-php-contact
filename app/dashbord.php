<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['logout'])) {
    // Détruire le cookie "user_email"
    setcookie("user_email", "", time() - 3600, "/");

    // Détruire la session
    session_unset();
    session_destroy();

    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $user_id = $_POST['user_id'];

    $host = 'db';
    $dbname = getenv('MYSQL_DATABASE');
    $username = getenv('MYSQL_USER');
    $passwd = getenv('MYSQL_PASSWORD');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwd);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }

    $sql = "INSERT INTO contact (name, surname, email, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $surname, $email, $user_id]);

    header("Location: dashbord.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Contacts</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Mon Tableau de Bord</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <!-- Lien "Se déconnecter" -->
            <li class="nav-item">
                <a class="nav-link" href="?logout">Se déconnecter</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Tableau de Bord - Gestion des Contacts</h2>
    <p>Bienvenue dans votre tableau de bord de gestion des contacts. Vous pouvez ajouter, modifier ou supprimer des contacts ici.</p>
    <div class="row">
        <div class="col-md-6">
            <!-- Formulaire d'ajout de contact -->
            <form method="POST">
                <div class="form-group">
                    <label for="surname">Nom</label>
                    <input type="text" class="form-control" id="surname" name="surname" required>
                </div>
                <div class="form-group">
                    <label for="name">Prénom</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <!-- Ajoutez un champ caché pour l'ID de l'utilisateur -->
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                <button type="submit" class="btn btn-primary">Ajouter Contact</button>
            </form>
        </div>
        <div class="col-md-6">
            <!-- Liste des contacts -->
            <h3>Liste des Contacts</h3>
            <ul class="list-group">
                <!-- Affichez ici la liste des contacts depuis la base de données -->
                <?php

                $host = 'db';
                $dbname = getenv('MYSQL_DATABASE');
                $username = getenv('MYSQL_USER');
                $passwd = getenv('MYSQL_PASSWORD');

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwd);
                } catch (PDOException $e) {
                    die("Erreur de connexion à la base de données: " . $e->getMessage());
                }

                $user_id = $_SESSION['user_id'];

                $stmt = $pdo->prepare("SELECT idcontact, name, surname, email FROM contact WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $contact = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($contact as $contact) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
                    $contact['name'] . ' ' . $contact['surname'] . ' - ' . $contact['email'] . 
                    '<span style="cursor: pointer;">
                        <a href="delete-contact.php?contact_id=' . $contact['idcontact'] . '">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                            </svg>
                        </a>                    
                    </span>
                </li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Attachez un gestionnaire de clic au bouton SVG de suppression
    $('svg[id^="delete-contact-"]').click(function() {
        var contactId = this.id.split('-')[2]; // Obtenez l'ID du contact à partir de l'ID du bouton

        // Envoyez une demande AJAX pour supprimer le contact
        $.ajax({
            type: "POST",
            url: "delete_contact.php",
            data: { contact_id: contactId }, // Envoyez l'ID du contact au script de suppression
            success: function(response) {
                // Rafraîchissez la liste des contacts ou effectuez toute autre action nécessaire
                location.reload(); // Rechargez la page pour afficher la liste mise à jour
            },
            error: function(xhr, status, error) {
                // Gérez les erreurs ici
                console.error(error);
            }
        });
    });
});
</script>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>