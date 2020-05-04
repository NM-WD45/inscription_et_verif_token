<?php
session_start();
include ('includes/header.php');
include ('includes/db.php');
include ('includes/menu.php');

if ($_GET['id']) {
    $id = $_GET['id'];
    $token = $_GET['token'];

    $req = $db->prepare('SELECT * FROM identifiants WHERE id = :id');
    $req->bindValue(':id', $id, PDO::PARAM_INT);
    $req->execute();
    $auth = $req->fetch();

    if ($auth['confirm_token'] == $token) 
    {
        $_SESSION['auth'] = $auth;

        $db->prepare('UPDATE identifiants SET confirm_token = NULL, confirm_at = NOW() WHERE id = ?')
        ->execute([$id]);
            
        die('
        Compte confirmé!
        <p>
        <a href="mon-compte.php?id='.$id.'">Aller sur votre espace membre</a>
        </p>';
    }
    else 
    {
        die('
        Lien invalide ou compte déjà validé, contacter le webmaster
        <p>
        <a href="connexion.php">Retour</a>
        </p>';
    }
}
else {
        die('Vous ne pouvez pas accéder à cette page sans être connecté.
        <p>
        <a href="connexion.php">Fermer</a>
        </p>');
}
include ('includes/footer.php');
