<?php
include ('includes/header.php');
include ('includes/functions.php');
require 'includes/db.php';
include ('includes/menu.php');

if (!empty($_POST)) {
    $errors = array();
    if(empty($_POST['pseudo']) || (preg_match("/([^A-Za-z0-9_])/",$_POST['pseudo']))){
        $errors['pseudo'] = "Votre pseudo n'est pas valide";
    }

    if(empty($_POST['email']) || (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))){
        $errors['email'] = "Votre email n'est pas valide";
    }

    if (empty($_POST['code_postal'])) {
        $errors['code_postal'] = "Vous n'avez pas saisie de code postal";
    }

    if (empty($_POST['password'])) {
        $errors['password'] = "Vous n'avez pas saisie de mot de passe";
    }

    if ($_POST['password_confirm'] !== $_POST['password']) {
        $errors['password'] = "Votre mot de passe et sa confirmation sont différents";
    }

    if (empty($errors)) {
        $pseudo = $_POST['pseudo'];
        $mail = $_POST['email'];
        $mdp = $_POST['password'];
        $cp = $_POST['code_postal'];

        $req = $db->prepare("SELECT id FROM membres WHERE mail=:mail");
        $req->bindValue(':mail',$mail,PDO::PARAM_STR);
        $req->execute();
        $data = $req->fetch();

        $user = $data['id'];

            if ($user) {
                echo '
                Cet email est déjà enregistré.
                Pour réinitialiser votre mot de passe cliquez ici
                </p>
                <p>
                <a href="inscription.php">Fermer</a>
                </p>
                </div>';
            }
            else {
                $req = $db->prepare('INSERT INTO membres SET pseudo = ?, code_postal = ?, mail_parent = ?');
                $req->execute([$pseudo, $cp, $mail]);

                $req = $db->prepare("SELECT * FROM membres WHERE mail=:mail");
                $req->bindValue(':mail',$mail,PDO::PARAM_STR);
                $req->execute();
                $data = $req->fetch();

                $id = $data['id'];
                    
                $req = $db->prepare('INSERT INTO identifiants SET id_membre= ?, mail_membre = ?, identifiant = ?, 
                mdp = ?, confirm_token = ?');
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $token = str_token(60);
                $req->execute([$id, $mail, $pseudo, $password, $token]);

                mail($mail, "Merci de confirmer votre inscription", "Afin de valider votre compte cliquez
                 sur le lien suivant : \n\n
                 http://www.mon-doudou-perdu.fr/confirm-token.php?id=$id&token=$token" );
                
                die('Inscription terminée.
                <br/>RDV dans votre boîte mail pour valider votre adresse mail <br/>
                <p>
                <a href="connexion.php">Fermer</a>
                </p>
                ');
            }
    }
    else {
        echo '<p>Des erreurs ont été détectées : </p>';
        foreach ($errors as $error){
            echo '
            <ul>
            <li> -> ' . $error . '</li>
            </ul>';
        }
        echo '<p>
        <a href="inscription.php" style="color:white;border:1px solid white;padding:10px">Fermer</a>
        </p>';
    }
}
?>
 <h1>Inscription</h1>

    <form method="POST" action="">
        <label for="">Identifiant / pseudo</label><br/>
        <input type="text" name="pseudo" ><br/>
        <span class="legende">Caratères aurorisés : alphanumériques</span><br/><br/>

        <label for="">adresse e-mail</label><br/>
        <input type="email" name="email" ><br/><br/>

        <label for="">Code postal</label><br/>
        <input type="text" name="code_postal" ><br/><br/>

        <label for="">Mot de passe</label><br/>
        <input type="password" name="password" ><br/>
        <span class="legende">10 caratères maxi</span><br/><br/>

        <label for="">Confirmation mot de passe</label><br/>
        <input type="password" name="password_confirm" ><br/><br/>

        <button type="submit">M'enregistrer</button>
    </form>
</div>

<?php
include ('includes/footer.php');
