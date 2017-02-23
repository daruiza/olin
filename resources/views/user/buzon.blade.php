@extends('app')

@section('content')
	
	@if (count($errors) > 0)
			<div class="alert alert-danger">
				<strong>Algo no va bien con el Buzón!</strong> Hay problemas con con los datos Consultados.<br><br>
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
			
		@if(Session::has('message'))
			<div class="alert alert-info">
				<strong>¡Buzón de Mensajes!</strong> La operación se ha realizado adecuadamente.<br><br>
				<ul>								
					<li>{{ Session::get('message') }}</li>
				</ul>
			</div>
                
		@endif
 		<!-- error llega cuando se esta recuperando la contraseña inadecuadamente -->
		@if(Session::has('error'))
			<div class="alert alert-danger">
				<strong>¡Algo no va bien!</strong> Hay problemas con los datos.<br><br>
				<ul>								
					<li>{{ Session::get('error') }}</li>								
				</ul>
			</div>
                
		@endif
		Hola buzón	

@endsection
