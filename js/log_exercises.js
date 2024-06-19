document.addEventListener('DOMContentLoaded', () => {
    const logSetButton = document.getElementById('log-set');
    const logForm = document.getElementById('log-form');
    const updateForm = document.getElementById('update-form');
    const modalCloseButtons = document.querySelectorAll('.modal-close');

    if (logSetButton) {
        logSetButton.addEventListener('click', () => {
            logForm.classList.add('is-active');
        });
    }

    document.querySelectorAll('.edit-set').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const logId = button.getAttribute('data-log-id');
            const reps = button.getAttribute('data-reps');
            const weight = button.getAttribute('data-weight');

            document.getElementById('update-log-id').value = logId;
            document.getElementById('update-reps').value = reps;
            document.getElementById('update-weight').value = weight;

            updateForm.classList.add('is-active');
        });
    });

    modalCloseButtons.forEach(button => {
        button.addEventListener('click', () => {
            logForm.classList.remove('is-active');
            updateForm.classList.remove('is-active');
        });
    });
});