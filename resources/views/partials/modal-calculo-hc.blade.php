<!-- MODAL DETALLE DE COTIZACION -->
<div class="modal fade" id="modal-calculo-hc">
	<div class="modal-dialog modal-xl " style="width:100%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title page-title">
					<h1 id="titulo-form-detalle" class="page-title">Cálculo HC y Cartón</h1>

				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" style="    background-color: #F2F4FD">
				<div id="" class="col-12 mb-2">
				<form id="formAreaHC" method="POST" action="{{ route('cotizador.crear_areahc') }}">
      @csrf
      @include('cotizador.areas-hc.form', ['tipo' => "create",'areahc' => null,'class' => '',])
    </form>
				</div>
			</div>
		</div>
	</div>
</div>