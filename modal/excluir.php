<div class="modal fade" id="excluirModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tem certeza que deseja excluir o produto?(PARA SEMPRE!)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="../php/excluirProduto.php" method="POST">
        <input type="hidden" name="excluirProduto" id="excluirProduto">
        Você tem certeza que deseja excluir o produto:
        <h3 name="produtoExcluir" id="produtoExcluir"></h3>
         <button type="submit" class="btn btn-danger">Excluir</button>
        </form>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>