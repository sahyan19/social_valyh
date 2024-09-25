<?php
include("db.php");
session_start();

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Gestion des requêtes POST
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
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <small>Publié le <?= htmlspecialchars($post['created_at']) ?></small>

        <!-- Réactions et bouton Détails -->
        <div>
            <?php
            $reactions = $pdo->prepare('SELECT type, user_id, created_at FROM reactions WHERE post_id = ?');
            $reactions->execute([$post['id']]);
            $reactionDetails = $reactions->fetchAll(PDO::FETCH_ASSOC);
            
            $reactionCounts = [];
            foreach ($reactionDetails as $reaction) {
                $reactionCounts[$reaction['type']] = ($reactionCounts[$reaction['type']] ?? 0) + 1;
            }
            ?>
            <span>
                <i class="fas fa-thumbs-up reaction-like" title="Like"></i> <?= $reactionCounts['like'] ?? 0 ?>
                <i class="fas fa-heart reaction-love" title="Love"></i> <?= $reactionCounts['love'] ?? 0 ?>
                <i class="fas fa-surprise reaction-wow" title="Wow"></i> <?= $reactionCounts['wow'] ?? 0 ?>
                <i class="fas fa-sad-tear reaction-sad" title="Sad"></i> <?= $reactionCounts['sad'] ?? 0 ?>
                <i class="fas fa-angry reaction-angry" title="Angry"></i> <?= $reactionCounts['angry'] ?? 0 ?>
            </span>
            
            <!-- Bouton Détails -->
            <button onclick="toggleDetails(<?= $post['id'] ?>)">Détails</button>
            
            <!-- Détails des réactions -->
            <div id="details-<?= $post['id'] ?>" style="display: none;">
                <ul>
                    <?php foreach ($reactionDetails as $detail): 
                        // Récupérez l'email de l'utilisateur qui a réagi
                        $user_stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
                        $user_stmt->execute([$detail['user_id']]);
                        $user = $user_stmt->fetch();
                        // Vérifiez si l'utilisateur existe
                        if ($user) {
                    ?>
                        <li><?= htmlspecialchars($user['email']) ?> a réagi avec <?= htmlspecialchars($detail['type']) ?> le <?= htmlspecialchars($detail['created_at']) ?></li>
                    <?php } endforeach; ?>
                </ul>
            </div>
        </div>
        
        
        <!-- Formulaire pour ajouter une réaction à la publication -->
        <form method="POST">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <select name="reaction" required>
                <option value="" disabled selected>Réagir...</option>
                <option value="like">Like</option>
                <option value="love">Love</option>
                <option value="wow">Wow</option>
                <option value="sad">Sad</option>
                <option value="angry">Angry</option>
            </select>
            <button type="submit">Réagir</button>
        </form>

        <h3>Commentaires :</h3>

        <!-- Afficher les commentaires -->
        <?php
        $comments = $pdo->prepare('SELECT comments.*, users.email FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ?');
        $comments->execute([$post['id']]);
        foreach ($comments as $comment): ?>
            <div style="margin-left: 20px;">
                <strong><?= htmlspecialchars($comment['email']) ?> :</strong> <?= nl2br(htmlspecialchars($comment['content'])) ?>
                <small>Commenté le <?= htmlspecialchars($comment['created_at']) ?></small>

                <!-- Réactions et bouton Détails pour les commentaires -->
                <div>
                    <?php
                    // Utilisez ici le bon id pour récupérer les réactions du commentaire
                    $commentReactions = $pdo->prepare('SELECT type, user_id, created_at FROM reactions WHERE comment_id = ?');
                    $commentReactions->execute([$comment['id']]);
                    $reactionDetails = $commentReactions->fetchAll(PDO::FETCH_ASSOC);
                    
                    $reactionCounts = [];
                    foreach ($reactionDetails as $reaction) {
                        $reactionCounts[$reaction['type']] = ($reactionCounts[$reaction['type']] ?? 0) + 1;
                    }
                    ?>
                    <span>
                        <i class="fas fa-thumbs-up reaction-like" title="Like"></i> <?= $reactionCounts['like'] ?? 0 ?>
                        <i class="fas fa-heart reaction-love" title="Love"></i> <?= $reactionCounts['love'] ?? 0 ?>
                        <i class="fas fa-surprise reaction-wow" title="Wow"></i> <?= $reactionCounts['wow'] ?? 0 ?>
                        <i class="fas fa-sad-tear reaction-sad" title="Sad"></i> <?= $reactionCounts['sad'] ?? 0 ?>
                        <i class="fas fa-angry reaction-angry" title="Angry"></i> <?= $reactionCounts['angry'] ?? 0 ?>
                    </span>
                    
                    <!-- Bouton Détails -->
                    <button onclick="toggleDetails(<?= $comment['id'] ?>, 'comment')">Détails</button>
                    
                    <!-- Détails des réactions -->
                    <div id="details-comment-<?= $comment['id'] ?>" style="display: none;">
                        <ul>
                            <?php foreach ($reactionDetails as $detail): 
                                // Récupérez l'email de l'utilisateur qui a réagi
                                $user_stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
                                $user_stmt->execute([$detail['user_id']]);
                                $user = $user_stmt->fetch();
                                // Vérifiez si l'utilisateur existe
                                if ($user) {
                            ?>
                                <li><?= htmlspecialchars($user['email']) ?> a réagi avec <?= htmlspecialchars($detail['type']) ?> le <?= htmlspecialchars($detail['created_at']) ?></li>
                            <?php } endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Formulaire pour réagir au commentaire -->
                <form method="POST" style="margin-left: 20px;">
                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                    <select name="reaction" required>
                        <option value="" disabled selected>Réagir...</option>
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
            <textarea name="comment_content" placeholder="Votre commentaire..." required></textarea>
            <button type="submit">Commenter</button>
        </form>
    </div>
    <hr>
<?php endforeach; ?>
<button><a href="logout.php">Se Déconnecter</a></button>
<script src="script.js"></script>
