<?php
include("db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];

        if (isset($_POST['reaction'])) {
            $stmt = $pdo->prepare('SELECT * FROM reactions WHERE post_id = ? AND user_id = ? AND comment_id IS NULL');
            $stmt->execute([$post_id, $user_id]);
            $existing_reaction = $stmt->fetch();

            if ($existing_reaction) {
                $stmt = $pdo->prepare('UPDATE reactions SET type = ? WHERE post_id = ? AND user_id = ? AND comment_id IS NULL');
                $stmt->execute([$_POST['reaction'], $post_id, $user_id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)');
                $stmt->execute([$post_id, $user_id, $_POST['reaction']]);
            }
        }

        $reactions = $pdo->prepare('SELECT type FROM reactions WHERE post_id = ? AND comment_id IS NULL');
        $reactions->execute([$post_id]);
        $reaction_counts = $reactions->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($reaction_counts as $reaction) {
            $result[$reaction['type']] = ($result[$reaction['type']] ?? 0) + 1;
        }

        echo json_encode($result);

    } elseif (isset($_POST['comment_id'])) {
        $comment_id = $_POST['comment_id'];

        if (isset($_POST['reaction'])) {
            $stmt = $pdo->prepare('SELECT * FROM reactions WHERE comment_id = ? AND user_id = ?');
            $stmt->execute([$comment_id, $user_id]);
            $existing_reaction = $stmt->fetch();

            if ($existing_reaction) {
                $stmt = $pdo->prepare('UPDATE reactions SET type = ? WHERE comment_id = ? AND user_id = ?');
                $stmt->execute([$_POST['reaction'], $comment_id, $user_id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO reactions (comment_id, user_id, type) VALUES (?, ?, ?)');
                $stmt->execute([$comment_id, $user_id, $_POST['reaction']]);
            }
        }

        $reactions = $pdo->prepare('SELECT type FROM reactions WHERE comment_id = ?');
        $reactions->execute([$comment_id]);
        $reaction_counts = $reactions->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($reaction_counts as $reaction) {
            $result[$reaction['type']] = ($result[$reaction['type']] ?? 0) + 1;
        }

        echo json_encode($result);
    }
}
