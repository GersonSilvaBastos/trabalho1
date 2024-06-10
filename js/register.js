document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        const nome = this.name.value;
        const email = this.email.value;
        const senha = this.password.value;
        const confirmarSenha = this.confirm_password.value;

        if (!nome || !email || !senha || !confirmarSenha) {
            displayMessage('All fields are required!', 'error');
            event.preventDefault();
        } else if (senha !== confirmarSenha) {
            displayMessage('Passwords do not match!', 'error');
            event.preventDefault();
        } else if (!validateEmail(email)) {
            displayMessage('Please enter a valid email address!', 'error');
            event.preventDefault();
        }
    });
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function displayMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.innerText = message;
    document.body.prepend(messageDiv);
    setTimeout(() => messageDiv.remove(), 3000);
}
