<?php
include("db.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: home.php');
    } else {
        echo '<h1>Identifiants incorrects ou Compte introuvable</h1>';
        echo "<button><a href='register.php' >S'inscrire</a></button>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Connexion</title>
</head>
<body>
    <div class="container-formulaire">
        <form method="POST" >
            <div class="formulaire-login">
                <h1>Connexion</h1>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </div>
        </form>
    </div>
</body>
</html>