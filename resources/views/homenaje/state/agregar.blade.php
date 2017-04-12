@extends('app')

@section('content')


<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">@if(Session::has('titulo')) {{Session::get('titulo')}} @else Nuevo @endif Estado</div>
				<div class="panel-body">
				@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Algo no va bien con el ingreso!</strong> Hay problemas con los datos diligenciados.<br><br>
							<ul>							
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
							</ul>
					</div>
				@endif			
				@if(Session::has('message'))
					<div class="alert alert-info">
						<strong>¡Ingreso de Estado!</strong> El estado se ha agregado adecuadamente.<br><br>
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
						
				{!! Form::open(array('url' => 'estado/save')) !!}			
					<div class="panel-body">
					<!-- Aqui todo el codigo del formulario -->
					<div class="form-group">
						{!! Form::label('estado', 'Estado', array('class' => 'col-md-4 control-label')) !!}							
						<div class="col-md-12">
							{!! Form::text('state', old('estado'), array('class' => 'form-control','placeholder'=>'Ingresa el estado', 'autofocus'=>'autofocus'))!!}
						</div>
					</div>

					<div class="form-group">
						{!! Form::label('alerta', 'Alerta', array('class' => 'col-md-4 control-label')) !!}							
						<div class="col-md-12">
							{!! Form::text('alert', old('alerta'), array('class' => 'form-control','placeholder'=>'Ingresa la alerta'))!!}
						</div>
					</div>

					<div class="form-group">
						{!! Form::label('order', 'Orden', array('class' => 'col-md-4 control-label')) !!}							
						<div class="col-md-12">
							{!! Form::text('order', old('order'), array('class' => 'form-control','placeholder'=>'Ingresa el Orden '))!!}
						</div>
					</div>

					<div class="form-group">
						{!! Form::label('descripcion', 'Descripción', array('class' => 'col-md-4 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('description', old('description'), array('class' => 'form-control','placeholder'=>'Ingresa la descripción'))!!}
						</div>
					</div>	

					

												

					
					<!-- Aprovechar el formulario para editar -->
					{!! Form::hidden('edit', old('edit')) !!}
					{!! Form::hidden('id', old('id')) !!}

					</div>	
					
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