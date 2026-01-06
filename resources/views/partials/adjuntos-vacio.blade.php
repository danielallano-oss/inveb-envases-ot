<section id="adjuntos" class="py-3 sticky-top">
	<div class="card">
		<div class="card-header">Archivos adjuntos</div>

		<div class="card-body">
			<div class="files">
				<div class="title">Areas</div>
				<div class="btn-group btn-group-sm btn-group-toggle pull-left" data-toggle="buttons">
					
					<label class="btn btn-light active">
						<span data-attribute="titulo" data-toggle="tooltip" title="Área de Ventas">
						<input type="radio" name="filetype-areas" id="area_1" value="area_1" style="display:none" checked><h6 style="color: #01a546;font-size: inherit;align-content: center;"><b>&nbsp;&nbsp;Ventas&nbsp;&nbsp;</b></h6>
						</span>
					</label>
					@if(!Auth()->user()->isVendedorExterno())
						<label class="btn btn-light">
							<span data-attribute="titulo" data-toggle="tooltip" title="Área de Diseño Estructural">
							<input type="radio" name="filetype-areas" id="area_2" value="area_2" style="display:none"><h6 style="color: #01a546;font-size: inherit;align-content: center;"><b>Diseño Estructural</b></h6>
							</span>
						</label>
					@endif
					<label class="btn btn-light">
						<span data-attribute="titulo" data-toggle="tooltip" title="Área de Diseño Gráfico">
						<input type="radio" name="filetype-areas" id="area_3" value="area_3" style="display:none"><h6 style="color: #01a546;font-size: inherit;align-content: center;"><b>Diseño Gráfico</b></h6>
						</span>
					</label>
					<label class="btn btn-light">
						<span data-attribute="titulo" data-toggle="tooltip" title="Área de Catalogación">
						<input type="radio" name="filetype-areas" id="area_4" value="area_4" style="display:none"><h6 style="color: #01a546;font-size: inherit;align-content: center;"><b>Catalogación</b></h6>
						</span>
					</label>											
				</div>
				<hr>
				<div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-light active">
						<input type="radio" name="filetype-options" id="all" value="all" checked>&nbsp;&nbsp; Todos &nbsp;&nbsp;
					</label>
					<label class="btn btn-light">
						<input type="radio" name="filetype-options" id="pdf" value="pdf">&nbsp;&nbsp; PDF &nbsp;&nbsp;
					</label>
					<label class="btn btn-light">
						<input type="radio" name="filetype-options" id="cad" value="cad">&nbsp;&nbsp; CAD &nbsp;&nbsp;
					</label>
					<label class="btn btn-light">
						<input type="radio" name="filetype-options" id="ofi" value="ofi">&nbsp;&nbsp; Office &nbsp;&nbsp;
					</label>
					<label class="btn btn-light">
						<input type="radio" name="filetype-options" id="img" value="img">&nbsp;&nbsp; Img &nbsp;&nbsp;
					</label>
					<label class="btn btn-light">
						<input type="radio" name="filetype-options" id="otr" value="otr">&nbsp;&nbsp; Otros &nbsp;&nbsp;
					</label>
				</div>

				<section class="file-container py-3" data-container="files">
					@if(count($files_by_area)<1 && count($files_develop_ventas)<1)
						<article>
							<div class="text-muted font-italic">Aún no hay documentos para mostrar</div>
						</article>
					@else
						@if(count($files_develop_ventas))
							@foreach($files_develop_ventas as $file_develop)
								<article data-component="archivo_area_1" data-file-type="area_1_{{$file_develop['tipo']}}" class="file-item mb-3 border-bottom pb-3">
									<div class="form-row">
										<div class="col-2">
											<img class="img-fluid icon" src="{{asset('img/filetype/'.$file_develop['tipo'].'.svg')}}" alt="Icon">
										</div> 
										<div class="col-10">
											<div class="title h6 mb-0 text-truncate"><span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$file_develop['url'])}}">{{str_replace('/files/','',$file_develop['url'])}}</span></div>
											<div class="extra-data">
												<div><span data-attribute="peso"></span>{{$file_develop['peso']}} <span data-attribute="peso-tipo">{{$file_develop['unidad']}}</span></div>
												<div><span data-attribute="fecha">{{Carbon\Carbon::parse($file_develop['created_at'])->format('d/m/Y H:i')}}</span></div>									
											</div>
											<div><a data-attribute="link" href="{{$file_develop['url']}}" download title="Descargar">Descargar</a></div>
										</div>
									</div>
								</article>
							@endforeach
						@endif
						@if(count($files_by_area))
							@foreach($files_by_area as $file)
								@if(in_array($file->role_id, [9,10,11,12]))
									<article data-component="archivo_area_4" data-file-type="area_4_{{$file->tipo}}" class="file-item mb-3 border-bottom pb-3">
										<div class="form-row">
											<div class="col-2">
												<img class="img-fluid icon" src="{{asset('img/filetype/'.$file->tipo.'.svg')}}" alt="Icon">
											</div> 
											<div class="col-10">
												<div class="title h6 mb-0 text-truncate"><span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$file->url)}}">{{str_replace('/files/','',$file->url)}}</span></div>
												<div class="extra-data">
													<div><span data-attribute="peso"></span>{{$file->peso}} <span data-attribute="peso-tipo">{{$file->unidad}}</span></div>
													<div><span data-attribute="fecha">{{Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i')}}</span></div>
												</div>
												<div><a data-attribute="link" href="{{$file->url}}" download title="Descargar">Descargar</a></div>
											</div>
										</div>
									</article>
								@else
									@if(in_array($file->role_id, [3,4]))
										<article data-component="archivo_area_1" data-file-type="area_1_{{$file->tipo}}" class="file-item mb-3 border-bottom pb-3">
											<div class="form-row">
												<div class="col-2">
													<img class="img-fluid icon" src="{{asset('img/filetype/'.$file->tipo.'.svg')}}" alt="Icon">
												</div>
												<div class="col-10">
													<div class="title h6 mb-0 text-truncate"><span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$file->url)}}">{{str_replace('/files/','',$file->url)}}</span></div>
													<div class="extra-data">
														<div><span data-attribute="peso"></span>{{$file->peso}} <span data-attribute="peso-tipo">{{$file->unidad}}</span></div>
														<div><span data-attribute="fecha">{{Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i')}}</span></div>
													</div>
													<div><a data-attribute="link" href="{{$file->url}}" download title="Descargar">Descargar</a></div>
												</div>
											</div>
										</article>
									@else
										@if(in_array($file->role_id,[5,6]))
											<article data-component="archivo_area_2" data-file-type="area_2_{{$file->tipo}}" class="file-item mb-3 border-bottom pb-3">
												<div class="form-row">
													<div class="col-2">
														<img class="img-fluid icon" src="{{asset('img/filetype/'.$file->tipo.'.svg')}}" alt="Icon">
													</div>
													<div class="col-10">
														<div class="title h6 mb-0 text-truncate"><span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$file->url)}}">{{str_replace('/files/','',$file->url)}}</span></div>
														<div class="extra-data">
															<div><span data-attribute="peso"></span>{{$file->peso}} <span data-attribute="peso-tipo">{{$file->unidad}}</span></div>
															<div><span data-attribute="fecha">{{Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i')}}</span></div>
														</div>
														<div><a data-attribute="link" href="{{$file->url}}" download title="Descargar">Descargar</a></div>
													</div>
												</div>
											</article>
										@else
											@if(in_array($file->role_id,[7,8]))
												<article data-component="archivo_area_3" data-file-type="area_3_{{$file->tipo}}" class="file-item mb-3 border-bottom pb-3">
													<div class="form-row">
														<div class="col-2">
															<img class="img-fluid icon" src="{{asset('img/filetype/'.$file->tipo.'.svg')}}" alt="Icon">
														</div>
														<div class="col-10">
															<div class="title h6 mb-0 text-truncate"><span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$file->url)}}">{{str_replace('/files/','',$file->url)}}</span></div>
															<div class="extra-data">
																<div><span data-attribute="peso"></span>{{$file->peso}} <span data-attribute="peso-tipo">{{$file->unidad}}</span></div>
																<div><span data-attribute="fecha">{{Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i')}}</span></div>
															</div>
															<div><a data-attribute="link" href="{{$file->url}}" download title="Descargar">Descargar</a></div>
														</div>
													</div>
												</article>
											@else
												<article data-component="archivo_area_1" data-file-type="area_1_{{$file->tipo}}" class="file-item mb-3 border-bottom pb-3">
													<div class="form-row">
														<div class="col-2">
															<img class="img-fluid icon" src="{{asset('img/filetype/'.$file->tipo.'.svg')}}" alt="Icon">
														</div>
														<div class="col-10">
															<div class="title h6 mb-0 text-truncate"><span data-attribute="titulo" data-toggle="tooltip" title="{{str_replace('/files/','',$file->url)}}">{{str_replace('/files/','',$file->url)}}</span></div>
															<div class="extra-data">
																<div><span data-attribute="peso"></span>{{$file->peso}} <span data-attribute="peso-tipo">{{$file->unidad}}</span></div>
																<div><span data-attribute="fecha">{{Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i')}}</span></div>
															</div>
															<div><a data-attribute="link" href="{{$file->url}}" download title="Descargar">Descargar</a></div>
														</div>
													</div>
												</article>
											@endif
										@endif
									@endif
								@endif							
							@endforeach
						@endif
					@endif
				</section>
				<input type="hidden" id="area_selected" name="area_selected" value="">
				<input type="hidden" id="option_selected" name="option_selected" value="">
			</div>
		</div>
	</div>
</section>