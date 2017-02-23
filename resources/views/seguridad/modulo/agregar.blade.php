@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">@if(Session::has('titulo')) {{Session::get('titulo')}} @else Nuevo @endif Modulo</div>
				<div class="panel-body">
				@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Algo no va bien con el ingreso!</strong> Hay problemas con con los datos diligenciados.<br><br>
							<ul>							
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
							</ul>
					</div>
				@endif			
				@if(Session::has('message'))
					<div class="alert alert-info">
						<strong>¡Ingreso de Usuario!</strong> El modulo se ha agregado adecuadamente.<br><br>
						<ul>								
							<li>{{ Session::get('message') }}</li>
						</ul>
					</div>                
				@endif			    
			   	@if(Session::has('error'))			   		
					<div class="alert alert-danger">
						<strong>¡Algo no va bien!</strong> Hay problemas con los datos diligenciados.<br><br>
							<ul>								
								<li>{{ Session::get('error') }}</li>								
							</ul>
					</div>                
				@endif	
						
				{!! Form::open(array('url' => 'modulo/save')) !!}	
					
					<ul class="nav nav-tabs">
					  <li role="presentation" class="active"><a href="#tab1" data-toggle="tab">Modulo</a></li>
					  <li role="presentation"><a href="#tab2" data-toggle="tab">Preferencias</a></li>					  				 
					</ul>	
					<div class="panel-body">
                    <div class="tab-content">
						<div class="tab-pane fade in active" id="tab1">
							<div class="form-group">
								{!! Form::label('rol', 'Modulo', array('class' => 'col-md-4 control-label')) !!}							
								<div class="col-md-12">
									{!! Form::text('module', old('module'), array('class' => 'form-control','placeholder'=>'Ingresa el modulo', 'autofocus'=>'autofocus'))!!}
								</div>
							</div>							
							<div class="form-group">
								{!! Form::label('descripcion', 'Descripción', array('class' => 'col-md-4 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('description', old('description'), array('class' => 'form-control','placeholder'=>'Ingresa el la descripción'))!!}
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('app', 'Aplicación', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::select('app_id',Session::get('modulo.apps'),old('app_id'),array('class' => 'form-control','placeholder'=>'Ingresa la aplicación')) !!}
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="tab2">
							<div class="form-group">
								{!! Form::label('js', 'JavaScript', array('class' => 'col-md-4 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('js', old('js'), array('class' => 'form-control','placeholder'=>'Ingresa el aechivo Javascript'))!!}
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('categoria', 'Categoria', array('class' => 'col-md-4 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('categoria', old('categoria'), array('class' => 'form-control','placeholder'=>'Ingresa la categoria'))!!}
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('controlador', 'Controlador', array('class' => 'col-md-4 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('controlador', old('controlador'), array('class' => 'form-control','placeholder'=>'Ingresa el controlador'))!!}
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('uiicono', 'Icono JQuery', array('class' => 'col-md-4 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('uiicono', old('uiicono'), array('class' => 'form-control','placeholder'=>'Ingresa el icono JQuery'))!!}
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('icono', 'Icono', array('class' => 'col-md-4 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('icono', old('icono'), array('class' => 'form-control','placeholder'=>'Ingresa el icono'))!!}
								</div>
							</div>
						</div>
					</div>
					</div>	
				
																
					
					<!-- Aprovechar el formulario para editar -->
					{!! Form::hidden('edit', old('edit')) !!}
					{!! Form::hidden('modulo_id', old('modulo_id')) !!}
					
					<div class="form-group">
						<div class="col-md-6 col-md-offset-0">
							{!! Form::submit('Enviar', array('class' => 'btn btn-primary')) !!}																
						</div>
					</div>										
			
				{!! Form::close() !!}				
				</div>
			</div>		
		</div>
	</div>	
</div>		
@endsection

@section('script')		
	<script type="text/javascript">  	
		
	</script>
@endsection