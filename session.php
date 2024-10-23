<?php
session_start();

include 'connect.php';

// Vérifie si l'utilisateur est connecté (si l'ID de l'utilisateur est dans la session)
if (!isset($_SESSION['connected_id'])) {
    // Si l'utilisateur n'est pas connecté, redirection vers la page de connexion
    header('Location: login.php');
    exit();  // Termine le script pour s'assurer que rien d'autre ne se passe
}

$userId = $_SESSION['connected_id'];  // L'utilisateur est connecté, on récupère son ID
$userPseudo = $_SESSION['pseudo'];
