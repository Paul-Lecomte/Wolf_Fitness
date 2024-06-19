<?php
include '../../components/header.php';

//on vàrifie que le form ne soit pas vide
if (!empty($_POST)) {
    //ici le formualire est envoyé
    //on vérifie que tous les champs soit remplie
    if (isset($_POST["email"], $_POST["password"])  && !empty($_POST["email"]) && !empty($_POST["password"])) {
        //on check l'email
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            die("L'adresse email n'est pas valide");
        }

        //on peut enregistrer notre user
        //on se co a la db
        require_once "../../components/db.php";

        //Requête préparé
        $sql = "SELECT * FROM users WHERE email = :email";
        $req = $db->prepare($sql);
        $req->bindValue(':email', $_POST["email"]);
        $req->execute();
        $user = $req->fetch();

        //si l'email n'existe pas dans bd
        if (!$user) {
            die("Les info de connection ne sont pas valable");
        }

        //ici j'ai un user dans la db donc je dois comparém le mot de passe
        if (password_verify($_POST["password"], $user->password)) {
          session_start();
          $_SESSION["user"] = [
            "id" => $user->user_id,
            "username" => $user->username,
            "email" => $user->email,
            "profile_pic" => $user->profile_pic,
          ];
          header("Location: ../feed/feed.php");
          exit();
        } else {
          die("information de connexion incorrect");
        }
        //ici on a un user co valide donc on crée la session
        

    } else {
        die("formulaire incomplet");
    }
}
?>
    <div class="logo container my-6 pers_align">
      <img style="max-width: 20%" src="../../assets/logo.svg" alt="wolf fitness logo">
    </div>
    <form method="post">
      <div class="columns is-max-desktop pers_align">
        <div class="field column my-3 is-half pers_align">
          <label class="label">EMAIL</label>
          <div class="control">
            <input class="input" name="email" type="text">
          </div>
        </div>
        <div class="field column my-3 is-half pers_align">
          <label class="label">PASSWORD</label>
          <div class="control">
            <input class="input" name="password" type="text">
          </div>
        </div>
        <div class="control pers_align">
          <button type="button" class="mt-3 p-2 c-button-red is-align-self-center"><a style="color:red;" href="create_login.php">Create Account</a></button>
          <button type="submit" class="mt-3 p-2 c-button">LOGIN</button>
        </div>
        
      </div>
    </form>
  </body>
</html>