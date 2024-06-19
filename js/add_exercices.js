document.addEventListener('DOMContentLoaded', () => {
    const addExerciseButton = document.getElementById('add-exercise');
    const logSetButton = document.getElementById('log-set');
    const exerciseForm = document.getElementById('exercise-form');
    const logForm = document.getElementById('log-form');
    const modalCloseButtons = document.querySelectorAll('.modal-close');

    if (addExerciseButton) {
        addExerciseButton.addEventListener('click', () => {
            exerciseForm.classList.add('is-active');
        });
    }

    if (logSetButton) {
        logSetButton.addEventListener('click', () => {
            logForm.classList.add('is-active');
        });
    }

    modalCloseButtons.forEach(button => {
        button.addEventListener('click', () => {
            exerciseForm.classList.remove('is-active');
            logForm.classList.remove('is-active');
        });
    });
});

