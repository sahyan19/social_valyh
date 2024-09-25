<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gestion des publications
    if (isset($_POST['content'])) {
        $stmt = $pdo->prepare('INSERT INTO posts (user_id, content) VALUES (?, ?)');
        $stmt->execute([$user_id, $_POST['content']]);
    }
    // Gestion des commentaires
    if (isset($_POST['comment_content']) && isset($_POST['post_id'])) {
        $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
        $stmt->execute([$_POST['post_id'], $user_id, $_POST['comment_content']]);
    }
    // Gestion des réactions
    if (isset($_POST['reaction']) && isset($_POST['post_id'])) {
        $stmt = $pdo->prepare('INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)');
        $stmt->execute([$_POST['post_id'], $user_id, $_POST['reaction']]);
    }
}

// Récupérer toutes les publications
$posts = $pdo->query('SELECT posts.*, users.email FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC')->fetchAll();
?>

<h1>Bienvenue sur le mini réseau social</h1>

<!-- Formulaire pour créer une nouvelle publication -->
<form method="POST">
    <textarea name="content" placeholder="Votre publication..." required></textarea>
    <button type="submit">Publier</button>
</form>

<hr>

<!-- Affichage des publications -->
<?php foreach ($posts as $post): ?>
    <div>
        <h3><?= htmlspecialchars($post['email']) ?> a publié :</h3>
        <p><?= htmlspecialchars($post['content']) ?></p>
        <small>Publié le <?= $post['created_at'] ?></small>

        <!-- Afficher les commentaires -->
        <?php
        $comments = $pdo->prepare('SELECT comments.*, users.email FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ?');
        $comments->execute([$post['id']]);
        foreach ($comments as $comment): ?>
            <div style="margin-left: 20px;">
                <strong><?= htmlspecialchars($comment['email']) ?> :</strong> <?= htmlspecialchars($comment['content']) ?>
                <small>Commenté le <?= $comment['created_at'] ?></small>
            </div>
        <?php endforeach; ?>

        <!-- Formulaire pour ajouter un commentaire -->
        <form method="POST" style="margin-left: 20px;">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <input type="text" name="comment_content" placeholder="Ajouter un commentaire" required>
            <button type="submit">Commenter</button>
        </form>

        <!-- Formulaire pour ajouter une réaction -->
        <form method="POST" style="margin-left: 20px;">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <select name="reaction">
                <option value="like">Like</option>
                <option value="love">Love</option>
                <option value="wow">Wow</option>
                <option value="sad">Sad</option>
                <option value="angry">Angry</option>
            </select>
            <button type="submit">Réagir</button>
        </form>

        <hr>
    </div>
<?php endforeach; ?>