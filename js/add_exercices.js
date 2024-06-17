// Import GSAP if not already imported in HTML
// import { gsap } from 'gsap';

document.addEventListener('DOMContentLoaded', () => {
    const addExerciseButton = document.getElementById('add-exercise');
    const exerciseForm = document.getElementById('exercise-form');
    const modalClose = document.querySelector('.modal-close');
    const modalBackground = document.querySelector('.modal-background');

    const showForm = () => {
        gsap.to(exerciseForm, { opacity: 1, display: 'block', duration: 0.5 });
    };

    const hideForm = () => {
        gsap.to(exerciseForm, { opacity: 0, display: 'none', duration: 0.5 });
    };

    // Function to handle click on add.svg button
    const handleAddExerciseClick = (e) => {
        e.preventDefault();
        showForm();
    };

    if (addExerciseButton) {
        addExerciseButton.addEventListener('click', handleAddExerciseClick);
    }

    if (modalClose) {
        modalClose.addEventListener('click', hideForm);
    }

    if (modalBackground) {
        modalBackground.addEventListener('click', hideForm);
    }
});
