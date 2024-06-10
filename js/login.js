document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        const email = this.email.value;
        const password = this.password.value;

        if (!email || !password) {
            displayMessage('All fields are required!', 'error');
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
