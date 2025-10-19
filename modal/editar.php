<div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Editar Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="../php/editarProduto.php" method="POST" enctype="multipart/form-data">
        <label>Alterar imagem do produto</label>
        <input type="file" name="imagemProduto" accept="image/*">
        <input type="hidden" name="idProduto" id="idProduto">
        nome
        <input type="text" name="nomeProduto" id="nomeProduto" placeholder="nome">
        preco
        <input type="number" name="precoProduto" id="precoProduto" placeholder="preço">
        categoria
        <input type="text" name="categoriaProduto" id="categoriaProduto" placeholder="categoria">        estoque
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Editar</button>
        </form>
      </div>
    </div>
  </div>
</div>