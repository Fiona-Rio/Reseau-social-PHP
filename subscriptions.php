<?php
include 'session.php';
include 'connect.php';
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mes abonnements</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
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
        <aside>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez la liste des personnes dont
                     <?php echo $userPseudo; ?>
                    <!-- n° <?php echo $userId; ?> -->
                    suit les messages
                </p>

            </section>
        </aside>
        <main class='contacts'>
            <?php
            $laQuestionEnSql = "
                    SELECT users.* 
                    FROM followers 
                    LEFT JOIN users ON users.id=followers.followed_user_id 
                    WHERE followers.following_user_id='$userId'
                    GROUP BY users.id
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
                exit();
            }
            while ($tag = $lesInformations->fetch_assoc()) {
                // echo "<pre>" . print_r($tag, 1) . "</pre>";
                ?>
                <article>
                    <img src="user.jpg" alt="blason" />
                    <h3><?php echo $tag['alias'] ?></h3>
                    <p>id: <?php echo $tag['id'] ?></p>
                </article>
            <?php } ?>
        </main>
    </div>
</body>

</html>