
$(document).ready(function () {
    $('#cadastrarProduto').click(function (event) {
        event.preventDefault();

        var formData = new FormData();
       
        formData.append("nomeProduto", $('#nomeProduto').val());
        formData.append("descricaoProduto", $('#descricaoProduto').val());
        formData.append("valorProduto", $('#valorProduto').val());
        var categoria = $('#categoriaProduto').val();
        formData.append("categoriaProduto", categoria);
        formData.append("estoqueItem", $('#estoqueItem').val());

        // pega o arquivo
        var arquivo = $('#imagemProduto')[0].files[0];
        if (arquivo) {
            formData.append("imagemUsuario", arquivo);
        }

        $.ajax({
            url: "../php/gerenciarProdutos.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json"
        })
        .done(function (resposta) {
            if (resposta.status === "sucesso") {
                $('#resposta').html(`<div class="alert alert-success">${resposta.mensagem}</div>`);
                setTimeout(() => window.location.href = resposta.redirect, 1000);
            } else {
                $('#resposta').html(`<div class="alert alert-error">${resposta.mensagem}</div>`);
            }
        })
        .fail(function (jqXHR, textStatus) {
            $('#resposta').html(`<p style="color: red;">Erro: ${textStatus}</p>`);
        });
    });

    //Adicionar valor ao modal
    $(function() {
      $('#editarModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nome = button.data('produto_nome');
        var preco = button.data('preco');
        var categoria = button.data('categoria');
        var estoque = button.data('estoque');
        var modal = $(this);

        // aplicar valor ao id
        modal.find('#idProduto').val(id);
        modal.find('#nomeProduto').val(nome);
        modal.find('#precoProduto').val(preco);
        modal.find('#categoriaProduto').val(categoria);
        modal.find('#estoqueProduto').val(estoque);

      });

        $('#excluirModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var idExcluir = button.data('id');
        var nomeExcluir = button.data('nome_produto');
        var modalExcluir = $(this);
        // aplicar valor ao id
        modalExcluir.find('#excluirProduto').val(idExcluir);
        modalExcluir.find('#produtoExcluir').html(nomeExcluir);
      });
    });

        

});
