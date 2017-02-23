@extends('app')

@section('content')
	<!-- Mensajes y aletas -->
	<!-- Este se usa para validar formularios -->
	@if (count($errors) > 0)
		<div class="alert alert-danger fade in">
			<strong>Algo no va bien con el modulo Solicitudes!</strong> Hay problemas con con los datos diligenciados.<br><br>
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
		</div>
	@endif	
	<!-- Este se usa para mostrar mensajes -->		
	@if(Session::has('message'))
		<div class="alert alert-info fade in">
			<strong>¡Actualización de Informacion!</strong> El registro se ha actualizado adecuadamente.<br><br>
			<ul>								
				<li>{{ Session::get('message') }}</li>
			</ul>
		</div>                
	@endif
    <!-- Mesnsajes de error -->
   @if(Session::has('error'))
		<div class="alert alert-danger fade in">
			<strong>¡Algo no va bien!</strong> Hay problemas con los datos diligenciados.<br><br>
				<ul>								
					<li>{{ Session::get('error') }}</li>								
				</ul>
		</div>                
	@endif
	
	<div class="row">
		<div class="col-md-4">						
			@if(Session::has('modulo'))
			<div class="col-md-11 col-md-offset-1 cuadro">	
				<ul>					
					<dd class="">
						{!! Form::open(array('id'=>'form_search','url' => 'solicitud/buscar')) !!}
							{!! Form::label('solicitud', 'Buscador de '.Session::get('modulo.modulo'), array('class' => 'col-md-12 control-label')) !!}
							{!! Form::text('names', '', array('class' => 'form-control','placeholder'=>'Ingresa: Id Solicitud, Estado, ID Titular u Homenaje')) !!}
							{!! Form::hidden('id', Session::get('modulo.id')) !!}
						{!! Form::close() !!}
					</dd>					
				</ul>
			</div>
			<div class="col-md-11 col-md-offset-1 cuadro">
				<ul>
					<dd class="title">Modulo: {{Session::get('modulo.modulo')}}</dd>
					</br>
					<li style="border-bottom: 1px dotted #78a5b1">{{Session::get('modulo.description')}}</li>
					<li> Opciones disponibles: {{ count(Session::get('opaplus.usuario.permisos')[Session::get('modulo.id_aplicacion')]['modulos'][Session::get('modulo.categoria')][Session::get('modulo.id')]['opciones'])}}</li>
					<dd style="border-bottom: 1px dotted #78a5b1">
						<ul>							
						@foreach (Session::get('opaplus.usuario.permisos')[Session::get('modulo.id_aplicacion')]['modulos'][Session::get('modulo.categoria')][Session::get('modulo.id')]['opciones'] as $llave_opt => $opt)
							@if($opt['lugar'] == Session::get('opaplus.usuario.lugar.lugar'))
								@if($opt['vista'] != 'listar')
									<li  type="square" ><a href="{{url(json_decode(Session::get('opaplus.usuario.permisos')[Session::get('modulo.id_aplicacion')]['modulos'][Session::get('modulo.categoria')][Session::get('modulo.id')]['preferencias'])->controlador)}}/{{($opt['accion'])}}/{{Session::get('modulo.id_aplicacion')}}/{{Session::get('modulo.categoria')}}/{{Session::get('modulo.id')}}" >{{$opt[$llave_opt]}}</a></li>   
								@endif
							@endif							
						@endforeach
						</ul>
					</dd>	
					<li> Cantidad de Solicitudes:  {{Session::get('modulo.total_solicitudes')}} </li>
					<dd style="border-bottom: 1px dotted #78a5b1">
						<ul>							
						@foreach (Session::get('modulo.solicitudes') as $llave_sol => $sol)
							<li type="square" >{{$sol->state}} : {{$sol->total}}</li>
							<script type="text/javascript">  hom_solicitud.datos_pie.push({name:"{{$sol->state}}",y:{{$sol->total}}});</script>
							<script type="text/javascript">  hom_solicitud.colores_pie.push('{{$sol->alert}}');</script>
						@endforeach
						</ul>
					</dd>	
					<li> Cantidad de Servicios:  {{Session::get('modulo.total_servicios')}} </li>
					<dd style="border-bottom: 1px dotted #78a5b1">
						<ul>							
						@foreach (Session::get('modulo.servicios_entidad') as $llave_ser => $ser)
							<li type="square" >{{$ser->seat}} : {{$ser->total}}</li>
							<script type="text/javascript">  hom_solicitud.datos_pie_servicios.push({name:"{{$ser->seat}}",y:{{$ser->total}}});</script>
							
						@endforeach
						</ul>
					</dd>
					<dd style="border-bottom: 1px dotted #78a5b1">
						<ul>						
						@foreach (Session::get('modulo.datos.categoria') as $categoria)
							<li type="square" >{{$categoria}}</li>
							<script type="text/javascript">  hom_solicitud.datos_categoria.push("{{$categoria}}");</script>						
						@endforeach							
						@foreach (Session::get('modulo.datos.datos') as $llave_datos => $datos)							
							@foreach ($datos as $val)
								<script type="text/javascript">  hom_solicitud.aux_datos.push({{$val}});</script>							
							@endforeach														
							<script type="text/javascript"> 
								hom_solicitud.datos_multibar.push({name:"{{$llave_datos}}",data:hom_solicitud.aux_datos});
								javascript:hom_solicitud.aux_datos = [];
							</script>
						@endforeach
						</ul>
					</dd>					
					 											
				</ul>								
			</div>	 
			@else
			<div class="alert alert-danger">
				<strong>¡Algo no va bien!</strong> Hay problemas con los datos diligenciados.<br><br>
					<ul>								
						<li> Los Datos Para la Construcción Del Index no se Consultarón Adecuadamente, o se esta realizando la consulta por medio del URL </li>								
					</ul>
			</div> 			
			@endif			
		</div>
		<div class="col-md-8">
			<div class="col-md-11 col-md-offset-0 cuadro">
				<div id="container_pie" style="width:100%; height:100%;"></div>	
			</div>		
			
		</div>
		<div class="col-md-12">
			<div class="col-md-10 col-md-offset-1 cuadro">
				<div id="container_pie_servicios" style="width:100%; height:100%;"></div>	
			</div>	
			<div class="col-md-10 col-md-offset-1 cuadro">
				<div id="container_bar" style="width:100%; height:100%;"></div>	
			</div>
		</div>
		
	</div>
		
@endsection

@section('script')
	<script type="text/javascript">  	
		javascript:seg_user.iniciarPie('#container_pie','Distribución de Solicitudes por Estado',hom_solicitud.datos_pie,hom_solicitud.colores_pie);
		javascript:seg_user.iniciarPie('#container_pie_servicios','Distribución de Servicios por Entidad',hom_solicitud.datos_pie_servicios);
		javascript:seg_user.iniciarBar('#container_bar','Distribución de Servicios por Entidad y Días','Días y Entidades',hom_solicitud.datos_multibar,hom_solicitud.datos_categoria);		
		javascript:hom_solicitud.datos_pie = [];
		javascript:hom_solicitud.datos_pie_servicios = [];
		javascript:hom_solicitud.datos_multibar = [];
		javascript:hom_solicitud.datos_categoria = [];
		javascript:hom_solicitud.aux_datos = [];	
	</script>
@endsection
    