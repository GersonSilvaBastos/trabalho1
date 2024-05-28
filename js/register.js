function validarRegistro() {
    console.log("Validando registro...");

    var nome = document.getElementById("nome").value;
    var email = document.getElementById("email").value;
    var senha = document.getElementById("senha").value;
    var confirmarSenha = document.getElementById("confirmar-senha").value;

    if (nome === "" || email === "" || senha === "" || confirmarSenha === "") {
        alert("Por favor, preencha todos os campos obrigatórios.");
        return false;
    }
    if (senha !== confirmarSenha) {
        alert("As senhas não coincidem. Por favor, insira a mesma senha nos dois campos.");
        return false;
    }
    return true;
}
});

