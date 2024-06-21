<?php
include '../../components/header.php';

//we check if the form is empty
if (!empty($_POST)) {
    //we check that the form is complete
    if (isset($_POST["email"], $_POST["password"])  && !empty($_POST["email"]) && !empty($_POST["password"])) {
        //we check the email
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            die("L'adresse email n'est pas valide");
        }

        //we can log the user
        //we connect to the db
        require_once "../../components/db.php";

        //prepared req
        $sql = "SELECT * FROM users WHERE email = :email";
        $req = $db->prepare($sql);
        $req->bindValue(':email', $_POST["email"]);
        $req->execute();
        $user = $req->fetch();

        //if the email doesn't exist
        if (!$user) {
            die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR 
                    <br>
                    Sorry the information you entered are not valid :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="login.php">
                    Return
                </a>
            </button>
        </div>');
        }

        //here we have a verified user we can now compare the password
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
          die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR 
                    <br>
                    Sorry the information you entered are not valid :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="login.php">
                    Return
                </a>
            </button>
        </div>');
        }
        

    } else {
        die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR
                    <br>
                    Sorry the form seems to be incomplete :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="login.php">
                    Return
                </a>
            </button>
        </div>');
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