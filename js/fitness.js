function openEditModal(id, name, description) {
    document.getElementById('edit_training_id').value = id;
    document.getElementById('edit_training_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('editModal').classList.add('is-active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('is-active');
}