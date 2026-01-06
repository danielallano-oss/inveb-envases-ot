<div class="form-row">
  <div class="col-12">
    <div class="card h-100">
      <div class="card-header"></div>
      <div class="card-body">
        <div class="row">

          <div class="col">
            <div class="form-group">
              <div class="col">
                <!-- Origen -->
                {!! armarSelectArrayCreateEditOTSeparado([1 => "Nacional", 0=>"Internacional"], 'nacional', 'Origen' , $errors, $client ,'form-control',true,false) !!}
              </div>
            </div>
          </div>

          {{-- rut --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'rut', 'Rut', 'text',$errors, $client, 'form-control', $class, '') !!}

          {{-- name --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'nombre', 'Nombre', 'text',$errors, $client, 'form-control', '', '') !!}
          {{-- codigo --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'codigo', 'Código Cliente', 'text',$errors, $client, 'form-control', '', '') !!}
        </div>
        <div class="row">

          {{-- direccion --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'direccion', 'Dirección', 'text',$errors, $client, 'form-control', '', '') !!}

          {{-- poblacion --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'poblacion', 'Población', 'text',$errors, $client, 'form-control', '', '') !!}

          {{-- telefono --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'telefono', 'Teléfono', 'text',$errors, $client, 'form-control', '', '') !!}


          {{-- codigo_zona --}} <!-- ($formato, $key, $title , $type, $errors, $objeto ,$class_input, $required, $placeholder) -->
          {!! armarInputCreateEdit('col', 'codigo_zona', 'Código Zona', 'text',$errors, $client, 'form-control', '', '') !!}
        </div>
        <div class="row">
          @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdministrador())
          <!-- Tipo de Cliente -->
          {!! armarSelectArrayCreateEdit(["A"=>"A","B"=>"B","C"=>"C","D"=>"D","Z"=>"Z"],'col', 'tipo_cliente', 'Tipo de Cliente' , $errors, $client ,'form-control form-element') !!}
          <!-- Clasificacion -->
          {!! armarSelectArrayCreateEdit($clasificaciones,'col', 'clasificacion', 'Clasificación' , $errors, $client ,'form-control form-element') !!}
          <!-- Margen Mínimo Vendedor Externo -->
          {!! armarInputCreateEdit('col', 'margen_minimo_vendedor_externo', 'Margen Mínimo Vendedor Externo', 'text',$errors, $client, 'form-control', '', '') !!}
          <div class="col">
            <div class="form-group ">
              <div class="col">
                <div class="form-group ">
                  <label>&nbsp;</label>
                </div>
              </div>
            </div>
          </div>

          @endif
        </div>
        <div class="form-group form-row">
          <div class="col-12">
            <!--<div class="mt-3 text-left">
              <button class="btn btn-success btn-sm" type="button" data-toggle="collapse" data-target="#seccion_contactos" id="button_contactos">Contactos</button>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <button class="btn btn-success btn-sm" type="button" data-toggle="collapse" data-target="#seccion_plantas" id="button_plantas">Instalaciones</button>
            </div>-->
            <div class="mt-3 text-right">
              @if(Auth()->user()->isIngeniero() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño() || Auth()->user()->isDiseñador() || Auth()->user()->isJefePrecatalogador() || Auth()->user()->isPrecatalogador())
                <a href="{{ route('mantenedores.clients.list') }}" class="btn btn-light">Atras</a>
              @else
                <a href="{{ route('mantenedores.clients.list') }}" class="btn btn-light">Cancelar</a>
              @endif
              @if(!Auth()->user()->isIngeniero() && !Auth()->user()->isJefeCatalogador() && !Auth()->user()->isCatalogador() && !Auth()->user()->isJefeDesarrollo() && !Auth()->user()->isJefeDiseño() && !Auth()->user()->isDiseñador() && !Auth()->user()->isJefePrecatalogador() && !Auth()->user()->isPrecatalogador())
                <button id="submitClient" type="submit" class="btn btn-success">{{ isset($client->id) ? __('Actualizar') : __('Guardar') }}</button>
              @endif
            </div>
          </div>
        <!-- CONTACTOS -->
        <br><br>
        <div class="col-12">
          <div id="seccion_indicaciones">
            <div class="row">
              <h4 style="padding-left:15px; color: #666; margin-bottom: 20px">Indicaciones Especiales &nbsp;&nbsp;&nbsp;</h4>
              @if($tipo=='edit')
                @if($indicaciones->count()>0)
                  @foreach ( $indicaciones as $indicacion )
                    <a herf="#" data-toggle="modal" data-target="#modal-editar-indicacion" data-editar="{{$indicacion->id}}">
                      <div class="material-icons md-50" data-toggle="tooltip" title="Editar">edit_note</div>
                    </a>
                  @endforeach
                @else
                  <a herf="#" data-toggle="modal" data-target="#modal-indicacion">
                    <div class="material-icons md-50" data-toggle="tooltip" title="Agregar">edit_note</div>
                  </a>
                @endif
              @else
                <a herf="#" data-toggle="modal" data-target="#modal-indicacion">
                  <div class="material-icons md-50" data-toggle="tooltip" title="Agregar">edit_note</div>
                </a>
              @endif

            </div>
            <div class="row" style="margin:2px;">
              <div class="col-12"  style="background:#3aaa35;" >
                <div class="card" style="border-color: green;border-block-color: darkgrey; text-align:left; color: #666;">
                  <table id="client_indicaciones" name="client_indicaciones" style="margin:5px;">
                    <tbody>
                      @if($tipo=='edit')
                        @if($indicaciones->count()>0)
                          @foreach ( $indicaciones as $indicacion )
                            @if(!is_null($indicacion->garantia_ect))
                              <tr>
                                <td><b>Garantía Ect:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->garantia_ect}}</td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_1))
                              <tr>
                                <td><b>Indicación 1:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_1}}. &nbsp;<b>{{$indicacion->user_name_campo_1}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_2))
                              <tr>
                                <td><b>Indicación 2:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_2}}. &nbsp;<b>{{$indicacion->user_name_campo_2}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_3))
                              <tr>
                                <td><b>Indicación 3:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_3}}. &nbsp;<b>{{$indicacion->user_name_campo_3}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_4))
                              <tr>
                                <td><b>Indicación 4:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_4}}. &nbsp;<b>{{$indicacion->user_name_campo_4}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_5))
                              <tr>
                                <td><b>Indicación 5:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_5}}. &nbsp;<b>{{$indicacion->user_name_campo_5}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_6))
                              <tr>
                                <td><b>Indicación 6:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_6}}. &nbsp;<b>{{$indicacion->user_name_campo_6}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_7))
                              <tr>
                                <td><b>Indicación 7:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_7}}. &nbsp;<b>{{$indicacion->user_name_campo_7}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_8))
                              <tr>
                                <td><b>Indicación 8:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_8}}. &nbsp;<b>{{$indicacion->user_name_campo_8}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_9))
                              <tr>
                                <td><b>Indicación 9:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{$indicacion->campo_9}}. &nbsp;<b>{{$indicacion->user_name_campo_9}}</b></td>
                              </tr>
                            @endif
                            @if(!is_null($indicacion->campo_10))
                              <tr>
                                <td><b>Indicación 10: </b>&nbsp;{{$indicacion->campo_10}}. &nbsp;<b>{{$indicacion->user_name_campo_10}}</b></td>
                              </tr>
                            @endif
                          @endforeach
                        @else
                          <tr>
                            <td colspan="12">No tiene indicaciones especiales registradas</td>
                          </tr>
                        @endif
                      @else
                        <tr>
                          <td colspan="12">No tiene indicaciones especiales registradas</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">&nbsp;</div>
        <div class="col-12">&nbsp;</div>

        <div class="col-12">
          <div id="seccion_plantas">
            <div class="row">
              <h4 style="padding-left:15px; color: #666; margin-bottom: 20px">Instalaciones &nbsp;&nbsp;&nbsp;
                @if(!Auth()->user()->isIngeniero() && !Auth()->user()->isJefeCatalogador() && !Auth()->user()->isCatalogador() && !Auth()->user()->isJefeDesarrollo() && !Auth()->user()->isJefeDiseño() && !Auth()->user()->isDiseñador() && !Auth()->user()->isJefePrecatalogador() && !Auth()->user()->isPrecatalogador())
                  <button class="btn btn-success btn-sm" type="button"  id="button_crear_planta" herf="#" data-toggle="modal" data-target="#modal-crear-planta">Registrar</button></h4>
                @endif
            </div>

            <div class="row">
              <div class="col-12">
                <table id="client_installations" name="client_installations">

                  <tbody>
                    @if($tipo=='edit')
                      @if($installations->count()>0)
                        @foreach ( $installations as $installation )
                          <tr style="background: #e4e6e4;">
                            <td>
                              <h3>&nbsp;&nbsp;{{$installation->nombre}}&nbsp;&nbsp;
                                @if(!Auth()->user()->isIngeniero() && !Auth()->user()->isJefeCatalogador() && !Auth()->user()->isCatalogador() && !Auth()->user()->isJefeDesarrollo() && !Auth()->user()->isJefeDiseño() && !Auth()->user()->isDiseñador() && !Auth()->user()->isJefePrecatalogador() && !Auth()->user()->isPrecatalogador())
                                  <a herf="#" data-toggle="modal" data-target="#modal-editar-planta" data-editar="{{$installation->id}}">
                                    <div class="material-icons md-14" data-toggle="tooltip" title="Editar">edit</div>
                                  </a>
                                @endif
                              </h3>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <table class="table table-sm table-bordered" width="100%">
                                <thead>
                                  <tr>
                                    <th colspan="10">
                                      <h5><b>Datos Paletizado</b></h5>
                                    </th>
                                  </tr>
                                  <tr>
                                    <th style="width: 350px;"><b>Tipo Pallet</b></th>
                                    <th style="width: 120px;"><b>Altura Pallet</b></th>
                                    <th style="width: 120px;"><b>Sobresalir Carga</b></th>
                                    <th style="width: 120px;"><b>Bulto Zunchado</b></th>
                                    <th style="width: 120px;"><b>Formato Etiqueta</b></th>
                                    <th style="width: 120px;"><b>Etiquetas Pallet</b></th>
                                    <th style="width: 150px;"><b>Termocontraible</b></th>
                                    <th style="width: 200px;"><b>Fsc</b></th>
                                    <th style="width: 120px;"><b>Pais Mercado/Destino</b></th>
                                    <th style="width: 120px;"><b>Certificado Calidad</b></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>
                                      @if(is_null($installation->tipo_pallet)) N/A @else {{$installation->TipoPalleT->descripcion}} @endif
                                    </td>
                                    <td>{{$installation->altura_pallet}}</td>
                                    <td>
                                      @if($installation->sobresalir_carga==1) SI @else NO @endif
                                    </td>
                                    <td>
                                      @if($installation->bulto_zunchado==1) SI @else NO @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->formato_etiqueta)) N/A @else {{$installation->formato_etiqueta_pallet->descripcion}} @endif
                                    </td>
                                    <td>
                                      {{$installation->etiquetas_pallet}}
                                    </td>
                                    <td>
                                      @if($installation->termocontraible==1) SI @else NO @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->fsc)) N/A @else {{$installation->Fsc->descripcion}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->pais_mercado_destino)) N/A @else {{$installation->TargetMarket->name}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->certificado_calidad)) N/A @else {{$installation->qa->descripcion}} @endif
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <table class="table table-sm table-bordered" width="100%">
                                <thead>
                                  <tr>
                                    <th colspan="8">
                                      <h5><b>Listado de Contactos</b></h5>
                                    </th>
                                  </tr>
                                  <tr>
                                    <th style="width: 150px;"><b>Descripción</b></th>
                                    <th style="width: 150px;"><b>Nombre</b></th>
                                    <th style="width: 150px;"><b>Cargo</b></th>
                                    <th style="width: 150px;"><b>Correo</b></th>
                                    <th style="width: 150px;"><b>Teléfono</b></th>
                                    <th style="width: 150px;"><b>Comuna</b></th>
                                    <th style="width: 300px;"><b>Dirección</b></th>
                                    <th style="width: 100px;"><b>Estado</b></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>
                                      <b>Contacto 1</b>
                                    </td>
                                    <td>
                                      @if(is_null($installation->nombre_contacto)) N/A @else {{$installation->nombre_contacto}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->cargo_contacto)) N/A @else {{$installation->cargo_contacto}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->email_contacto)) N/A @else {{$installation->email_contacto}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->phone_contacto)) N/A @else {{$installation->phone_contacto}} @endif
                                    </td>
                                    <td>
                                      {{ optional($installation->Comuna)->ciudad ?? 'N/A' }}
                                    </td>
                                    <td>
                                      @if(is_null($installation->direccion_contacto)) N/A @else {{$installation->direccion_contacto}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->active_contacto)) >N/A @else {{$installation->active_contacto}} @endif
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <b>Contacto 2</b>
                                    </td>
                                    <td>
                                      @if(is_null($installation->nombre_contacto_2)) N/A @else {{$installation->nombre_contacto_2}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->cargo_contacto_2)) N/A @else {{$installation->cargo_contacto_2}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->email_contacto_2)) N/A @else {{$installation->email_contacto_2}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->phone_contacto_2)) N/A @else {{$installation->phone_contacto_2}} @endif
                                    </td>
                                    <td>
                                      {{ optional($installation->Comuna_2)->ciudad ?? 'N/A' }}
                                    </td>
                                    <td>
                                      @if(is_null($installation->direccion_contacto_2)) N/A @else {{$installation->direccion_contacto_2}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->active_contacto_2)) >N/A @else {{$installation->active_contacto_2}} @endif
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <b>Contacto 3</b>
                                    </td>
                                    <td>
                                      @if(is_null($installation->nombre_contacto_3)) N/A @else {{$installation->nombre_contacto_3}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->cargo_contacto_3)) N/A @else {{$installation->cargo_contacto_3}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->email_contacto_3)) N/A @else {{$installation->email_contacto_3}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->phone_contacto_3)) N/A @else {{$installation->phone_contacto_3}} @endif
                                    </td>
                                    <td>
                                      {{ optional($installation->Comuna_3)->ciudad ?? 'N/A' }}
                                    </td>
                                    <td>
                                      @if(is_null($installation->direccion_contacto_3)) N/A @else {{$installation->direccion_contacto_3}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->active_contacto_3)) >N/A @else {{$installation->active_contacto_3}} @endif
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <b>Contacto 4</b>
                                    </td>
                                    <td>
                                      @if(is_null($installation->nombre_contacto_4)) N/A @else {{$installation->nombre_contacto_4}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->cargo_contacto_4)) N/A @else {{$installation->cargo_contacto_4}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->email_contacto_4)) N/A @else {{$installation->email_contacto_4}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->phone_contacto_4)) N/A @else {{$installation->phone_contacto_4}} @endif
                                    </td>
                                    <td>
                                      {{ optional($installation->Comuna_4)->ciudad ?? 'N/A' }}
                                    </td>
                                    <td>
                                      @if(is_null($installation->direccion_contacto_4)) N/A @else {{$installation->direccion_contacto_4}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->active_contacto_4)) >N/A @else {{$installation->active_contacto_4}} @endif
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <b>Contacto 5</b>
                                    </td>
                                    <td>
                                      @if(is_null($installation->nombre_contacto_5)) N/A @else {{$installation->nombre_contacto_5}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->cargo_contacto_5)) N/A @else {{$installation->cargo_contacto_5}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->email_contacto_5)) N/A @else {{$installation->email_contacto_5}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->phone_contacto_5)) N/A @else {{$installation->phone_contacto_5}} @endif
                                    </td>
                                    <td>
                                      {{ optional($installation->Comuna_5)->ciudad ?? 'N/A' }}
                                    </td>
                                    <td>
                                      @if(is_null($installation->direccion_contacto_5)) N/A @else {{$installation->direccion_contacto_5}} @endif
                                    </td>
                                    <td>
                                      @if(is_null($installation->active_contacto_5)) >N/A @else {{$installation->active_contacto_5}} @endif
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>

                        @endforeach

                      @else
                        <tr>
                          <td colspan="12" align="center">No tiene instalaciones registradas</td>
                        </tr>
                      @endif
                    @else
                      <tr>
                        <td colspan="12" align="center">No tiene instalaciones registradas</td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

