@extends('app')

@section('content')
	<!-- Mensajes y aletas -->
	<!-- Este se usa para validar formularios -->
	@if (count($errors) > 0)
		<div class="alert alert-danger fade in">
			<strong>Algo no va bien con el modulo Documentos!</strong> Hay problemas con con los datos diligenciados.<br><br>
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
		</div>
	@endif	
	<!-- Este se usa para mostrar mensajes -->		
	@if(Session::has('message'))
		<div class="alert alert-info fade in">
			<strong>¡Actualización de Informacion!</strong> El registro se ha actualizado adecuadamente.<br><br>
			<ul>								
				<li>{{ Session::get('message') }}</li>
			</ul>
		</div>                
	@endif
    <!-- Mesnsajes de error -->
   @if(Session::has('error'))
		<div class="alert alert-danger fade in">
			<strong>¡Algo no va bien!</strong> Hay problemas con los datos diligenciados.<br><br>
				<ul>								
					<li>{{ Session::get('error') }}</li>								
				</ul>
		</div>                
	@endif
	
	<div class="row">		
		<div class="col-md-10 col-md-offset-1">
						
			<div class="col-md-8" style="text-align: justify;">
				<div  style="text-align: center;">
					<h4>INTEGRADOR OLIVOS - OLIN</h4>
					<h5>PERFIL DE USUARIO Y SALIDA SEGURA</h5>				
				</div>
				<p>Luego de ingresar en la aplicación, hallaremos en la parte superior derecha
				un selector o botón desplegable que tiene el nombre del usuario como titulo;
				Dicho botón cuenta con dos opciones: <b><i>Perfil y Salida Segura</i></b> como se ilusta en la figura #1.
				</p>
				<p>
					La opción <b><i>Salida Segura</i></b> se usara al momento de cerrar la sesión de trabajo o ante un
					 cambio de usuario; mientras que la opción <b><i>Perfil</i></b> se usara para configurar las caracteristicas
					  de usuario: </br > 
					  	<ul>
					  		<li>Cambio de contraseña.</li>
					  		<li>Edición de información de usuario.</li> 
					  		<li>Cambio de entorno de trabajo, que puede ser el escritorio o la papelera.</li>
				  		</ul>				  
				</p>			
				<h5 style="margin-top: 3%;">PERFIL DE USUARIO</h5>
				<p>Luego de seleccionar la opción <b><i>Perfil</i></b>, se despliega en pantalla una interfaz con toda la información
				 disponible del usuario; Dicha información puede ser administrada desde el panel laterarl izquierdo. Ver figuara #2. 
				 </br > 
				 <ul>
			  		<li>Para cambiar la imagen, luego de dar clic sobre la imagen actual y usamos el boton examinar para seleccionar una nueva imagen, a continuación damos clic en el botón Enviar. Ver imagen a continuación.</li>
			  		{{ Html::image('images/doc/nuevaImagen.png','Imagen no disponible',array( 'style'=>'width: 60%; border:2px solid #009440;border-radius: 3px;' ))}}
			  		<li>Para cambiar el entorno de trabajo, basta con dar clic en el botón Papelera. Generalmente para recuperar datos previamente borrados.</li> 
			  		<li>Para cambiar la contraseña, damos clic en la opción Cambiar contraseña y a continuación diligenciamos la nueva contraseña en la ventana emergente. Ver imagen a continuación.</li>
			  		{{ Html::image('images/doc/senia.png','Imagen no disponible',array( 'style'=>'width: 60%; border:2px solid #009440;border-radius: 3px;' ))}}
			  		<li>Para habilitar la edición de los datos de usuario, basta con dar clic en la opción Habilitar edición.</li>
		  		</ul>	
				</p>
				 </br > 
				<div  style="text-align: center;">					
					<h5>RECUPERAR CONTRASEÑA</h5>				
				</div>
				<p>Luego de un fallido intento de acceso a la aplicación, 
				</p>
				
			</div>
			<div class="col-md-4" style="text-align: justify;">
				{{ Html::image('images/doc/perfilSalida.png','Imagen no disponible',array( 'style'=>'width: 100%; border:2px solid #009440;border-radius: 3px;' ))}}
				<div style = "text-align: center;">
					Figura #1.
				</div><br>
				{{ Html::image('images/doc/perfil.png','Imagen no disponible',array( 'style'=>'width: 100%; border:2px solid #009440;border-radius: 3px;' ))}}
				<div style = "text-align: center;">
					Figura #2.
				</div>
			</div>
			<p>			
			</p>	
			
		</div>
		
	</div>
		
@endsection

@section('script')
	<script type="text/javascript">
					
	</script>
@endsection
    