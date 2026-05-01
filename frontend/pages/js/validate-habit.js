// validate-habit.js
// Client-side validation for Add and Edit Habit forms
// Developer: Sashi Khatri

function validateForm() {

    const habitName     = document.getElementById('habitName').value.trim();
    const targetPerWeek = document.getElementById('targetPerWeek').value;

    // Check habit name is not empty
    if (habitName === '') {
        alert('Habit name is required.');
        return false;
    }

    // Check habit name length
    if (habitName.length > 50) {
        alert('Habit name must be 50 characters or less.');
        return false;
    }

    // Check target per week is between 1 and 7
    if (targetPerWeek < 1 || targetPerWeek > 7) {
        alert('Target per week must be between 1 and 7.');
        return false;
    }

    return true;
}