<!-- Header ----------------------------------------------------------------------------->
      <nav class="navbar pers_navbar" style="width:100%">
        <a href="feed.php" class="logo container py-3" >
          <img style="max-width: 5%" class="m-auto" src="./assets/logo.svg" alt="wolf fitness logo">
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