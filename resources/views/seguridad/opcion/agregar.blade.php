@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">@if(Session::has('titulo')) {{Session::get('titulo')}} @else Nueva @endif Opción</div>
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
						<strong>¡Ingreso de Opción!</strong> La opcion se ha agregado adecuadamente.<br><br>
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
						
				{!! Form::open(array('url' => 'opcion/save')) !!}			
				
					<div class="form-group">
						{!! Form::label('option', 'Opción', array('class' => 'col-md-4 control-label')) !!}							
						<div class="col-md-12">
							{!! Form::text('option', old('option'), array('class' => 'form-control','placeholder'=>'Ingresa la Opción', 'autofocus'=>'autofocus'))!!}
						</div>
					</div>

					<div class="form-group">
						{!! Form::label('action', 'Acción', array('class' => 'col-md-4 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('action', old('action'), array('class' => 'form-control','placeholder'=>'Ingresa la acción'))!!}
						</div>
					</div>	
					
					<div class="form-group">
						{!! Form::label('lugar', 'Lugar', array('class' => 'col-md-4 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('lugar', old('lugar'), array('class' => 'form-control','placeholder'=>'Ingresa el lugar'))!!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('vista', 'Vista', array('class' => 'col-md-4 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('vista', old('vista'), array('class' => 'form-control','placeholder'=>'Ingresa la vista'))!!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('icono', 'Icono', array('class' => 'col-md-4 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('icono', old('icono'), array('class' => 'form-control','placeholder'=>'Ingresa el icono'))!!}
						</div>
					</div>											
					
					<!-- Aprovechar el formulario para editar -->
					{!! Form::hidden('edit', old('edit')) !!}
					{!! Form::hidden('option_id', old('option_id')) !!}
					
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