@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">@if(Session::has('titulo')) {{Session::get('titulo')}} @else Nueva @endif Sede</div>
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
						<strong>¡Ingreso Sede!</strong> La sede se ha agregado adecuadamente.<br><br>
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
						
				{!! Form::open(array('url' => 'sedes/save')) !!}	
				
				<ul class="nav nav-tabs">
				  <li role="presentation" class="active"><a href="#tab1" data-toggle="tab">Sede</a></li>
				  <li role="presentation"><a href="#tab2" data-toggle="tab">Usuarios</a></li>				  
				</ul>
				<div class="panel-body">
	            	<div class="tab-content">
	            	
	            		<div class="tab-pane fade in active" id="tab1">							
							<div class="form-group">
								{!! Form::label('seat', 'Sede', array('class' => 'col-md-4 control-label')) !!}							
								<div class="col-md-12">
									{!! Form::text('seat', old('seat'), array('class' => 'form-control','placeholder'=>'Ingresa la sede', 'autofocus'=>'autofocus'))!!}
								</div>
							</div>						
							<div class="form-group">
								{!! Form::label('phone', 'Teléfono', array('class' => 'col-md-4 control-label')) !!}							
								<div class="col-md-12">
									{!! Form::text('phone', old('phone'), array('class' => 'form-control','placeholder'=>'Ingresa el teléfono', 'autofocus'=>'autofocus'))!!}
								</div>
							</div>	
							<div class="form-group">
								{!! Form::label('descripcion', 'Descripción', array('class' => 'col-md-4 control-label')) !!}
								<div class="col-md-12">
									{!! Form::text('description', old('description'), array('class' => 'form-control','placeholder'=>'Ingresa la descripción'))!!}
								</div>
							</div>						
						</div>
						
						<div class="tab-pane fade" id="tab2">
							@if(Session::has('modulo.users'))
								@foreach (Session::get('modulo.users') as $usr)
									<div class="form-group input-grp">
										<div class = "col-md-12">									
											@if (Session::has('modulo.users_seats'))												
												@if (in_array($usr->user_id,Session::get('modulo.users_seats')))
													<input checked="checked" name="{{Session::get('modulo.id').'_'.$usr->name}}" value="{{$usr->user_id}}" id="{{Session::get('modulo.id').'_'.$usr->name}}" type="checkbox">
												@else
													{{ Form::checkbox(Session::get('modulo.id').'_'.$usr->name, $usr->user_id,old(Session::get('modulo.id').'_'.$usr->name)) }}
												@endif
											@else
												{{ Form::checkbox(Session::get('modulo.id').'_'.$usr->name, $usr->user_id,old(Session::get('modulo.id').'_'.$usr->name)) }}
											@endif
											<span class="glyphicon glyphicon-link" >{{ $usr->name}} - {{ $usr->names}} {{ $usr->surnames}}</span>
										</div>						
									</div>								
								@endforeach							
							@endif	
						</div>
						
	            	</div>
            	</div>
				<!-- Aprovechar el formulario para editar -->
				{!! Form::hidden('mod_id', Session::get('modulo.id')) !!}
				{!! Form::hidden('edit', old('edit')) !!}
				{!! Form::hidden('seat_id', old('seat_id')) !!}
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