<!-- Header ----------------------------------------------------------------------------->
<nav class="navbar pers_navbar" style="width:100%">
        <a href="../feed/feed.php" class="logo py-3" >
          <img class="ml-6 image is-64x64" src="../../assets/logo.svg" alt="wolf fitness logo">
        </a>
        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <?php if (!isset($_SESSION["user"])): ?>
                    <a href="../credential/create_login.php" class="button is-primary">
                        <strong>Register</strong>
                    </a>
                    <a href="../credential/login.php" class="button is-info is-dark">
                        Login
                    </a>
                    <?php else: ?>
                    <a href="../credential/logout.php" class="button is-danger is-dark">
                        Logout
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
      </nav>