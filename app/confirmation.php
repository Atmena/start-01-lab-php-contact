<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de l'Inscription</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5 text-center">
    <h2>Confirmation de l'Inscription</h2>
    <p>Votre compte a été créé avec succès.</p>
    <p>Vous serez redirigé vers la page d'accueil dans <span id="countdown">5</span> secondes.</p>
</div>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Redirection automatique vers index.php après 10 secondes -->
<script>
    // Fonction pour mettre à jour le compte à rebours
    function updateCountdown() {
        const countdownElement = document.getElementById("countdown");
        let seconds = parseInt(countdownElement.textContent);
        if (seconds > 0) {
            seconds--;
            countdownElement.textContent = seconds;
        } else {
            // Rediriger vers index.php après le compte à rebours
            window.location.href = "index.php";
        }
    }

    // Mettre à jour le compte à rebours toutes les 1 seconde
    setInterval(updateCountdown, 1000);
</script>
</body>
</html>