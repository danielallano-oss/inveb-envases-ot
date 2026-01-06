<article class="history-box p-3 bg-white shadow type-<?php echo rand(1, 4); ?>" data-component="history-box">
	<div class="form-row row-history-box">
		<div class="col profile text-center">
			<img src="{{asset('img/profile-default.jpg')}}" alt="Profile" class="rounded-circle img-thumbnail" width="80" height="80">
			<div data-container="profile">
				<div class="font-weight-bold" data-attribute="name">Francisco Rojas</div>
				<div data-attribute="role">Ingeniero</div>
			</div>
		</div>
		<div class="col d-flex flex-column" data-container="content">
			<h2 class="h6" data-attribute="title">Creación OT Caja Clementina</h2>
			<div class="content" data-attribute="content">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim nobis sed, ipsum. Aperiam voluptates, in eaque voluptas quidem. Possimus, earum?</p>
			</div>
			<div class="footer mt-auto">
				<div class="row comment-data mb-2">
					<div class="col-auto">
						<span class="text-muted">Tipo de gestión: </span>
						<span class="text-primary" data-attribute="history-status">Proceso de ventas</span>
					</div>
					<div class="col-auto ml-auto">
						<span class="text-muted">Área: </span>
						<span class="text-primary" data-attribute="history-area">Diseño</span>
					</div>
				</div>
				<div class="row comment-data">
					<div class="col-auto">
						<span class="text-muted">Fecha: </span>
						<span class="text-primary" data-attribute="creation-date">14/08/2019 14:45</span>
					</div>
					<div class="col-auto ml-auto">
						<span class="text-muted">Archivos subidos: </span>
						<span class="text-primary" data-attribute="files-count">3</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</article>