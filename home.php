<?php
include("db.php");
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
    // Gestion des réactions sur les publications ou commentaires
    if (isset($_POST['reaction'])) {
        if (isset($_POST['post_id'])) {
            $stmt = $pdo->prepare('INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)');
            $stmt->execute([$_POST['post_id'], $user_id, $_POST['reaction']]);
        } elseif (isset($_POST['comment_id'])) {
            $stmt = $pdo->prepare('INSERT INTO reactions (comment_id, user_id, type) VALUES (?, ?, ?)');
            $stmt->execute([$_POST['comment_id'], $user_id, $_POST['reaction']]);
        }
    }
}

// Récupérer toutes les publications avec les utilisateurs
$posts = $pdo->query('SELECT posts.*, users.email FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC')->fetchAll();
?>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<h1>Bienvenue sur Social Valyh</h1>

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

        <!-- Afficher les réactions sur la publication -->
        <div>
            <?php
            $reactions = $pdo->prepare('SELECT type, COUNT(*) as count FROM reactions WHERE post_id = ? GROUP BY type');
            $reactions->execute([$post['id']]);
            $reactionCounts = [];
            foreach ($reactions as $reaction) {
                $reactionCounts[$reaction['type']] = $reaction['count'];
            }
            ?>
            <span>
                <i class="fas fa-thumbs-up reaction-like" title="Like"></i> <?= $reactionCounts['like'] ?? 0 ?>
                <i class="fas fa-heart reaction-love" title="Love"></i> <?= $reactionCounts['love'] ?? 0 ?>
                <i class="fas fa-surprise reaction-wow" title="Wow"></i> <?= $reactionCounts['wow'] ?? 0 ?>
                <i class="fas fa-sad-tear reaction-sad" title="Sad"></i> <?= $reactionCounts['sad'] ?? 0 ?>
                <i class="fas fa-angry reaction-angry" title="Angry"></i> <?= $reactionCounts['angry'] ?? 0 ?>
            </span>
        </div>
        
        <!-- Formulaire pour ajouter une réaction à la publication -->
        <form method="POST">
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

        <!-- Afficher les commentaires -->
        <?php
        $comments = $pdo->prepare('SELECT comments.*, users.email FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ?');
        $comments->execute([$post['id']]);
        foreach ($comments as $comment): ?>
            <div style="margin-left: 20px;">
                <strong><?= htmlspecialchars($comment['email']) ?> :</strong> <?= htmlspecialchars($comment['content']) ?>
                <small>Commenté le <?= $comment['created_at'] ?></small>

                <!-- Afficher les réactions sur le commentaire -->
                <div>
                    <?php
                    $comment_reactions = $pdo->prepare('SELECT type, COUNT(*) as count FROM reactions WHERE comment_id = ? GROUP BY type');
                    $comment_reactions->execute([$comment['id']]);
                    $commentReactionCounts = [];
                    foreach ($comment_reactions as $reaction) {
                        $commentReactionCounts[$reaction['type']] = $reaction['count'];
                    }
                    ?>
                    <span>
                        <i class="fas fa-thumbs-up reaction-like" title="Like"></i> <?= $commentReactionCounts['like'] ?? 0 ?>
                        <i class="fas fa-heart reaction-love" title="Love"></i> <?= $commentReactionCounts['love'] ?? 0 ?>
                        <i class="fas fa-surprise reaction-wow" title="Wow"></i> <?= $commentReactionCounts['wow'] ?? 0 ?>
                        <i class="fas fa-sad-tear reaction-sad" title="Sad"></i> <?= $commentReactionCounts['sad'] ?? 0 ?>
                        <i class="fas fa-angry reaction-angry" title="Angry"></i> <?= $commentReactionCounts['angry'] ?? 0 ?>
                    </span>
                </div>

                <!-- Formulaire pour réagir au commentaire -->
                <form method="POST" style="margin-left: 20px;">
                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                    <select name="reaction">
                        <option value="like">Like</option>
                        <option value="love">Love</option>
                        <option value="wow">Wow</option>
                        <option value="sad">Sad</option>
                        <option value="angry">Angry</option>
                    </select>
                    <button type="submit">Réagir</button>
                </form>
            </div>
        <?php endforeach; ?>

        <!-- Formulaire pour ajouter un commentaire -->
        <form method="POST" style="margin-left: 20px;">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <input type="text" name="comment_content" placeholder="Ajouter un commentaire" required>
            <button type="submit">Commenter</button>
        </form>

        <hr>
    </div>
<?php endforeach; ?>
<button><a href="logout.php">Se Déconnecter</a></button>
