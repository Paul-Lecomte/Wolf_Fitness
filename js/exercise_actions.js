// exercise_actions.js

document.addEventListener('DOMContentLoaded', () => {
    const addExerciseButton = document.getElementById('add-exercise');
    const exerciseForm = document.getElementById('exercise-form');
    const editExerciseButtons = document.querySelectorAll('.edit-exercise');
    const editExerciseForm = document.getElementById('edit-exercise-form');
    const modalCloseButtons = document.querySelectorAll('.modal-close');
    const modalBackgrounds = document.querySelectorAll('.modal-background');

    const showForm = (form) => {
        gsap.to(form, {opacity: 1, display: 'block', duration: 0.5});
    };

    const hideForm = (form) => {
        gsap.to(form, {opacity: 0, display: 'none', duration: 0.5});
    };

    addExerciseButton.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(exerciseForm);
    });

    editExerciseButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const exerciseId = button.getAttribute('data-exercise-id');
            const exerciseName = button.getAttribute('data-exercise-name');
            const description = button.getAttribute('data-description');
            document.getElementById('edit-exercise-id').value = exerciseId;
            document.getElementById('edit-exercise-name').value = exerciseName;
            document.getElementById('edit-description').value = description;
            showForm(editExerciseForm);
        });
    });

    modalCloseButtons.forEach(button => {
        button.addEventListener('click', () => {
            hideForm(button.closest('.modal'));
        });
    });

    modalBackgrounds.forEach(background => {
        background.addEventListener('click', () => {
            hideForm(background.closest('.modal'));
        });
    });
});
