<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Administration</title>
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

    <?php
    include 'connect.php';
    if ($mysqli->connect_errno) {
        echo ("Échec de la connexion : " . $mysqli->connect_error);
        exit();
    }
    ?>
    <div id="wrapper" class='admin'>
        <main>
            <h2>Utilisatrices</h2>
            <?php
            $laQuestionEnSql = "SELECT * FROM `users` LIMIT 50";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
                exit();
            }
            while ($tag = $lesInformations->fetch_assoc()) {
                //echo "<pre>" . print_r($tag, 1) . "</pre>";
                ?>
                <article>
                    <h3>
                        <a href="wall.php?user_id=<?php echo $tag['id'] ?>"><?php echo $tag['alias'] ?></a>
                    </h3>
                    <p>id : <?php echo $tag['id'] ?></p>
                    <nav>
                        <a href="wall.php?user_id=<?php echo $tag['id'] ?>">Mur</a>
                        | <a href="feed.php?user_id=<?php echo $tag['id'] ?>">Flux</a>
                        | <a href="settings.php?user_id=<?php echo $tag['id'] ?>">Paramètres</a>
                        | <a href="followers.php?user_id=<?php echo $tag['id'] ?>">Suiveurs</a>
                        | <a href="subscriptions.php?user_id=<?php echo $tag['id'] ?>">Abonnements</a>
                    </nav>
                </article>
            <?php } ?>
        </main>
    </div>
</body>

</html>