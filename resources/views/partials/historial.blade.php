<section id="historial" data-container="history">
	<h2 class="page-title">Actividades</h2>
	@if(count($ot->gestiones))
		@if(auth()->user()->isSuperAdministrador())
			@foreach($ot->gestiones as $gestion)
				<div class="row mb-3 no-gutters row-item">
					<div class="col-10">
						<article class="history-box p-3 bg-white shadow type-{{$gestion->management_type_id}}" data-component="history-box">
							<div class="form-row row-history-box">
								<div class="col profile text-center">
									<img src="{{asset('img/profile-default.jpg')}}" alt="Profile" class="rounded-circle img-thumbnail" width="80" height="80">
									<div data-container="profile">
										<div class="font-weight-bold" data-attribute="name">{{$gestion->user->fullname}}</div>
										<div data-attribute="role">{{$gestion->user->role->nombre}}</div>
									</div>
								</div>
								<div class="col d-flex flex-column" data-container="content">
									<!-- <h2 class="h6" data-attribute="title">$gestion->titulo</h2> -->
									<div class="content" data-attribute="content">
										<p>{{$gestion->observacion}}</p>
									</div>
									<div class="footer mt-auto">
										<div class="row comment-data mb-2">
											<div class="col-auto">
												<span class="text-muted">Tipo de gestión: </span>
												<span class="text-primary" data-attribute="history-status">{{$gestion->type->nombre}}</span>
												<br>
												@if($gestion->type->id == 8)
												@if(!is_null($gestion->muestra_id))
													<span class="text-muted">ID Muestra: </span>
													<span class="text-primary" data-attribute="history-status">{{$gestion->muestra_id}}</span>
													<br>
												@endif
												@endif
												@if($gestion->type->id == 9 || $gestion->type->id == 10)
												@if(!is_null($gestion->proveedor_id))
													<span class="text-muted">Proveedor: </span>
													<span class="text-primary" data-attribute="history-status">{{$gestion->proveedor->name}}</span>
													<br>
												@endif
												@endif
												@if(!empty($gestion->state_id))
												<br>
												<span class="text-muted">Nuevo Estado: </span>
												<span class="text-primary" data-attribute="history-status">{{$gestion->state->nombre ?? 'N/A'}}</span>
												@if($gestion->state_id == 12)
												<br>
												<span class="text-muted">Motivo de Rechazo: </span>
												<span class="text-primary" data-attribute="history-status">{{$motivos[$gestion->motive_id] ?? 'N/A'}}</span>
												<br>
												<span class="text-muted">Área: </span>
												<span class="text-primary" data-attribute="history-status">{{$gestion->area_consultada->nombre ?? 'N/A'}}</span>
												@endif
												@endif

												@if($gestion->type->id == 2)
												<br>
												<span class="text-muted">Área Consultada: </span>
												<span class="text-primary" data-attribute="files-count">{{$gestion->area_consultada->nombre ?? 'N/A'}}</span>
												<br>
												@if(!is_null($gestion->muestra_id))
													<span class="text-muted">ID Muestra: </span>
													<span class="text-primary" data-attribute="history-status">{{$gestion->muestra_id}}</span>
													<br>
												@endif
												<span class="text-muted">Estado: </span>
												<span class="text-primary" data-attribute="files-count">
													@if(empty($gestion->respuesta))
													Por Responder
													@else Respondida
													@endif
												</span>
												@endif
											</div>
											<div class="col-auto ml-auto">
												<span class="text-muted">Área: </span>
												<span class="text-primary" data-attribute="history-area">{{$gestion->area->nombre ?? 'N/A'}}</span>
											</div>
										</div>
										<div class="row comment-data">
											<div class="col-auto">
												<span class="text-muted">Fecha: </span>
												<span class="text-primary" data-attribute="creation-date">{{$gestion->created_at->format('d/m/Y H:i')}}</span>
											</div>
											<div class="col-auto ml-auto">
												<span class="text-muted">Archivos subidos: </span>
												<span class="text-primary" data-attribute="files-count">{{count($gestion->files)}}</span>
											</div>

										</div>

										<!-- Solo si es una gestion de consulta con respuesta -->
										@if($gestion->type->id == 2 && $gestion->respuesta)
										<br>
										<div class="form-group form-row">
											<div class="col">
												<span class="text-muted">Respondida Por: </span>
												<span class="text-primary">{{$gestion->respuesta->user->fullname}}</span>
												<br>
												<span class="text-muted">Fecha: </span>
												<span class="text-primary">{{$gestion->respuesta->created_at->format('d/m/Y H:i')}}</span>
												<br>
												<span class="text-muted">Respuesta: </span>
												<span class="text-primary">{{$gestion->respuesta->respuesta}}</span>
											</div>
										</div>
										@endif
									</div>
								</div>
							</div>
							<!-- Si la gestion es del tipo consulta, aun no ha sido respondida y el area a la que fue consultada es igual al area del user logueado o en caso de q el user sea catalogador pueda verla si es para el area 4 o 5 (catalogacion y precatalogacion) -->
							@if($gestion->type->id == 2 && !$gestion->respuesta && (isset(auth()->user()->role->area) && $gestion->consulted_work_space_id == auth()->user()->role->area->id || (isset(auth()->user()->role->area) && auth()->user()->role->area->id == 4 && ($gestion->consulted_work_space_id == 4 || $gestion->consulted_work_space_id == 5)) ) )

							<div class="card-body">
								<form method="POST" action="{{ route('respuesta', $gestion->id) }}" enctype="multipart/form-data">
									@csrf
									<div class="form-group form-row">
										<label for="respuesta" class="col-12 col-form-label">Respuesta</label>
										<div class="col">
											<textarea class="form-control" name="respuesta" id="respuesta" cols="30" rows="2" required></textarea>
										</div>
									</div>
									<div class="row ml-1">
										<button type="submit" class="btn btn-primary px-5">Enviar Respuesta</button>
									</div>
								</form>
							</div>
							@endif
						</article>
					</div>
				</div>						
			@endforeach
		@else
			@foreach($ot->gestiones as $gestion)
				@if($gestion->user->role->id!=18)
					<div class="row mb-3 no-gutters row-item">
						<div class="col-10">
							<article class="history-box p-3 bg-white shadow type-{{$gestion->management_type_id}}" data-component="history-box">
								<div class="form-row row-history-box">
									<div class="col profile text-center">
										<img src="{{asset('img/profile-default.jpg')}}" alt="Profile" class="rounded-circle img-thumbnail" width="80" height="80">
										<div data-container="profile">
											<div class="font-weight-bold" data-attribute="name">{{$gestion->user->fullname}}</div>
											<div data-attribute="role">{{$gestion->user->role->nombre}}</div>
										</div>
									</div>
									<div class="col d-flex flex-column" data-container="content">
										<!-- <h2 class="h6" data-attribute="title">$gestion->titulo</h2> -->
										<div class="content" data-attribute="content">
											<p>{{$gestion->observacion}}</p>
										</div>
										<div class="footer mt-auto">
											<div class="row comment-data mb-2">
												<div class="col-auto">
													<span class="text-muted">Tipo de gestión: </span>
													<span class="text-primary" data-attribute="history-status">{{$gestion->type->nombre}}</span>
													<br>
													@if($gestion->type->id == 8)
													@if(!is_null($gestion->muestra_id))
														<span class="text-muted">ID Muestra: </span>
														<span class="text-primary" data-attribute="history-status">{{$gestion->muestra_id}}</span>
														<br>
													@endif
													@endif
													@if($gestion->type->id == 9 || $gestion->type->id == 10)
													@if(!is_null($gestion->proveedor_id))
														<span class="text-muted">Proveedor: </span>
														<span class="text-primary" data-attribute="history-status">{{$gestion->proveedor->name}}</span>
														<br>
													@endif
													@endif
													@if(!empty($gestion->state_id))
													<br>
													<span class="text-muted">Nuevo Estado: </span>
													<span class="text-primary" data-attribute="history-status">{{$gestion->state->nombre ?? 'N/A'}}</span>
													@if($gestion->state_id == 12)
													<br>
													<span class="text-muted">Motivo de Rechazo: </span>
													<span class="text-primary" data-attribute="history-status">{{$motivos[$gestion->motive_id] ?? 'N/A'}}</span>
													<br>
													<span class="text-muted">Área: </span>
													<span class="text-primary" data-attribute="history-status">{{$gestion->area_consultada->nombre ?? 'N/A'}}</span>
													@endif
													@endif

													@if($gestion->type->id == 2)
													<br>
													<span class="text-muted">Área Consultada: </span>
													<span class="text-primary" data-attribute="files-count">{{$gestion->area_consultada->nombre ?? 'N/A'}}</span>
													<br>
													@if(!is_null($gestion->muestra_id))
														<span class="text-muted">ID Muestra: </span>
														<span class="text-primary" data-attribute="history-status">{{$gestion->muestra_id}}</span>
														<br>
													@endif
													<span class="text-muted">Estado: </span>
													<span class="text-primary" data-attribute="files-count">
														@if(empty($gestion->respuesta))
														Por Responder
														@else Respondida
														@endif
													</span>
													@endif
												</div>
												<div class="col-auto ml-auto">
													<span class="text-muted">Área: </span>
													<span class="text-primary" data-attribute="history-area">{{$gestion->area->nombre ?? 'N/A'}}</span>
												</div>
											</div>
											<div class="row comment-data">
												<div class="col-auto">
													<span class="text-muted">Fecha: </span>
													<span class="text-primary" data-attribute="creation-date">{{$gestion->created_at->format('d/m/Y H:i')}}</span>
												</div>
												<div class="col-auto ml-auto">
													<span class="text-muted">Archivos subidos: </span>
													<span class="text-primary" data-attribute="files-count">{{count($gestion->files)}}</span>
												</div>

											</div>

											<!-- Solo si es una gestion de consulta con respuesta -->
											@if($gestion->type->id == 2 && $gestion->respuesta)
											<br>
											<div class="form-group form-row">
												<div class="col">
													<span class="text-muted">Respondida Por: </span>
													<span class="text-primary">{{$gestion->respuesta->user->fullname}}</span>
													<br>
													<span class="text-muted">Fecha: </span>
													<span class="text-primary">{{$gestion->respuesta->created_at->format('d/m/Y H:i')}}</span>
													<br>
													<span class="text-muted">Respuesta: </span>
													<span class="text-primary">{{$gestion->respuesta->respuesta}}</span>
												</div>
											</div>
											@endif
										</div>
									</div>
								</div>
								<!-- Si la gestion es del tipo consulta, aun no ha sido respondida y el area a la que fue consultada es igual al area del user logueado o en caso de q el user sea catalogador pueda verla si es para el area 4 o 5 (catalogacion y precatalogacion) -->
								@if($gestion->type->id == 2 && !$gestion->respuesta && (isset(auth()->user()->role->area) && $gestion->consulted_work_space_id == auth()->user()->role->area->id || (isset(auth()->user()->role->area) && auth()->user()->role->area->id == 4 && ($gestion->consulted_work_space_id == 4 || $gestion->consulted_work_space_id == 5)) ) )

								<div class="card-body">
									<form method="POST" action="{{ route('respuesta', $gestion->id) }}" enctype="multipart/form-data">
										@csrf
										<div class="form-group form-row">
											<label for="respuesta" class="col-12 col-form-label">Respuesta</label>
											<div class="col">
												<textarea class="form-control" name="respuesta" id="respuesta" cols="30" rows="2" required></textarea>
											</div>
										</div>
										<div class="row ml-1">
											<button type="submit" class="btn btn-primary px-5">Enviar Respuesta</button>
										</div>
									</form>
								</div>
								@endif
							</article>
						</div>
					</div>
				@endif		
			@endforeach
		@endif		
	@else
	<article>
		<div class="text-muted font-italic">Aún no hay actividades para mostrar</div>
	</article>
	@endif

</section>