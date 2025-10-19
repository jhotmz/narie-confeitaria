<?php
require_once("../script/dbd.php");  
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/registro.css">
    <script src="../js/jquery-3.7.1.min.js"></script>
</head>

<style>
    .alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin: 10px 0;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: fadeIn 0.3s ease-in-out;
    display: flex;
    justify-content: center;
}

.alert-success {
    background-color: #e6ffed;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {

    background-color: #ffe6e6;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>

<body>
<div class="container">

    <h1>Crie sua conta</h1>
    <p class="subtitulo">Preencha os campos abaixo para se registrar</p>

    <section>
      <div class="registro_usuario">
        <div class="input">
          <label for="nome_usuario">Nome</label>
          <input type="text" name="nome_usuario" id="nome_usuario" placeholder="Digite seu nome">
        </div>

        <div class="input">
          <label for="sobrenome_usuario">Sobrenome</label>
          <input type="text" name="sobrenome_usuario" id="sobrenome_usuario" placeholder="Digite seu sobrenome">
        </div>

        <div class="input">
          <label for="cpf_usuario">CPF</label>
          <input type="number" name="cpf_usuario" id="cpf_usuario" placeholder="Digite seu CPF" max="1">
        </div>

        <div class="input">
          <label for="email_usuario">Email</label>
          <input type="email" name="email_usuario" id="email_usuario" placeholder="Digite seu email">
        </div>

        <div class="input">
          <label for="senha_usuario">Senha</label>
          <input type="password" name="senha_usuario" id="senha_usuario" placeholder="Digite sua senha">
        </div>

        <div class="input">
          <label for="confirmarSenha_usuario">Confirme a senha</label>
          <input type="password" name="confirmarSenha_usuario" id="confirmarSenha_usuario" placeholder="Confirme sua senha">
        </div>
      <p id="resposta"></p>
        <button type="submit" id="registrarUsuario">Registrar</button>
  

        <p class="login">Já tem uma conta? <a href="entrar.php">Faça login</a></p>
      </div>
        
    </section>
  </div>
  <script>

           const inputNumero = document.getElementById('cpf_usuario');
        const limite = 11;

        inputNumero.addEventListener('input', function() {
          if (this.value.length > limite) {
            this.value = this.value.slice(0, limite); // Corta o excesso
          }
        });

    $(document).ready(function() {
    $('#registrarUsuario').click(function(event) {
        event.preventDefault();
        var nome = $('#nome_usuario').val();
        var sobrenome = $('#sobrenome_usuario').val();
        var cpf = $('#cpf_usuario').val();
        var email = $('#email_usuario').val();
        var senha = $('#senha_usuario').val();
        var confirmarSenha = $('#confirmarSenha_usuario').val();

        $.ajax({
            url: "../php/registro.php",
            type: "POST",
            data: {
                nomeUsuario: nome,
                sobrenomeUsuario: sobrenome,
                cpfUsuario: cpf,
                emailUsuario: email,
                senhaUsuario: senha,
                confirmarSenha: confirmarSenha
            },
            dataType: "json"
        })
.done(function(resposta) {
    if (resposta.status === "sucesso") {
        $('#resposta').html(`<div class="alert alert-success">${resposta.mensagem}</div>`);

        setTimeout(() => {
            window.location.href = resposta.redirect;
        }, 1000);
    } else {
        $('#resposta').html(`<div class="alert alert-error">${resposta.mensagem}</div>`);
    }
})
        .fail(function(jqXHR, textStatus) {
            $('#resposta').html(
                `<p style="color: red;">Erro: ${textStatus}</p>`
            );
        });
    });
});

  </script>
</body>
</html>