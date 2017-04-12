@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">@if(Session::has('titulo')) {{Session::get('titulo')}} @else Nuevo @endif Usuario</div>
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
						<strong>¡Ingreso de Usuario!</strong> El usuario se ha agregado adecuadamente.<br><br>
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
						
				{!! Form::open(array('url' => 'usuario/save')) !!}			
				
				<ul class="nav nav-tabs">
				  <li role="presentation" class="active"><a href="#tab1" data-toggle="tab">Credencial</a></li>
				  <li role="presentation"><a href="#tab2" data-toggle="tab">Perfil</a></li>
				  <li role="presentation"><a href="#tab3" data-toggle="tab">Aplicaciones</a></li>				 
				</ul>
				
				<div class="panel-body">
                    <div class="tab-content">
						<div class="tab-pane fade in active" id="tab1">
							<div class="form-group">                        		
								{!! Form::label('name', 'Usuario', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('name', old('name'), array('class' => 'form-control','placeholder'=>'Ingresa el nombre de usuario')) !!}
								</div>
							</div>
							<div class="form-group"> 
								@if(Session::has('titulo'))
									{!! Form::label('password', 'Contraseña', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::password('password', array('class' => 'form-control','placeholder'=>'Nueva contraseña de usuario')) !!}
									</div>
								@else
									{!! Form::label('password', 'Contraseña', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::password('password', array('class' => 'form-control','placeholder'=>'Ingresa la contraseña de usuario, por defecto es: 0000')) !!}
									</div>
								@endif                       		
								
							</div>
							<div class="form-group">
								{!! Form::label('email', 'Correo electronico', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::email('email', old('email'), array('class' => 'form-control','placeholder'=>'Ingresa el email')) !!}
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('rol', 'Rol', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::select('rol',Session::get('modulo.roles'),old('rol_id'),array('class' => 'form-control','placeholder'=>'Ingresa el rol')) !!}
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="tab2">
							<div class="form-group input-grp">
								{!! Form::label('names', 'Nombres', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('names', old('names'), array('class' => 'form-control','placeholder'=>'Ingresa los nombres')) !!}
								</div>
							</div>
		
							<div class="form-group input-grp">
								{!! Form::label('surnames', 'Apellidos', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('surnames', old('surnames') ,array('class' => 'form-control','placeholder'=>'Ingresa los apellidos')) !!}
								</div>
							</div>
							<div class="form-group input-grp">
								{!! Form::label('identificacion', 'Identiticación', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('identificacion', old('identificacion') ,array('class' => 'form-control','placeholder'=>'Ingresa la  identificación')) !!}
								</div>
							</div>
							
							<div class="form-group input-grp">
								{!! Form::label('birthdate', 'Fecha de nacimiento', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('birthdate',old('birthdate'), array('class' => 'form-control','placeholder'=>'Ingresa la fecha de nacimiento; aaa-mm-dd')) !!}
								</div>
							</div>
							
							<div class="form-group input-grp">
								{!! Form::label('sex', 'Sexo', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::select('sex',array('Masculino' => 'Masculino', 'Femenino' => 'Femenino'),old('sex'), array('class' => 'form-control','placeholder'=>'Ingresa el sexo')) !!}
								</div>
							</div>
							
							<div class="form-group input-grp">
								{!! Form::label('adress', 'Dirección', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('adress',old('adress'), array('class' => 'form-control','placeholder'=>'Ingresa la dirección')) !!}
								</div>
							</div>
							
							<div class="form-group input-grp">
								{!! Form::label('movil_number', 'Movil', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('movil_number',old('movil_number'), array('class' => 'form-control','placeholder'=>'Ingresa el número de movil')) !!}
								</div>
							</div>
							
							<div class="form-group input-grp">
								{!! Form::label('fix_number', 'Fijo', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('fix_number',old('fix_number'), array('class' => 'form-control','placeholder'=>'Ingresa el número fijo')) !!}
								</div>
							</div>
																		
							<div class="form-group input-grp">
								{!! Form::label('perfil_description', 'Descripción', array('class' => 'col-md-12 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('perfil_description',old('perfil_description'), array('class' => 'form-control','placeholder'=>'Ingresa la descripcion')) !!}
								</div>
							</div>										
						</div>
						<div class="tab-pane fade" id="tab3">
							
							@if(Session::has('modulo.apps'))
								@foreach (Session::get('modulo.apps') as $app)
									<div class="form-group input-grp">
										<div class = "col-md-12">
										<div class = "col-md-4 ">
											@if (Session::has('modulo.user_apps'))												
												@if (in_array($app['id'],Session::get('modulo.user_apps')))
													<input checked="checked" name="{{Session::get('modulo.id').'_'.$app['app']}}" value="{{$app['id']}}" id="{{Session::get('modulo.id').'_'.$app['app']}}" type="checkbox">
												@else
													{{ Form::checkbox(Session::get('modulo.id').'_'.$app['app'], $app['id'],old(Session::get('modulo.id').'_'.$app['app'])) }}
												@endif
											@else
												{{ Form::checkbox(Session::get('modulo.id').'_'.$app['app'], $app['id'],old(Session::get('modulo.id').'_'.$app['app'])) }}
											@endif
											<span class="{{ json_decode($app['preferences'], true)['icono'] }}">{{ $app['app']}}</span>
										</div>									
										<div class = "col-md-8 ">  {{$app['description']}}</div>
										</div>								
									</div>								
								@endforeach							
							@endif	
							
						</div>						
						{!! Form::hidden('mod_id', Session::get('modulo.id')) !!}
						{!! Form::hidden('edit', old('edit')) !!}
						{!! Form::hidden('user_id', old('user_id')) !!}												
					</div>
				</div>				
				
				<div class="form-group">
					<div class="col-md-2 col-md-offset-0 ">
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
		javascript:seg_user.iniciarDatepiker('birthdate');	
		javascript:$('#rol').addClass('form-control');	
	</script>
@endsection