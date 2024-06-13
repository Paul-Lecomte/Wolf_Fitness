<!-- Footer ----------------------------------------------------------------------------->
      <footer id="footer" style="background-color: #14161a">
        <nav class="pers-footer navbar pers_navbar  mt-2">
          <div class="m-0 my-3 columns" style="width:100%">
            <a class="home has-text-centered column is-one-quarter img is-32x32" href="feed.php">
              <img src="assets/home.svg" alt="Home button">
              <p>
                Home
              </p>
            </a>
            <button class="search has-text-centered column is-one-quarter img is-32x32" onclick="">
              <img src="assets/magnifier.svg" alt="Search button">
              <p>
                Search
              </p>
            </button>
            <?php if (isset($_SESSION["user"])): ?>
            <button type="button" class="post has-text-centered column is-one-quarter img is-32x32">
              <a href="profile.php">
                <img src="assets/profile.svg" alt="profile">
                <p>
                  Profile
                </p>
              </a>
            </button>
            <button class="post has-text-centered column is-one-quarter img is-32x32" onclick="newPost()">
              <img src="assets/plus.svg" alt="Post button">
              <p>
                Post
              </p>
            </button>
            <?php else: ?>
            <button type="button" class="post has-text-centered column is-one-quarter img is-32x32">
              <a href="login.php">
                <img src="assets/profile.svg" alt="profile">
                <p>
                  Profile
                </p>
              </a>
            </button>
            <button type="button" class="post has-text-centered column is-one-quarter img is-32x32">
              <a href="login.php">
                <img src="assets/plus.svg" alt="Post button">
                <p>
                  Post
                </p>
              </a>
            </button>
            <?php endif; ?>
          </div>
        </nav> 
      </footer>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js"></script>
    <script src="js/footer.js"></script>
    <script src="js/animation.js"></script>
  </body>