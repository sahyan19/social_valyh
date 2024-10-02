<?php
include("db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

//============| email de l'utilisateur connecté |======================================

$user_login = $pdo->prepare('SELECT email FROM users WHERE id = ?');
$user_login->execute([$user_id]);
$user_email = $user_login->fetch();

//===================| Gestion des requêtes POST |======================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //===========| Gestion des publications |==============================
    if (isset($_POST['content'])) {
        $stmt = $pdo->prepare('INSERT INTO posts (user_id, content) VALUES (?, ?)');
        $stmt->execute([$user_id, $_POST['content']]);

        // Récupérer la publication pour l'afficher sans recharger la page
        $postId = $pdo->lastInsertId();
        $post = $pdo->prepare('SELECT posts.*, users.email FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?');
        $post->execute([$postId]);
        $newPost = $post->fetch();
        echo json_encode($newPost);
        exit;
    }

    //============| Gestion des commentaires |=============================
    if (isset($_POST['comment_content']) && isset($_POST['post_id'])) {
        $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
        $stmt->execute([$_POST['post_id'], $user_id, $_POST['comment_content']]);

        // Récupérer le commentaire pour l'afficher sans recharger la page
        $commentId = $pdo->lastInsertId();
        $comment = $pdo->prepare('SELECT comments.*, users.email FROM comments JOIN users ON comments.user_id = users.id WHERE comments.id = ?');
        $comment->execute([$commentId]);
        $newComment = $comment->fetch();
        echo json_encode($newComment);
        exit;
    }
    
    //=============| Gestion des réactions: publications/commentaires |===========
    if (isset($_POST['reaction'])) {
        if (isset($_POST['post_id'])) {
            $stmt = $pdo->prepare('SELECT * FROM reactions WHERE post_id = ? AND user_id = ?');
            $stmt->execute([$_POST['post_id'], $user_id]);
            $existingReaction = $stmt->fetch();

            if ($existingReaction) {
                $stmt = $pdo->prepare('UPDATE reactions SET type = ? WHERE post_id = ? AND user_id = ?');
                $stmt->execute([$_POST['reaction'], $_POST['post_id'], $user_id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)');
                $stmt->execute([$_POST['post_id'], $user_id, $_POST['reaction']]);
            }
        } elseif (isset($_POST['comment_id'])) {
            $stmt = $pdo->prepare('SELECT * FROM reactions WHERE comment_id = ? AND user_id = ?');
            $stmt->execute([$_POST['comment_id'], $user_id]);
            $existingReaction = $stmt->fetch();

            if ($existingReaction) {
                $stmt = $pdo->prepare('UPDATE reactions SET type = ? WHERE comment_id = ? AND user_id = ?');
                $stmt->execute([$_POST['reaction'], $_POST['comment_id'], $user_id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO reactions (comment_id, user_id, type) VALUES (?, ?, ?)');
                $stmt->execute([$_POST['comment_id'], $user_id, $_POST['reaction']]);
            }
        }
    }
}

//===================| Récupérer toutes les publications |==================================

$posts = $pdo->query('SELECT posts.*, users.email FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC')->fetchAll();
?>

<link rel="stylesheet" href="../style/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="container">
    <div>
        <div class="user">
            <img src="../img/imageTeste.png" alt="Photo de profil" class="pdp">
            <h4><?= htmlspecialchars($user_email['email']) ?></h4>
        </div>
        <button><a href="logout.php">Déconnexion</a></button>
    </div>

    <div class="content">
        <!-- Formulaire pour créer une nouvelle publication -->
        <form id="post-form">
            <textarea name="content" placeholder="Votre publication..." required></textarea>
            <button type="submit">Publier</button>
        </form>

        <hr>

        <!-- Affichage des publications -->
        <?php foreach ($posts as $post): ?>
            <div>
                <h3 class="head-post"><img src="../img/imageTeste.png" alt="photo de profil" class="image-gros"><?= htmlspecialchars($post['email']) ?> a publié :</h3>
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
                    <span id="reactions-<?= $post['id'] ?>">
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
                                $user_stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
                                $user_stmt->execute([$detail['user_id']]);
                                $user = $user_stmt->fetch();
                                if ($user) {
                            ?>
                                <li><?= htmlspecialchars($user['email']) ?> a réagi avec <?= htmlspecialchars($detail['type']) ?> le <?= htmlspecialchars($detail['created_at']) ?></li>
                            <?php } endforeach; ?>
                        </ul>
                    </div>
                </div>
                                
                                
                <!-- Formulaire pour ajouter une réaction à la publication -->
                <form onsubmit="event.preventDefault(); update_post_reactions(<?= $post['id'] ?>, this.reaction.value);">
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
                 <div id="comments-<?= $post['id']?>">
                    <?php
                    $comments = $pdo->prepare('SELECT comments.*, users.email FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ?');
                    $comments->execute([$post['id']]);
                    foreach ($comments as $comment): ?>
                        <div style="margin-left: 20px;">
                            <div class="comment-item">
                                <div class="comment-inside">
                                    <img src="../img/imageTeste.png" alt="Photo de profil" class="image-mini">
                                    <strong><?= htmlspecialchars($comment['email']) ?> :</strong>
                                </div>
                                <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                <small>Commenté le <?= htmlspecialchars($comment['created_at']) ?></small>
                            </div>

                            <!-- Réactions et bouton Détails pour les commentaires -->
                            <div>
                                <?php
                                $commentReactions = $pdo->prepare('SELECT type, user_id, created_at FROM reactions WHERE comment_id = ?');
                                $commentReactions->execute([$comment['id']]);
                                $reactionDetails = $commentReactions->fetchAll(PDO::FETCH_ASSOC);

                                $reactionCounts = [];
                                foreach ($reactionDetails as $reaction) {
                                    $reactionCounts[$reaction['type']] = ($reactionCounts[$reaction['type']] ?? 0) + 1;
                                }
                                ?>
                                <span id="reactions-comment-<?= $comment['id'] ?>">
                                    <i class="fas fa-thumbs-up reaction-like" title="Like"></i> <?= $reactionCounts['like'] ?? 0 ?>
                                    <i class="fas fa-heart reaction-love" title="Love"></i> <?= $reactionCounts['love'] ?? 0 ?>
                                    <i class="fas fa-surprise reaction-wow" title="Wow"></i> <?= $reactionCounts['wow'] ?? 0 ?>
                                    <i class="fas fa-sad-tear reaction-sad" title="Sad"></i> <?= $reactionCounts['sad'] ?? 0 ?>
                                    <i class="fas fa-angry reaction-angry" title="Angry"></i> <?= $reactionCounts['angry'] ?? 0 ?>
                                </span>

                                <button onclick="toggleDetails(<?= $comment['id'] ?>, 'comment')">Détails</button>

                                <!-- Détails des réactions -->
                                <div id="details-comment-<?= $comment['id'] ?>" style="display: none;">
                                    <ul>
                                        <?php foreach ($reactionDetails as $detail): 
                                            $user_stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
                                            $user_stmt->execute([$detail['user_id']]);
                                            $user = $user_stmt->fetch();
                                            if ($user) {
                                        ?>
                                            <li><?= htmlspecialchars($user['email']) ?> a réagi avec <?= htmlspecialchars($detail['type']) ?> le <?= htmlspecialchars($detail['created_at']) ?></li>
                                        <?php } endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- Formulaire pour ajouter une réaction au commentaire -->
                            <form onsubmit="event.preventDefault(); update_comment_reactions(<?= $comment['id'] ?>, this.reaction.value);">
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
                 </div>
                

                <!-- Formulaire pour ajouter un commentaire à la publication -->
                <form class="comment-form" data-post-id="<?= $post['id'] ?>">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <textarea name="comment_content" placeholder="Votre commentaire..." required></textarea>
                    <button type="submit">Commenter</button>
                </form>
            </div>
            <hr>
        <?php endforeach; ?>
    </div>
</div>

<script src="../js/script.js"></script>
