@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">		
		<div class="col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">Acceso a {{ Session::get('app') }}</div>
				<div class="panel-body">
				
					<!-- $error llega si la función validator falla en autenticar los datos -->
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Algo no va bien con el acceso!</strong> Hay problemas con los datos diligenciados.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul><br>							
							<div data-toggle="modal" data-target="#rpsw_modal" style = "cursor:pointer;" ><strong>Recuperar Contraseña!!!</strong></div><br>
						</div>
					@endif
					<!-- message llega si logout funciona adecuadamente -->
					@if(Session::has('message'))
						<div class="alert alert-info">
							<strong>¡Operación exitosa!</strong> La operación se ha ejecutado adecuadamente.<br><br>
							<ul>								
								<li>{{ Session::get('message') }}</li>								
							</ul>
						</div>
		                
		            @endif		            
		            <!-- error llega cuando se esta recuperando la contraseña inadecuadamente -->
		            @if(Session::has('error'))
						<div class="alert alert-danger">
							<strong>¡Algo no va bien!</strong> Hay problemas con los datos diligenciados.<br><br>
							<ul>								
								<li>{{ Session::get('error') }}</li>								
							</ul>
						</div>
		                
		            @endif
		            
					{!! Form::open(array('url' => 'auth/acces')) !!}
						<div class="panel-body">
												
							<div class="form-group">
								{!! Form::label('usuario', 'Usuario', array('class' => 'col-md-12 control-label')) !!}						
								<div class="col-md-12">
									{!! Form::text('usuario', old('usuario'), array('class' => 'form-control','placeholder'=>'Ingresa tu llave de usuario', 'autofocus'=>'autofocus'))!!}
								</div>
							</div>
	
							<div class="form-group">
								{!! Form::label('contrasenia', 'Contraseña', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::password('contrasenia', array('class' => 'form-control','placeholder'=>'Ingresa tu contraseña')) !!}
								</div>
							</div>
							
						</div>
						
						<div class="form-group">
							<div class="col-md-6 col-md-offset-0">
								{!! Form::submit('Ingresar', array('class' => 'btn btn-primary')) !!}																
							</div>
						</div>					
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>  
@endsection

@section('modal')
	
	<div class="modal fade" id="rpsw_modal" role="dialog">
	<div class="modal-dialog">	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title">Recuperar contraseña</h4>
	        </div>
	        <div class="modal-body">
		        <div class="row">
		        {!! Form::open(array('id'=>'form_rpsw','url' => 'auth/recoverpsw','method'=>'POST')) !!}
	        		<div class="form-group">
						{!! Form::label('email', 'Correo Electronico', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::email('email','', array('class' => 'form-control','placeholder'=>'Ingresa tu email')) !!}
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-md-6">
							{!! Form::submit('Enviar', array('class' => 'btn btn-primary')) !!}																
						</div>
					</div> 					  
					      
		        {!! Form::close() !!}
		        </div>
	        </div>
        </div>
	</div>
	</div>
	
	@if(Session::has('user'))	
	<div class="modal fade" id="psw_modal" role="dialog">
	<div class="modal-dialog">	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title">Cambiar contraseña</h4>
	        </div>
	        <div class="modal-body">
		        <div class="row">
		        {!! Form::open(array('id'=>'form_psw','url' => 'auth/savepsw','method'=>'POST')) !!}
	        		<div class="form-group">						
						<div class="col-md-12">
							{!! Form::hidden('id',Session::get('user.id') ) !!}
						</div>
					</div>
	        		<div class="form-group">						
						<div class="col-md-12">
							{!! Form::hidden('user',Session::get('user.name') ) !!}
						</div>
					</div>
	        		<div class="form-group">						
						<div class="col-md-12">
							{!! Form::hidden('contrasenia_uno',Session::get('user.password') ) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('contrasenia_dos', 'Contraseña Nueva', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::password('contrasenia_dos', array('class' => 'form-control','placeholder'=>'Ingresa tu nueva contraseña')) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('contrasenia_tres', 'Contraseña Nueva', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::password('contrasenia_tres', array('class' => 'form-control','placeholder'=>'Ingresa tu nueva contraseña')) !!}
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6">
							{!! Form::submit('Enviar', array('class' => 'btn btn-primary')) !!}																
						</div>
					</div> 					  
					      
		        {!! Form::close() !!}
		        </div>
	        </div>
        </div>
	</div>
	</div>		              
	@endif
	
@endsection

@section('script')
	<script type="text/javascript">  	
  		//disparamos el modal para recuperacion de contraseña
  		
  		$('#psw_modal').modal('show');  		
	</script>
@endsection

