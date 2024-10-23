<?php
include 'session.php';
session_destroy(); // Détruit la session
header("Location: login.php"); // Redirige vers la page d'accueil de connexion
exit();