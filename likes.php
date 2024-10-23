<?php
include 'connect.php';
// Gestion des Likes 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $postId = $_POST['post_id'];
    $action = $_POST['action']; // Récupère l'action (like ou dislike)

    // Vérifie si l'utilisateur a déjà liké le post
    $checkLikeSQL = "SELECT * FROM likes WHERE user_id = '$userId' AND post_id = '$postId'";
    $likeResult = $mysqli->query($checkLikeSQL);

    if ($action === 'like') {
        if ($likeResult->num_rows > 0) {
            $messageApresClick = "Vous avez déjà liké ce post.";
        } else {
            // Ajouter un nouveau like
            $requeteSQL = "INSERT INTO likes (user_id, post_id) VALUES ('$userId', '$postId')";
            $result = $mysqli->query($requeteSQL);
            if (!$result) {
                $messageApresClick = "Erreur lors de l'ajout du like : " . $mysqli->error;
            } else {
                $messageApresClick = "Vous avez liké ce post.";

            }
        }
    } elseif ($action === 'dislike') {

        if ($likeResult->num_rows > 0) {
            $requeteSQL = "DELETE FROM likes
        WHERE user_id = '$userId'
        AND post_id = '$postId'";
            $result = $mysqli->query($requeteSQL);
            if (!$result) {
                $messageApresClick = "Erreur lors de l'ajout du like : " . $mysqli->error;
            } else {
                $messageApresClick = "Vous avez bien retiré votre like.";

            }

        }
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
