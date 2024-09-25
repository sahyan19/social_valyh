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

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">S'inscrire</button>
</form>