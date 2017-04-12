@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">@if(Session::has('titulo')) {{Session::get('titulo')}} @else Nueva @endif Empresa</div>
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
						<strong>¡Ingreso de Empresa!</strong> La Empresa se ha agregado adecuadamente.<br><br>
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
						
				{!! Form::open(array('url' => 'empresas/save')) !!}
					<ul class="nav nav-tabs">
					  <li role="presentation" class="active"><a href="#tab1" data-toggle="tab">Empresa</a></li>
					  <li role="presentation"><a href="#tab2" data-toggle="tab">Vinculos</a></li>					  				 
					</ul>			
					<div class="panel-body">
	                    <div class="tab-content">
							<div class="tab-pane fade in active" id="tab1">
								<div class="form-group">
									{!! Form::label('company', 'Empresa', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										{!! Form::text('company', old('conpany'), array('class' => 'form-control','placeholder'=>'Ingresa el nombre de la empresa', 'autofocus'=>'autofocus'))!!}
									</div>
								</div>
			
								<div class="form-group">
									{!! Form::label('descripcion', 'Descripción', array('class' => 'col-md-4 control-label')) !!}
									<div class="col-md-12">
										{!! Form::text('description', old('description'), array('class' => 'form-control','placeholder'=>'Ingresa el la descripción'))!!}
									</div>
								</div>
								
								<div class="form-group">
									{!! Form::label('seat', 'Sede', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::select('seat_id',Session::get('modulo.seats'),old('seat_id'),array('class' => 'form-control','placeholder'=>'Ingresa la sede')) !!}
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="tab2">
								@if(Session::has('modulo.links'))
									@foreach (Session::get('modulo.links') as $link)
										<div class="form-group input-grp">
											<div class = "col-md-12">
												<div class = "col-md-6 "> 
													@if (Session::has('modulo.com_links'))
														@if (in_array($link['id'],Session::get('modulo.com_links')))
															<input checked="checked" name="{{Session::get('modulo.id').'_'.$link['link']}}" value="{{$link['id']}}" id="{{Session::get('modulo.id').'_'.$link['link']}}" type="checkbox">
														@else
															{{ Form::checkbox(Session::get('modulo.id').'_'.$link['link'], $link['id'],old(Session::get('modulo.id').'_'.$link['link'])) }}
														@endif
													@else
														{{ Form::checkbox(Session::get('modulo.id').'_'.$link['link'], $link['id'],old(Session::get('modulo.id').'_'.$link['link'])) }}
													@endif													
													<span class="glyphicon glyphicon-link">{{ $link['link']}}</span>
												</div>
												<div class = "col-md-6 ">  {{$link['description']}}</div>
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
					{!! Form::hidden('company_id', old('company_id')) !!}
					
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