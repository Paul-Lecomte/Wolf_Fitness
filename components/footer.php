<!-- Footer ----------------------------------------------------------------------------->
      <footer id="footer">
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
            <a class="notif has-text-centered column is-one-quarter img is-32x32">
              <img src="assets/bell.svg" alt="Notifications button">
              <p>
                Notifications
              </p>
            </a>
            <?php if (isset($_SESSION["user"])): ?>
            <button class="post has-text-centered column is-one-quarter img is-32x32" onclick="newPost()">
              <img src="assets/plus.svg" alt="Post button">
              <p>
                Post
              </p>
            </button>
            <?php else: ?>
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