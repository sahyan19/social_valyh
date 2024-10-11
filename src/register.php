<?php
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
    if ($stmt->execute([$email, $password])) {
        echo 'Inscription réussie ! <a href="login.php">Connectez-vous</a>';
    } else {
        echo 'Erreur lors de l\'inscription.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Inscription</title>
</head>
<body>
    <div class="container-formulaire">
        <h2>Inscription</h2>
        <form method="POST" class="formulaire">
            <input type="email" name="email" class="input" placeholder="Email" required><br>
            <input type="password" name="password" class="input" placeholder="Mot de passe" required><br>
            <button type="submit" class="btn">S'inscrire</button>
        </form>
    </div>
</body>
</html>