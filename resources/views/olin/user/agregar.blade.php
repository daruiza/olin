@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
	
		<div class="col-md-8 col-md-offset-2">
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
						<strong>¡Ingreso de Afiliados!</strong>Los Afiliados se ha agregado adecuadamente.<br><br>
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
		</div>
		
		@if( !Session::get('modulo.entidad') )
			<div class="col-md-5 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Carga masiva de Afiliados CSV Refinado</div>
					<div class="panel-body">
						{!! Form::open(array('url' => 'afiliados/save','method'=>'POST','files'=>true)) !!}			
							<div class="panel-body">
								
								<div class="form-group">
									{!! Form::label('seat', 'Sede', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::select('seat_id',Session::get('modulo.seats'),old('seat_id'),array('class' => 'form-control','placeholder'=>'Ingresa la sede','id'=>'id_seat','onchange' => 'javascript:oli_afiliado.changeSelect(this)')) !!}
									</div>
								</div>
								
								<div class="form-group">
									{!! Form::label('company', 'Empresa', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::select('com_id',array(),old('com_id'),array('class' => 'form-control','placeholder'=>'Ingresa la empresa','id'=>'id_company','onchange' => 'javascript:oli_afiliado.changeSelect(this)')) !!}
									</div>
								</div>
								
								<div class="form-group">
									{!! Form::label('link', 'Vinculo', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::select('link_id',array(),old('link_id'),array('class' => 'form-control','placeholder'=>'Ingresa el vinculo','id'=>'id_link')) !!}
									</div>
								</div>
								
								<div class="form-group">
									{!! Form::label('file', 'Archivo a cargar', array('class' => 'col-md-12 control-label')) !!}							
									<div class="col-md-12">
										{!! Form::file('carga') !!}
									</div>
								</div>
								
								
								<div class="form-group">							
									<div class="col-md-12">
										{{ Form::checkbox('check_carga', 1, true) }}
										<span class="">Sobre-escribir datos.  <i>(recomendado)</i></span>
									</div>
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
		
		
			<div class="col-md-5 col-md-offset-0">
				<div class="panel panel-default">
					<div class="panel-heading">Carga masiva por XLSX </div>					
					<div class="panel-body">
					{!! Form::open(array('url' => 'afiliados/saveseat','method'=>'POST','files'=>true)) !!}			
							<div class="panel-body">
								
								<div class="form-group">
									{!! Form::label('seat', 'Sede', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::select('seat_ide',Session::get('modulo.seats'),null,array('class' => 'form-control','placeholder'=>'Ingresa la sede','id'=>'id_seate','onchange' => 'javascript:oli_afiliado.changeSelectEntidad(this)')) !!}
									</div>
								</div>
								
								<div class="form-group">
									{!! Form::label('company', 'Empresa', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::select('com_ide',array(),null,array('class' => 'form-control','placeholder'=>'Ingresa la empresa','id'=>'id_companye','onchange' => 'javascript:oli_afiliado.changeSelectEntidad(this)')) !!}
									</div>
								</div>
								
								<div class="form-group">
									{!! Form::label('link', 'Vinculo', array('class' => 'col-md-12 control-label')) !!}
									<div class="col-md-12">
										{!! Form::select('link_ide',array(),null,array('class' => 'form-control','placeholder'=>'Ingresa el vinculo','id'=>'id_linke')) !!}
									</div>
								</div>
								
								<div class="form-group">
									{!! Form::label('file', 'Archivo a cargar', array('class' => 'col-md-12 control-label')) !!}							
									<div class="col-md-12">
										{!! Form::file('carga_seat') !!}
									</div>
								</div>
								
								
								<div class="form-group">							
									<div class="col-md-12">
										{{ Form::checkbox('check_carga_seat', 1, true) }}
										<span class="">Sobre-escribir datos.  <i>(recomendado)</i></span>
									</div>
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
			
			{!! Form::open(array('id'=>'form_select','url' => 'afiliados/select')) !!}
			{!! Form::close() !!}    
					
		@else
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Carga masiva Entidad: {{ Session::get('modulo.entidad') }}</div>
					<div class="panel-body">
					</div>
				</div>
			</div>
		
		@endif	
		
	</div>	
</div>		
@endsection

@section('script')		
	<script type="text/javascript">  	
		
	</script>
@endsection