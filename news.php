<?php
include 'session.php';
include 'connect.php';
include 'likes.php';
?>


<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Actualités</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css?v=1.5" />
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
        <div class="sidebar">
                <div id="avatar-frame" class="online">
                    <span class="gloss"></span>
                        <img width="100px" height="100px" src="Taylor-Swift.webp"/>
                </div>
            </div>    
            <section>
                <h3>Tous messages de fms</h3>
                <p>Retrouvez toutes les publications de fms !</p>
            </section>
        </aside>
        <main>
            <?php
            include 'connect.php';
            if ($mysqli->connect_errno) {
                echo "<article>";
                echo ("Échec de la connexion : " . $mysqli->connect_error);
                echo ("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                echo "</article>";
                exit();
            }
            $laQuestionEnSql = "
                    SELECT 
                    posts.id,
                    posts.content,
                    posts.user_id,
                    posts.created,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    LIMIT 5
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo "<article>";
                echo ("Échec de la requete : " . $mysqli->error);
                echo ("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                exit();
            }
            while ($post = $lesInformations->fetch_assoc()) {
                //echo "<pre>" . print_r($post, 1) . "</pre>";
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
                    <!-- <address>par <?php echo $post['author_name'] ?></address> -->
                    <address>par
                        <a href="wall.php?user_id=<?php echo $post['user_id'] ?>"> <?php echo $post['author_name'] ?></a>
                    </address>
                    <div>
                        <p><?php echo $post['content'] ?></p>
                    </div>
                    <footer>
                        <small>♥<?php echo $post['like_number'] ?></small>
                        <form action="news.php" method="post" style="display:inline;">
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