@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Limpieza masiva de Afiliados</div>
				<div class="panel-body">
				@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Algo no va bien con la limpieza!</strong> Hay problemas con con los datos diligenciados.<br><br>
							<ul>							
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
							</ul>
					</div>
				@endif			
				@if(Session::has('message'))
					<div class="alert alert-info">
						<strong>¡Limpieza de Afiliados!</strong>Los Afiliados se ha agregado adecuadamente.<br><br>
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
						
				{!! Form::open(array('url' => 'afiliados/delete','method'=>'POST')) !!}			
				
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
							<div class="col-md-12">
								{!! Form::checkbox('all', 1,false) !!} <span class="">Borrar Toda la base  <i>(no recomendado)</i></span>
							</div>
						</div>
						
					</div>
					<div class="form-group">
						<div class="col-md-6 col-md-offset-0">
							{!! Form::submit('Enviar', array('class' => 'btn btn-primary')) !!}																
						</div>
					</div>										
			
				{!! Form::close() !!}	
				
				{!! Form::open(array('id'=>'form_select','url' => 'afiliados/select')) !!}
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
