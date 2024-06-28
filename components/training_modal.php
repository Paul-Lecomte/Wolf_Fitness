<!-- Modal structure -->
<div id="trainingModal" class="modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Training Details</p>
      <button class="delete" aria-label="close"></button>
    </header>
    <section class="modal-card-body">
      <!-- Training exercises will be dynamically loaded here -->
      <div id="exercisesList"></div>
    </section>
    <footer class="modal-card-foot">
      <button class="button" id="closeModal">Cancel</button>
      <form method="POST">
        <button type="submit" class="button" name="add-training" value="add-training">Add to your trainings</button>
      </form>
    </footer>
  </div>
</div>