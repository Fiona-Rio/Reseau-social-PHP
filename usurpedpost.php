<?php
session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Post d'usurpateur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <?php include 'header.php'; ?>
    </header>


    <div id="wrapper">

        <aside>
            <h2>Présentation</h2>
            <p>Sur cette page on peut poster un message en se faisant
                passer pour quelqu'un d'autre</p>
        </aside>
        <main>
            <article>
                <h2>Poster un message</h2>
                <?php
                /**
                 * BD
                 */
                include 'connect.php';
                ;
                /**
                 * Récupération de la liste des auteurs
                 */
                $auteurPost = [];
                $laQuestionEnSql = "SELECT * FROM users";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                while ($user = $lesInformations->fetch_assoc()) {
                    $auteurPost[$user['id']] = $user['alias'];
                }


                $enCoursDeTraitement = isset($_POST['auteur']);
                if ($enCoursDeTraitement) {
                    echo "<pre>" . print_r($_POST, 1) . "</pre>";
                    $authorId = $_POST['auteur'];
                    $postContent = $_POST['message'];

                    $authorId = intval($mysqli->real_escape_string($authorId));
                    $postContent = $mysqli->real_escape_string($postContent);

                    $lInstructionSql = "INSERT INTO posts "
                        . "(id, user_id, content, created) "
                        . "VALUES (NULL, "
                        . $authorId . ", "
                        . "'" . $postContent . "', "
                        . "NOW());"
                    ;
                    echo $lInstructionSql;

                    $ok = $mysqli->query($lInstructionSql);
                    if (!$ok) {
                        echo "Impossible d'ajouter le message: " . $mysqli->error;
                    } else {
                        echo "Message posté en tant que :" . $auteurPost[$authorId];
                    }
                }
                ?>
                <form action="usurpedpost.php" method="post">
                    <input type='hidden' name='auteur' value=''>
                    <dl>
                        <dt><label for='auteur'>Auteur</label></dt>
                        <dd><select name='auteur'>
                                <?php
                                foreach ($auteurPost as $id => $alias)
                                    echo "<option value='$id'>$alias</option>";
                                ?>
                            </select></dd>
                        <dt><label for='message'>Message</label></dt>
                        <dd><textarea name='message'></textarea></dd>
                    </dl>
                    <input type='submit'>
                </form>
            </article>
        </main>
    </div>
</body>

</html>