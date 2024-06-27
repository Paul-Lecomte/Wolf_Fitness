document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('trainingModal');
    const closeModalBtn = document.querySelector('.delete');
    const closeModalFooterBtn = document.getElementById('closeModal');

    window.openTrainingModal = function(trainingId) {
        fetch(`../../components/fetch_training.php?training_id=${trainingId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const exercisesList = document.getElementById('exercisesList');
                exercisesList.innerHTML = '';

                if (data.error) {
                    exercisesList.textContent = data.error;
                } else {
                    data.exercises.forEach(exercise => {
                        // Create a container for each exercise
                        const exerciseContainer = document.createElement('div');
                        exerciseContainer.classList.add('exercise-item', 'box', 'my-2');

                        // Create paragraphs for exercise name and description
                        const exerciseName = document.createElement('p');
                        exerciseName.classList.add('title', 'is-5');
                        exerciseName.textContent = exercise.name;

                        const exerciseDescription = document.createElement('p');
                        exerciseDescription.textContent = exercise.description;

                        // Append paragraphs to container
                        exerciseContainer.appendChild(exerciseName);
                        exerciseContainer.appendChild(exerciseDescription);

                        // Append container to exercisesList
                        exercisesList.appendChild(exerciseContainer);
                    });
                }

                modal.classList.add('is-active');
            })
            .catch(error => console.error('Error:', error));
    }

    function closeTrainingModal() {
        modal.classList.remove('is-active');
    }

    closeModalBtn.addEventListener('click', closeTrainingModal);
    closeModalFooterBtn.addEventListener('click', closeTrainingModal);
});
