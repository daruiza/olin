@extends('app')

@section('content')
	<!-- Mensajes y aletas -->
	<!-- Este se usa para validar formularios -->
	@if (count($errors) > 0)
		<div class="alert alert-danger fade in">
			<strong>Algo no va bien con el modulo Opciones!</strong> Hay problemas con con los datos diligenciados.<br><br>
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
						{!! Form::open(array('id'=>'form_search','url' => 'opcion/buscar')) !!}
							{!! Form::label('usuario', 'Buscador de '.Session::get('modulo.modulo'), array('class' => 'col-md-12 control-label')) !!}
							{!! Form::text('names', '', array('class' => 'form-control','placeholder'=>'Ingresa: opción o descripcion')) !!}
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
					<li> Cantidad de Opciones:  {{Session::get('modulo.total_opciones')}} </li>		
					<li> Cantidad de Lugares:  {{count(Session::get('modulo.datos.datos'))}} </li>
					<dd style="border-bottom: 1px dotted #78a5b1">
						<ul>							
						@foreach (Session::get('modulo.datos.categoria') as $valor)
							<li  type="square" >{{$valor}}</li>							
							<script type="text/javascript">
								javascript:seg_opcion.datos_categoria.push("{{$valor}}");								
							</script>													
						@endforeach
						</ul>
					</dd>					
											
					@foreach (Session::get('modulo.datos.datos') as $llave => $valor)						
						@foreach ($valor as $val)
							<script type="text/javascript">  seg_opcion.aux_datos.push({{$val}});</script>							
						@endforeach	
						<script type="text/javascript">
							javascript:seg_opcion.datos_pila.push({name:"{{$llave}}",data:seg_opcion.aux_datos});
							javascript:seg_opcion.aux_datos = [];
						</script>											
					@endforeach			
					 											
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
				<div id="container_pila" style="width:100%; height:100%;"></div>	
			</div>		
			
		</div>
		
	</div>
		
@endsection

@section('script')
	<script type="text/javascript">  	
  		javascript:seg_user.iniciarPila('#container_pila','Distribución Opcoines en lugar y vista','Lugares y vistas',seg_opcion.datos_pila,seg_opcion.datos_categoria);		
		javascript:seg_opcion.datos_pila = [];
		javascript:seg_opcion.datos_categoria = [];
					
	</script>
@endsection
    