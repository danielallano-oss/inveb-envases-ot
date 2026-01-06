<header class="bg-primary">
	<nav class="container-fluid navbar navbar-expand navbar-dark">
		<a class="navbar-brand" href="{{ route('Ots') }}">
			<img src="{{ asset('img/logo-cmpc-white.svg')}}" alt="CMPC" class="align-baseline" width="80" height="33">
		</a>
		<ul class="header-menu">
			@if(Auth()->user()->isVendedorExterno())
			<li class="{{ (request()->is('ordenes-trabajo') || request()->is('') || request()->is('/')|| request()->is('home')) ? 'active' : '' }}"><a href="{{ route('Ots') }}">Órdenes de trabajo</a></li>
			{{-- <li class="{{ (request()->is('ordenes-trabajo2') || request()->is('') || request()->is('/')|| request()->is('home')) ? 'active' : '' }}"><a href="{{ route('Ots2') }}">Órdenes de trabajo 2</a></li> --}}
            <li class="dropmenu {{ request()->is('cotizador/*') ? 'active' : '' }} cotizador left">Cotizador
				<ul>
					<li><a href="{{ route('cotizador.index_cotizacion_externo') }}">Cotizaciones</a></li>

				</ul>
			</li>

			@else
				@if(!Auth()->user()->isAdmin())
					<li class="{{ (request()->is('ordenes-trabajo') || request()->is('') || request()->is('/')|| request()->is('home')) ? 'active' : '' }}"><a href="{{ route('Ots') }}">Órdenes de trabajo</a></li>
                    {{-- <li class="{{ (request()->is('ordenes-trabajo2') || request()->is('') || request()->is('/')|| request()->is('home')) ? 'active' : '' }}"><a href="{{ route('Ots2') }}">Órdenes de trabajo 2</a></li> --}}
				@endif

				@if(isset(auth()->user()->role->area) && !Auth()->user()->isAdmin() && !Auth()->user()->isVendedor())
					<li class="{{ request()->is('asignaciones') ? 'active' : '' }}"><a href="{{ route('asignaciones') }}">Asignaciones</a></li>
				@endif

				@if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo())

					<li class="{{ request()->is('listadoAprobacion') ? 'active' : '' }}"><a href="{{ route('listadoAprobacion') }}">Aprobaciones</a></li>
				@endif

				@if(Auth()->user()->isSuperAdministrador() || Auth()->user()->isAdmin() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
					<li class="dropmenu {{ request()->is('reportes') ? 'active' : '' }}">
						Reportería
						<ul>
							@if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportGestionLoadOtMonth') }}">Gestión de Carga OT por Mes</a></li>
								<li><a href="{{ route('reportGestionsActive') }}">Gestión de OT Activas</a></li>
								<li><a href="{{ route('reportTimeByAreaOtMonth') }}">Tiempos OT por Área</a></li>
								<li><a href="{{ route('reportRechazos') }}">Arbol de Rechazos</a></li>
								<li><a href="{{ route('reportCompletedOt') }}">Ratio Conversión OT</a></li>
								<li><a href="{{ route('reportActiveOtsPerArea') }}">OTs Activas Por Usuario</a></li>
							@endif
							@if(Auth()->user()->isAdmin() || Auth()->user()->isJefeVenta() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportAnulaciones') }}">Anulaciones</a></li>
							@endif
							@if(Auth()->user()->isAdmin() || Auth()->user()->isJefeDesarrollo() || auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportMuestras') }}">Muestras Pendientes</a></li>
							@endif
							@if(strtolower(Auth()->user()->nombre) == 'super' || strtolower(Auth()->user()->nombre) == 'marcela' || Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeMuestras() || Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportDisenoEstructuralySalaMuestra') }}">Indicador Desarrollo </a></li>
								<li><a href="{{ route('reportSalaMuestra') }}">Sala de Muestras</a></li>
								<li><a href="{{ route('reportTiempoPrimeraMuestra') }}">Tiempo Primera Muestra</a></li>
								{{-- <li><a href="{{ route('reportTiempoDisenadorExterno') }}">Diseñador Externo</a></li> --}}
								<li><a href="{{ route('reportTiempoDisenadorExternoAjuste') }}">Diseñador Externo Ajuste</a></li>
							@endif
						</ul>
					</li>
				@endif

                @if(Auth()->user()->isSuperAdministrador() || Auth()->user()->isAdmin() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
					<li class="dropmenu {{ request()->is('reportes2') ? 'active' : '' }}">
						Reportería 2
						<ul>
							@if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportGestionLoadOtMonthNew') }}">Gestión de Carga OT por Mes</a></li>
								<li><a href="{{ route('reportGestionsActiveNew') }}">Gestión de OT Activas</a></li>
								<li><a href="{{ route('reportTimeByAreaOtMonthNew') }}">Tiempos OT por Área</a></li>
								<li><a href="{{ route('reportRechazosNew') }}">Arbol de Rechazos</a></li>
								<li><a href="{{ route('reportCompletedOtNew') }}">Ratio Conversión OT</a></li>
								<li><a href="{{ route('reportActiveOtsPerAreaNew') }}">OTs Activas Por Usuario</a></li>
							@endif
							@if(Auth()->user()->isAdmin() || Auth()->user()->isJefeVenta() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportAnulacionesNew') }}">Anulaciones</a></li>
							@endif
							@if(Auth()->user()->isAdmin() || Auth()->user()->isJefeDesarrollo() || auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportMuestrasNew') }}">Muestras Pendientes</a></li>
							@endif
							@if(strtolower(Auth()->user()->nombre) == 'super' || strtolower(Auth()->user()->nombre) == 'marcela' || Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeMuestras() || Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportDisenoEstructuralySalaMuestraNew') }}">Indicador Desarrollo </a></li>
								<li><a href="{{ route('reportSalaMuestraNew') }}">Sala de Muestras</a></li>
								<li><a href="{{ route('reportTiempoPrimeraMuestraNew') }}">Tiempo Primera Muestra</a></li>
								{{-- <li><a href="{{ route('reportTiempoDisenadorExternoNew') }}">Diseñador Externo</a></li> --}}
							@endif
						</ul>
					</li>
				@endif

                 {{-- @if(Auth()->user()->isSuperAdministrador() || Auth()->user()->isAdmin() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
					<li class="dropmenu {{ request()->is('reportes3') ? 'active' : '' }}">
						Reportería 3
						<ul>
							@if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportGestionLoadOtMonthNew1') }}">Gestión de Carga OT por Mes</a></li>
								<li><a href="{{ route('reportGestionsActiveNew1') }}">Gestión de OT Activas</a></li>
								<li><a href="{{ route('reportTimeByAreaOtMonthNew1') }}">Tiempos OT por Área</a></li>
								<li><a href="{{ route('reportRechazosNew1') }}">Arbol de Rechazos</a></li>
								<li><a href="{{ route('reportCompletedOtNew1') }}">Ratio Conversión OT</a></li>
								<li><a href="{{ route('reportActiveOtsPerAreaNew1') }}">OTs Activas Por Usuario</a></li>
							@endif
							@if(Auth()->user()->isAdmin() || Auth()->user()->isJefeVenta() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportAnulacionesNew1') }}">Anulaciones</a></li>
							@endif
							@if(Auth()->user()->isAdmin() || Auth()->user()->isJefeDesarrollo() || auth()->user()->isJefeMuestras() || auth()->user()->isTecnicoMuestras() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportMuestrasNew1') }}">Muestras Pendientes</a></li>
							@endif
							@if(strtolower(Auth()->user()->nombre) == 'super' || strtolower(Auth()->user()->nombre) == 'marcela' || Auth()->user()->isJefeVenta() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeMuestras() || Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isGerenteGeneral() || Auth()->user()->isGerenteComercial() || Auth()->user()->isJefeDiseño())
								<li><a href="{{ route('reportDisenoEstructuralySalaMuestraNew1') }}">Indicador Desarrollo </a></li>
								<li><a href="{{ route('reportSalaMuestraNew1') }}">Sala de Muestras</a></li>
								<li><a href="{{ route('reportTiempoPrimeraMuestraNew1') }}">Tiempo Primera Muestra</a></li>
							@endif
						</ul>
					</li>
				@endif --}}

				@if(isset(auth()->user()->role->area))
					<li class="{{ request()->is('notificaciones') ? 'active' : '' }}"><a href="{{ route('notificacionesOT') }}">Notificaciones</a></li>
				@endif

				@if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isVendedor() || Auth()->user()->isJefeVenta() || Auth()->user()->isJefeCatalogador() || Auth()->user()->isCatalogador() || Auth()->user()->isIngeniero() || Auth()->user()->isJefeDesarrollo() || Auth()->user()->isJefeDiseño() || Auth()->user()->isDiseñador() || Auth()->user()->isJefePrecatalogador() || Auth()->user()->isPrecatalogador())
					<li class="dropmenu {{ request()->is('mantenedores/*') ? 'active' : '' }}">Mantenedores
						<ul>
							<li><a href="{{ route('mantenedores.clients.list') }}">Clientes</a></li>
							@if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador())
							<li><a href="{{ route('mantenedores.users.list') }}">Usuarios</a></li>
							<!-- <li><a href="{{ route('mantenedores.sectors.list') }}">Sectores</a></li> -->
							{{-- <li><a href="{{ route('mantenedores.hierarchies.list') }}">Jerarquías 1</a></li> --}}
							<li><a href="/mantenedores/hierarchies/list?active[]=1">Jerarquías 1</a></li>
							<li><a href="/mantenedores/subhierarchies/list?active[]=1">Jerarquías 2</a></li>
							{{-- <li><a href="{{ route('mantenedores.subhierarchies.list') }}">Jerarquías 2</a></li> --}}
							<li><a href="/mantenedores/subsubhierarchies/list?active[]=1">Jerarquías 3</a></li>
							{{-- <li><a href="{{ route('mantenedores.subsubhierarchies.list') }}">Jerarquías 3</a></li> --}}
							<li><a href="{{ route('mantenedores.product-types.list') }}">Tipo de Productos</a></li>
							<li><a href="{{ route('mantenedores.styles.list') }}">Estilos</a></li>
							<!-- <li><a href="{{ route('mantenedores.cartons.list') }}">Cartones</a></li> -->
							<li><a href="{{ route('mantenedores.colors.list') }}">Colores</a></li>
							<li><a href="{{ route('mantenedores.secuencias-operacionales.list') }}">Secuencias Operacionales</a></li>
							<li><a href="{{ route('mantenedores.almacenes.list') }}">Almacenes</a></li>
							{{-- <li><a href="{{ route('mantenedores.cantidad-base.list') }}">Cantidad Base</a></li> --}}
							<li><a href="{{ route('mantenedores.tipos-cintas.list') }}">Tipos Cintas</a></li>
							<li><a href="{{ route('mantenedores.rechazo-conjunto.list') }}">Rechazo Conjunto</a></li>
							<li><a href="{{ route('mantenedores.grupo-imputacion-material.list') }}">Grupo Imputación Material</a></li>
							<li><a href="{{ route('mantenedores.organizacion-venta.list') }}">Organizaciones Ventas</a></li>
							<li><a href="{{ route('mantenedores.tiempo-tratamiento.list') }}">Tiempo Tratamiento</a></li>
							<li><a href="{{ route('mantenedores.grupo-materiales-1.list') }}">Grupo Materiales 1</a></li>
							{{-- <li><a href="{{ route('mantenedores.grupo-materiales-2.list') }}">Grupo Materiales 2</a></li> --}}
							<li><a href="{{ route('mantenedores.matrices.masive') }}">Matrices</a></li>
							<li><a href="{{ route('mantenedores.grupo-plantas.list') }}">Grupo Plantas</a></li>
							<li><a href="{{ route('mantenedores.canals.list') }}">Canales</a></li>
							<li><a href="{{ route('mantenedores.adhesivos.list') }}">Adhesivos</a></li>
							<li><a href="{{ route('mantenedores.sectors.list') }}">Sectores</a></li>
							<li><a href="{{ route('mantenedores.cebes.list') }}">CeBe</a></li>
                            <li><a href="{{ route('mantenedores.pallet-types.list') }}">Tipo de Palet</a></li>
                            {{-- <li><a href="/mantenedores/materials/list?active[]=1">Materiales</a></li> --}}
                            @if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador())
                            <li><a href="{{ route('mantenedores.materiales.masive') }}">Carga de Materiales</a></li>
                            @endif
							<?php /*  <li><a href="{{ route('mantenedores.materials.list') }}">Materiales</a></li> */ ?>
							@endif
						</ul>
					</li>
				@endif

				@if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador() || Auth()->user()->isVendedor() || Auth()->user()->isGerenteGeneral()||Auth()->user()->isGerenteComercial()||Auth()->user()->isJefeVenta())

					<li class="dropmenu {{ request()->is('cotizador/*') ? 'active' : '' }} cotizador right">Cotizador
						<ul>
							<li><a href="{{ route('cotizador.index_cotizacion') }}">Cotizaciones</a></li>
							@if(Auth()->user()->isGerenteGeneral()||Auth()->user()->isGerenteComercial()||Auth()->user()->isJefeVenta())
							<li><a href="{{ route('cotizador.aprobaciones') }}">Aprobar Cotizaciones</a></li>
							@endif
							<li><a href="{{ route('cotizador.ayuda') }}">Sección de Ayuda</a></li>
						</ul>
					</li>
					@if(Auth()->user()->isAdmin() || Auth()->user()->isSuperAdministrador())
						<li class="dropmenu {{ request()->is('mantenedores/cotizador/*') ? 'active' : '' }}">Manten. Cotiza
							<ul style="min-width: 250px;">
								<li><a href="{{ route('mantenedores.cotizador.cartons.masive') }}">Cartones Corrugados</a></li>
								<li><a href="{{ route('mantenedores.cotizador.cartones-esquineros.masive') }}">Cartones Esquineros</a></li>
								<li><a href="{{ route('mantenedores.cotizador.papeles.masive') }}">Papeles</a></li>
								<li><a href="{{ route('mantenedores.cotizador.fletes.masive') }}">Fletes</a></li>
								<li><a href="{{ route('mantenedores.cotizador.mermas-corrugadoras.masive') }}">Mermas Corrugadoras</a></li>
								<li><a href="{{ route('mantenedores.cotizador.mermas-convertidoras.masive') }}">Mermas Convertidoras</a></li>
								<li><a href="{{ route('mantenedores.cotizador.paletizados.masive') }}">Paletizado</a></li>
								<li><a href="{{ route('mantenedores.cotizador.insumos-paletizados.masive') }}">Insumos Paletizado</a></li>
								<!-- <li><a href="{{ route('mantenedores.cotizador.tarifarios.masive') }}">Tarifario Margenes</a></li> -->
								<li><a href="{{ route('mantenedores.cotizador.consumo-adhesivos.masive') }}">Consumo Adhesivos</a></li>
								<li><a href="{{ route('mantenedores.cotizador.consumo-adhesivos-pegados.masive') }}">Consumo Adhesivos Pegados</a></li>
								<li><a href="{{ route('mantenedores.cotizador.consumo-energia.masive') }}">Consumo Energia</a></li>
								<li><a href="{{ route('mantenedores.cotizador.factores-desarrollo.masive') }}">Factores Desarrollo</a></li>
								<li><a href="{{ route('mantenedores.cotizador.factores-onda.masive') }}">Factores Onda</a></li>
								<li><a href="{{ route('mantenedores.cotizador.factores-seguridad.masive') }}">Factores Seguridad</a></li>
								<li><a href="{{ route('mantenedores.cotizador.maquilas.masive') }}">Maquilas</a></li>
								<li><a href="{{ route('mantenedores.cotizador.ondas.masive') }}">Tipos de Onda</a></li>
								<li><a href="{{ route('mantenedores.cotizador.plantas.masive') }}">Planta</a></li>
								<li><a href="{{ route('mantenedores.cotizador.variables.masive') }}">Variables Cotizador</a></li>
								<!--<li><a href="{{ route('mantenedores.cotizador.margenes-minimos.masive') }}">Margenes Minimos</a></li>-->
								<li><a href="{{ route('mantenedores.clasificaciones_clientes.list') }}">Clasificación Clientes</a></li>
								<li><a href="{{ route('mantenedores.cotizador.porcentajes-margenes-minimos.masive') }}">Porcentajes Margenes Minimos</a></li>
								<li><a href="{{ route('mantenedores.cotizador.mano-obra-mantencion.masive') }}">Mano Obra Mantención</a></li>
							</ul>
						</li>
					@endif
				@endif

			@endif


			<!-- Conexion para SAC mediante token_sac -->
			@php
				if(Auth()->user()->isAdmin() || Auth()->user()->isVendedor()|| Auth()->user()->isJefeVenta()){

					$token = Auth()->user()->token_sac;

					//echo ' <li><a href="https://test.envases-sac.inveb.cl/token/'.$token.'" target="_blank">S.A.C</a></li> ';
				}
			@endphp


			<li class="dropmenu right">{{ Auth::user()->nombre. ' ' . Auth::user()->apellido }}
				<div class="perfil">{{ Auth::user()->role->nombre }}</div>
				<ul>
					<li><a href="{{route('editarContraseña',Auth::user()->id)}}">Cambiar Contraseña</a></li>
					<li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Salir</a>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							@csrf
						</form>
					</li>
				</ul>
			</li>
		</ul>
	</nav>
</header>
