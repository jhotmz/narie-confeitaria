const buttonMenu = document.querySelector("#button-menu");
const menuWide = document.querySelector(".menu-wide");
const overlay = document.querySelector(".overlay");
const closeMenu = document.querySelector("#close-menu");

// Abre
buttonMenu.addEventListener("click", () => {
  menuWide.classList.add("active");
  overlay.classList.add("active");
});

// Fecha com o botão X
closeMenu.addEventListener("click", () => {
  menuWide.classList.remove("active");
  overlay.classList.remove("active");
});

// Fecha clicando no overlay
overlay.addEventListener("click", () => {
  menuWide.classList.remove("active");
  overlay.classList.remove("active");
});

// Fecha ao clicar em um link
menuWide.querySelectorAll("a").forEach(link => {
  link.addEventListener("click", () => {
    menuWide.classList.remove("active");
    overlay.classList.remove("active");
  });
});

$(document).ready(function () {

    $('.btnAdicionar').click(function (event) {
      let produto_id = $(this).closest(".produto").data("id");  
      
        $.ajax({
            url: "php/adicionar_sacola.php",
            type: "POST",
            data: {
              produto_id: produto_id
            },
            dataType: "json"
        })
        .done(function (resposta) {
            if (resposta.status === "sucesso") {
               alert(resposta.mensagem);
               //atualiza sacola
               atualizarBarraSacola();
            } else {
                alert(resposta.mensagem);
            }
        })
        .fail(function (jqXHR, textStatus) {
            $('#resposta').html(`<p style="color: red;">Erro: ${textStatus}</p>`);
        });
    });
});

// função de barra fixa na tela, exbibida quando o usuário adiciona algum produto na sacola
  function atualizarBarraSacola() {
  $.ajax({
    url: "php/sacola_total.php",
    method: "GET",
    dataType: "json"
  }).done(function(res) {
    if (res.quantidade > 0) {
      $('#totalItens').text(`${res.quantidade} itens | R$ ${res.total}`);
      $('#barraSacola').css('bottom', '0'); // mostra barra
    } else {
      $('#barraSacola').css('bottom', '-100px'); // esconde barra
    }
  });
}

// chama função quando carrega a página
$(document).ready(function() {
  atualizarBarraSacola();
});

// adicionar produto na sacola
$(document).on('click', '.btnAdicionar', function(e) {
  e.preventDefault();
  const produto_sacola = $(this).closest('.produto').data('id');

  if (!produto_sacola) return;

  $.ajax({
    url: "php/adicionar_sacola.php",
    method: "POST",
    data: { 
       produto_sacola: produto_sacola 
      },
    dataType: "json"
  }).done(function(res) {
    if (res.status === "sucesso") {
      atualizarBarraSacola();
    }
  });
});






