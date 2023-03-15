document.addEventListener('DOMContentLoaded', function () {
const passwordToggleButtons = document.querySelectorAll('.password-toggle');

passwordToggleButtons.forEach(function (toggleButton) {
const targetInputId = toggleButton.dataset.targetInput;
const passwordInput = document.querySelector(`input#${targetInputId}`);

if (!passwordInput) {
    console.error(`Password input with ID '${targetInputId}' not found`);
    return;
}

const toggleIcon = toggleButton.querySelector('i');

toggleButton.addEventListener('click', function (event) {
event.preventDefault();
if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    toggleIcon.classList.remove('fa-eye-slash');
    toggleIcon.classList.add('fa-eye');
} else {
    passwordInput.type = 'password';
    toggleIcon.classList.remove('fa-eye');
    toggleIcon.classList.add('fa-eye-slash');
}
});
});
});