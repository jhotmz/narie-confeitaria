<?php
session_start();
// Caso exista login, redirecionar para a página home
if(isset($_SESSION['user_id'])){
    header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar</title>
    <link rel="stylesheet" href="../css/entrar.css">
    <link rel="stylesheet" href="../css/alert.css">
    <script src="../js/jquery-3.7.1.min.js"></script>
</head>

<body>
<body>
  <img src="../img/brigadeiro.png" class="brigadeiro">
  <div class="container">
    <h1>Faça login</h1>
    <p class="subtitulo">Por favor, entre para continuar</p>

    <div class="registro_usuario">
      <div class="input">
        <label for="cpf_usuario">CPF</label>
        <input type="text" id="cpf_usuario" name="cpf_usuario" placeholder="Digite seu CPF">
      </div>

      <div class="input">
        <label for="senha_usuario">Senha</label>
        <input type="password" id="senha_usuario" name="senha_usuario" placeholder="Digite sua senha">
      </div>

      <button type="submit" id="loginUsuario">Entrar</button>
    </div>
    <p id="resposta"></p>

    <p class="login">Não tem uma conta? <a href="registro.php">Registre-se já</a></p>
  </div>
</body>

    <script>
                   const inputNumero = document.getElementById('cpf_usuario');
        const limite = 11;

        inputNumero.addEventListener('input', function() {
          if (this.value.length > limite) {
            this.value = this.value.slice(0, limite); // Corta o excesso
          }
        });
$(document).ready(function() {
    $('#loginUsuario').click(function(event) {
        event.preventDefault();
        var cpf = $('#cpf_usuario').val();
        var senha = $('#senha_usuario').val();

        $.ajax({
            url: "../php/logar.php",
            type: "POST",
            data: {
                cpfUsuario: cpf,
                senhaUsuario: senha
            },
            dataType: "json" // agora trabalhamos com JSON
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