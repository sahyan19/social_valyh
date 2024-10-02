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
        echo '<h1>Identifiants incorrects !</h1>';
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
        <h2>Connexion</h2>
        <form method="POST" class="formulaire">
                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Email" class="input"  required><br>
                <label for="password">Mot de passe:</label>
                <input type="password" name="password" class="input"  placeholder="Mot de passe" required><br>
                <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>