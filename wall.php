<?php
include 'session.php';
include 'connect.php';
include 'likes.php';
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css?v=1.6" />

    <!-- nouvelle version du CSS suite à ajout de la class sub-unsub-forms -->
</head>

<body>
    <section class="top-bar">
        <div class="window-controls">
            <button class="close-btn"></button>
            <button class="minimize-btn"></button>
            <button class="maximize-btn"></button>
        </div>
    </section>
    <?php include 'header.php'; ?>

    <div id="wrapper">

        <?php

        // Arriver sur la page d'un autre utilisateur
        // Vérifie si un user_id est présent dans l'URL
        if (isset($_GET['user_id'])) {
            $userId = $_GET['user_id']; // ID de l'utilisateur dans l'URL
            // } else {
            //     $userId = $_SESSION['connected_id']; // Si pas d'ID dans l'URL, on utilise l'ID de l'utilisateur connecté
        }

        $userId = $mysqli->real_escape_string($userId); // Sécurisation de l'ID
        


        //var_dump($userId);
        // var_dump($_SESSION['connected_id']);
        $sql = "SELECT * FROM users WHERE id = '$userId'";
        $result = $mysqli->query($sql);


        // Gestion de la publication de message 
        
        // Si la méthode est POST et que le champ 'message' existe 
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
            $message = $_POST['message'];

            if (!empty($message)) {
                $authorId = $_SESSION['connected_id']; // Ton propre ID
                echo "<pre>" . "ID DE LA PERSONNE CONNECTEE = " . $authorId . "</pre>";

                var_dump($authorId);
                $wallUserId = $userId; // L'ID de l'utilisateur sur lequel tu es
        
                // Prépare la requête SLQ pour mettre le message dans la BDD
                //les valeurs en '?' ne sont définies qu'après avec le bind_param
                $requeteSQL = "INSERT INTO posts (content, user_id, wall_user_id, created) VALUES (?, ?, ?, NOW())";
                // Prépare la requete SQL avec la connexion à la BDD ($mysqli)
                $appelSQL = $mysqli->prepare($requeteSQL);
                // Lie les valeurs du message (chaine de caractères) et de l'utilisateur à la requete
                // le 'si' veut dire 's' pour le premier parametre donc string et 'i' pour le deuxieme parametre donc integer
                $appelSQL->bind_param('sii', $message, $authorId, $wallUserId);
                // Execute la requete
                $appelSQL->execute();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }

        //Gestion des abonnements et désabonnements 
        $messageApresClick = ''; // Variable pour stocker les messages d'abonnement
        
        // On vérifie si la méthode est POST et si le formulaire d'abonnement a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
            $connectedUserId = $_SESSION['connected_id']; // Utilisateur connecté
            // on vérifie d'abord si l'abonnement existe déjà
            $checkFollowingSql = "SELECT * FROM followers WHERE followed_user_id = '$userId' AND following_user_id = '$connectedUserId'";
            $followingResult = $mysqli->query($checkFollowingSql);

            if ($followingResult->num_rows > 0) {
                //num_rows retourne le nombre de ligne du résultat de ma requête, si pas de ligne -> pas d'abonnement existant
                $messageApresClick = "Vous êtes déjà abonné.";
            } else {

                // On insère le nouvel abonnement dans la BDD
                $requeteSQL = "INSERT INTO `followers`(`followed_user_id`, `following_user_id`)
                        VALUES ('$userId', '$connectedUserId')";
                $result = $mysqli->query($requeteSQL);
                if (!$result) {
                    $messageApresClick = "Erreur lors de l'abonnement : " . $mysqli->error;
                } else {
                    $messageApresClick = "Vous êtes maintenant abonné";
                }
            }
        }




        // On vérifie si la méthode est POST et si le formulaire de désabonnement a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsubscribe'])) {
            $connectedUserId = $_SESSION['connected_id']; // Utilisateur connecté
            // On supprime l'abonnement de la BDD
            $requeteSQL = "DELETE FROM `followers`
                                        WHERE followed_user_id = '$userId' 
                                        AND following_user_id = '$connectedUserId'";
            $result = $mysqli->query($requeteSQL);
            if (!$result) {
                $messageApresClick = "Erreur lors du désabonnement : " . $mysqli->error;
            } else {
                $messageApresClick = "Vous vous êtes désabonné avec succès.";
            }
        }

        ?>
        <aside>
            <?php
            // Récupération des informations de l'utilisateur
            $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if ($lesInformations) {
                $user = $lesInformations->fetch_assoc();
            } else {
                die("Erreur lors de l'exécution de la requête : " . $mysqli->error);
            }
            ?>
            <!-- <h2><?php echo "<pre>" . "userId lié à la page actuellement : " . $userId . "</pre>"; ?></h2> -->
            <!-- <h2><?php echo "<pre>" . "Confirmation de l'id du user connecté : " . $authorId . "</pre>"; ?></h2> -->
            <!-- <img src="user.jpg" alt="Portrait de l'utilisatrice" /> -->
            <div class="sidebar">
                <div id="avatar-frame" class="online">
                    <span class="gloss"></span>
                        <img width="100px" height="100px" src="msn_avatar.png"/>
                </div>
            </div>    
            <section>
                <h3><?php echo ($user['alias']) ?></h3>
                <p>Bienvenue sur mon mur
                    <!-- (n° <?php echo $user['id'] ?>) -->
                </p>
            </section>
            <section>
                <!-- Afficher le message d'abonnement/désabonnement si défini -->
                <?php if ($messageApresClick): ?>
                    <p><?php echo $messageApresClick; ?></p>
                <?php endif; ?>

                <!-- Formulaire pour s'abonner -->
                <div class="sub-unsub-forms">
                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <!-- Champ caché pour envoyer l'ID de l'utilisateur à suivre -->
                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>" />
                        <input type="submit" value="m'abonner" name="subscribe" id="sub-button">
                    </form>
                    <!-- Formulaire pour se désabonner -->
                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <!-- Champ caché pour envoyer l'ID de l'utilisateur à désabonner -->
                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>" />
                        <input type="submit" value="me désabonner" name="unsubscribe" id="unsub-button">
                    </form>

                </div>
            </section>
        </aside>

        <main>
            <?php
            // Récupération des publications
            $laQuestionEnSql = "
                    SELECT posts.id, posts.content, posts.created, users.alias as author_name, 
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.wall_user_id = '$userId' OR posts.user_id = '$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }
            ?>

            <!-- Formulaire pour envoyer un message -->
            <div class="emoji-picker">
                <span class="emoji">😀</span>
                <span class="emoji">😂</span>
                <span class="emoji">❤️</span>
                <span class="emoji">🔥</span>
                <span class="emoji">👍</span>
                <span class="emoji">😎</span>
                <span class="emoji">🙌</span>
            </div>
            <form class="post" action="wall.php?user_id=<?php echo $userId; ?>" method="post">

                <textarea name='message' placeholder="Écris ton ptit mess ici..."></textarea>

                <input type="submit" value="Envoyer" id="button-envoyer">

            </form>


            <?php
            // Affichage des publications
            while ($post = $lesInformations->fetch_assoc()) {
                ?>
                <article>
                    <h3>
                        <time datetime='<?php echo $post['created'] ?>'>
                            <?php
                            $date = new DateTime($post['created']);
                            $formatter = new IntlDateFormatter(
                                'fr_FR',
                                IntlDateFormatter::LONG,  //date
                                IntlDateFormatter::SHORT //heure
                            );
                            echo $formatter->format($date);
                            ?>
                        </time>
                    </h3>
                    <address>par <?php echo $post['author_name'] ?></address>
                    <div>
                        <p><?php echo $post['content'] ?></p>
                    </div>
                    <footer>
                        <small>♥ <?php echo $post['like_number'] ?></small>
                        <form action="wall.php" method="post">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>" />
                            <!-- <?php echo "<pre>" . print_r($post, 1) . "</pre>"; ?> -->
                            <button type="submit" name="action" value="like">👍 J'aime</button>
                            <button type="submit" name="action" value="dislike">👎 Je n'aime plus</button>
                        </form>
                    </footer>
                </article>
                <?php
            }
            ?>
        </main>
    </div>
</body>

</html>