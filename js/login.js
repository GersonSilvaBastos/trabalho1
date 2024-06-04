document.getElementById('loginForm').addEventListener('submit', function(event) {
    const email = this.email.value;
    const password = this.password.value;

    if (!email || !password) {
        alert('All fields are required!');
        event.preventDefault();
    } else if (!validateEmail(email)) {
        alert('Please enter a valid email address!');
        event.preventDefault();
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}


