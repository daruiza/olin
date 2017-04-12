@extends('app')

@section('content')
	<!--  
	<div class="col-md-2 col-md-offset-0">
		<ul class="nav nav-pills nav-stacked " >
		  <li class="active"><a href="#">Home</a></li>
		  <li><a href="#">Menu 1</a></li>
		  <li><a href="#">Menu 2</a></li>
		  <li><a href="#">Menu 3</a></li>
		</ul>
	</div>
	-->
	@if(Session::has('error'))
		<div class="alert alert-danger">
			<strong>¡Acceso seguro!</strong> Acceso denegado.<br><br>
			<ul>								
				<li>{{ Session::get('error') }}</li>								
			</ul>
		</div>                
    @endif
    @if(Session::has('message-acces'))
		<div class="alert alert-info">
			<strong>¡Acceso seguro!</strong><br>
			<ul>								
				<li>{{ Session::get('message-acces') }}</li>
			</ul>
		</div>                
	@endif	
    
    <!-- Pintamos el inicio deacuerdo al ROl -->    
    @if(Session::get('opaplus.usuario.rol_id') == 4 )    	
    	<script>
			//Hay una mejor forma para hacer este llamado			
			window.location.href = '{{url("solicitud/crear/3/Administracion/13")}}';		
		</script>		
		
    @else
    	<div class="col-md-8 col-md-offset-2 cuadro">	
			<ul>					
				<dd class="">
					{!! Form::open(array('id'=>'form_search','url' => 'afiliados/buscar')) !!}
						{!! Form::label('usuario', 'Buscador de Afiliados', array('class' => 'col-md-12 control-label')) !!}
						{!! Form::text('names', '', array('class' => 'form-control','placeholder'=>'Ingresa: Identificación o Nombres de afiliado')) !!}					
					{!! Form::close() !!}
				</dd>					
			</ul>
		</div>    
    @endif
		
@endsection
    