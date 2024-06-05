<!-- Header ----------------------------------------------------------------------------->
<nav class="navbar pers_navbar" style="width:100%">
        <a href="feed.php" class="logo py-3" >
          <img class="ml-6 image is-64x64" src="./assets/logo.svg" alt="wolf fitness logo">
        </a>
        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <?php if (!isset($_SESSION["user"])): ?>
                    <a href="create_login.php" class="button is-primary">
                        <strong>S'inscrire</strong>
                    </a>
                    <a href="login.php" class="button is-info is-dark">
                        Se connecter
                    </a>
                    <?php else: ?>
                    <a href="logout.php" class="button is-danger is-dark">
                        Logout
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
      </nav>