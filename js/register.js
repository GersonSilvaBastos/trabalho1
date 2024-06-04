document.getElementById('registerForm').addEventListener('submit', function(event) {
    const nome = this.nome.value;
    const email = this.email.value;
    const senha = this.senha.value;
    const confirmarSenha = this.confirmar_senha.value;

    if (!nome || !email || !senha || !confirmarSenha) {
        alert('All fields are required!');
        event.preventDefault();
    } else if (senha !== confirmarSenha) {
        alert('Passwords do not match!');
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


