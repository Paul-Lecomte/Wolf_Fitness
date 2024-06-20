function showDescriptionModal(description) {
    const descriptionContent = document.getElementById('exercise-description-content');
    descriptionContent.textContent = description;
    const modal = document.getElementById('exercise-description-modal');
    modal.classList.add('is-active');
}

function hideDescriptionModal() {
    const modal = document.getElementById('exercise-description-modal');
    modal.classList.remove('is-active');
}