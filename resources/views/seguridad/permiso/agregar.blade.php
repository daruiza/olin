@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">@if(Session::has('titulo')) {{Session::get('titulo')}} @else Nuevo @endif Permiso</div>
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
						<strong>¡Ingreso de Permiso!</strong> El permiso se ha agregado adecuadamente.<br><br>
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
						
				{!! Form::open(array('url' => 'permiso/save')) !!}			
				
					<div class="form-group">
						{!! Form::label('rol', 'Rol', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::select('rol',Session::get('modulo.roles'),old('rol'),array('class' => 'form-control','placeholder'=>'Ingresa el rol')) !!}
						</div>
					</div>											
					
					<div class="form-group">
						{!! Form::label('module', 'Modulo', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::select('module',Session::get('modulo.modulos'),old('module'),array('class' => 'form-control','placeholder'=>'Ingresa el modulo')) !!}
						</div>
					</div>	
					
					<div class="form-group">
						{!! Form::label('option', 'Opción', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::select('option',Session::get('modulo.opciones'),old('option'),array('class' => 'form-control','placeholder'=>'Ingresa la opción')) !!}
						</div>
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