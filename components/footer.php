<!-- Footer ----------------------------------------------------------------------------->
      <footer id="footer" style="background-color: #14161a">
        <nav class="pers-footer navbar pers_navbar  mt-2">
          <div class=" my-3 is-flex is-justify-content-space-evenly is-align-items-center" style="width:100%">
            <a class="home has-text-centered img is-32x32" href="../feed/feed.php">
              <img src="../../assets/home.svg" alt="Home button">
              <p style="color: #657786;">
                Home
              </p>
            </a>
            <button class="search has-text-centered img is-32x32" onclick="">
              <a href="../search/search.php">
                <img src="../../assets/magnifier.svg" alt="Search button">
                <p style="color: #657786;">
                  Search
                </p>
              </a>
            </button>
            <?php if (isset($_SESSION["user"])): ?>
            <button type="button" class="post has-text-centered img is-32x32">
              <a href="../fitness/fitness.php">
                <img src="../../assets/training.svg" alt="trainings">
              </a>
            </button>
            <button type="button" class="post has-text-centered img is-32x32">
              <a href="../profile/profile.php">
                <img src="../../assets/profile.svg" alt="profile">
                <p style="color: #657786;">
                  Profile
                </p>
              </a>
            </button>
            <?php else: ?>
            <button type="button" class="post has-text-centered img is-32x32">
              <a href="../credential/login.php">
                <img src="../../assets/profile.svg" alt="profile">
                <p style="color: #657786;">
                  Profile
                </p>
              </a>
            </button>
            <button type="button" class="post has-text-centered img is-32x32">
              <a href="../credential/login.php">
                <img src="../../assets/plus.svg" alt="Post button">
                <p style="color: #657786;">
                  Post
                </p>
              </a>
            </button>
            <?php endif; ?>
            <?php if (function_exists('validatepost')): ?>
              <button class="post has-text-centered img is-32x32" onclick="newPost()">
                <img src="../../assets/plus.svg" alt="Post button">
                <p style="color: #657786;">
                  Post
                </p>
              </button>
            <?php else: ?>
              <button type="button" class="post has-text-centered img is-32x32">
                <a href="../feed/feed.php">
                  <img src="../../assets/plus.svg" alt="Post button">
                  <p style="color: #657786;">
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
    <script src="../../js/footer.js"></script>
    <script src="../../js/training-modal.js"></script>
    <script src="../../js/animation.js"></script>
  </body>