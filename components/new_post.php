<!-- New post ----------------------------------------------------------------------------->
<div id="new-post" class="p-3">
    <form method="post" enctype="multipart/form-data">
        <div class="cp-container pb-4">
            <p>
                Write something fun
            </p>
        </div>
        <div class="cp-description control">
            <textarea class="p-1 box" name="content" id="cp-input"></textarea>
        </div>
        <div class="cp-assets p-3">
            <input type="file" name="media" class="p-1 c-button"></input>
        </div>
        <div class="cp-assets">
            <label for="training">Select Training (Optional):</label>
            <select class="select" name="training_id" id="training">
                <option value="">None</option>
                <?php foreach ($trainings as $training) : ?>
                    <option value="<?= $training['id']; ?>"><?= htmlspecialchars($training['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="is-flex is-justify-content-space-around">
            <button class="mt-3 p-1 c-button" onclick="closeNewPost()">Close</button>
            <button class="mt-3 p-1 c-button" type="submit">Post it</button>
        </div>
    </form>
</div>