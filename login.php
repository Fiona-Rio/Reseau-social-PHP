<?php
include 'connect.php';
session_start();
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Connexion</title>
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
            <h2>Présentation</h2>
            <p>Bienvenu sur notre réseau social.</p>
        </aside>
        <main>
            <article>
                <h2>Connexion</h2>
                <?php
                $enCoursDeTraitement = isset($_POST['email']);
                if ($enCoursDeTraitement) {
                    echo "<pre>" . print_r($_POST, 1) . "</pre>";
                    $emailAVerifier = $_POST['email'];
                    $passwdAVerifier = $_POST['motpasse'];
                    $emailAVerifier = $mysqli->real_escape_string($emailAVerifier);
                    $passwdAVerifier = $mysqli->real_escape_string($passwdAVerifier);
                    $passwdAVerifier = md5($passwdAVerifier);
                    $lInstructionSql = "SELECT * "
                        . "FROM users "
                        . "WHERE "
                        . "email LIKE '" . $emailAVerifier . "'"
                    ;

                    $res = $mysqli->query($lInstructionSql);
                    $user = $res->fetch_assoc();
                    if (!$user or $user["password"] != $passwdAVerifier) {
                        echo "La connexion a échouée. ";

                    } else {
                        echo "Votre connexion est un succès : " . $user['alias'] . ".";
                        $_SESSION['connected_id'] = $user['id'];
                        $_SESSION['pseudo'] = $user['alias'];
                        var_dump($_SESSION);

                    }
            }
                ?>
                <form action="login.php" method="post">
                    <input type='hidden' name='email'>
                    <dl>
                        <dt><label for='email'>E-Mail</label></dt>
                        <dd><input type='email' name='email' 
                        required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"></dd>
                        <dt><label for='motpasse'>Mot de passe</label></dt>
                        <dd><input type='password' name='motpasse'required></dd>
                    </dl>
                    <input type='submit'>
                </form>
                <p>
                    Pas de compte?
                    <a href='registration.php'>Inscrivez-vous.</a>
                </p>

            </article>
        </main>
    </div>
</body>
</html>