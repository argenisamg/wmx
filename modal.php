<!--MODAL ADD MATERIAL-->
<div class="modal fade" id="modal-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                  <h3 class="modal-title" id="exampleModalLabel">Inventory Details</h3>
                  <button type="button" class="close" id="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body">
                <div class="divrow">
                    <div id="elements-modal"></div>               
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
		// cierra la ventana modal al hacer clic en el botón
		document.querySelector(".close").addEventListener("click", function() {
            $("#modal-details").modal("hide");
         });        
</script>