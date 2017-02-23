@extends('app')

@section('content')
<style>
	.name_user{
		background-color: #009440;
	    color: #fff97f;
	    margin-top: 10px;
	    padding: 1%;
	    text-align: center;	    	
	}
	.perfilImage{
		width: 100%;
		cursor:pointer;	
	}
	.option_user{		
		border: 1px solid #009440;
    	border-radius: 1px;
    	margin-top: 10px;
    	padding: 3% 3% 3% 5%;
	}
	.message_user{
		cursor:pointer;
		color: #009440;
		padding: 2%;
	}
	.message_user:hover {
		background-color: #009440;
		color: #fff97f;
	}   

</style>
<div class="container-fluid">
	<div class="row">	
		<div class="col-md-2 col-md-offset-1" >
			<div class = "perfilImage" data-toggle="modal" data-target="#img_modal"> 
				{{ Html::image('images/user/'.Session::get('opaplus.usuario.avatar'),'Imagen no disponible',array( 'style'=>'width: 100%; border:2px solid #009440;border-radius: 3px;' ))}}
			</div>
			<div class="name_user">
				<div>
					{{Session::get('opaplus.usuario.names')}}
				</div>
				<div>
					{{Session::get('opaplus.usuario.surnames')}}
				</div>
			</div>
			
			<div class="option_user">
				<!-- 
				<div class = "message_user" >
					<span class="glyphicon glyphicon-envelope" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>Correo interno
				</div>
				
				<div class = "message_user" >
					<span class="glyphicon glyphicon-paperclip" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>Notificaciones
				</div>
				
				<div class = "message_user" >
					<span class="glyphicon glyphicon-calendar" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>Calendario
				</div>
				 -->			
				@if(Session::get('opaplus.usuario.lugar.active'))
					<div id = "0" class = "message_user bnt_lugar">
						<span class="glyphicon glyphicon-trash" aria-hidden="true" style = "margin-right:5px; color:#666699;"></span>Papelera
					</div>
					<div id = "1" class = "message_user bnt_lugar" style="display: none;">
						<span class="glyphicon glyphicon-home" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>Escritorio
					</div>
				@else
					<div id = "0" class = "message_user bnt_lugar" style="display: none;>
						<span class="glyphicon glyphicon-trash" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>Papelera
					</div>					
					<div id = "1" class = "message_user bnt_lugar" ">
						<span class="glyphicon glyphicon-home" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>Escritorio
					</div>
				@endif			
				
			</div>	
			
			<div class="option_user">
				
				<div id="btn_new_psw" class = "message_user" data-toggle="modal" data-target="#psw_modal"  >
					<span class="glyphicon glyphicon-cog" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>Cambiar contraseña
				</div>
								
				<div class = "message_user" onclick="javascript:seg_user.edit(this);">
					<span class="glyphicon glyphicon-wrench" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span><font>Habilitar edición</font>
				</div>
				
			</div>			
			
		</div>
		<div class="col-md-8 col-md-offset-0">		
			<div class="form-group">
				{!! Form::label('name', 'Usuario:', array('class' => 'col-md-2 control-label')) !!}
				<div class="col-md-10">
					{!! Form::label('name', value(Session::get('opaplus.usuario.name')), array('class' => 'col-md-2 control-label')) !!}
				</div>
			</div>
			
			<div class="form-group">
				{!! Form::label('rol', 'Rol:', array('class' => 'col-md-2 control-label')) !!}
				<div class="col-md-10">
					{!! Form::label('rol', value(Session::get('opaplus.usuario.rol')), array('class' => 'col-md-6 control-label')) !!}
				</div>
			</div>
			
			<div class="form-group">
				{!! Form::label('ultimo_ingreso', 'Ultimo acceso', array('class' => 'col-md-2 control-label')) !!}
				<div class="col-md-10">
					{!! Form::label('ultimo_ingreso:', value(Session::get('opaplus.usuario.ultimo_ingreso')), array('class' => 'col-md-6 control-label')) !!}
				</div>
			</div>
		</div>
		<div class="col-md-8 col-md-offset-0">			
			<div class="panel panel-default">
				<div class="panel-heading">Perfil de Usuario {{Session::get('opaplus.usuario.names')}}.</div>
				<div class="panel-body">
				<div class = "alerts">
				@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Algo no va bien con el perfil!</strong> Hay problemas con con los datos diligenciados.<br><br>
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
				
				@if(Session::has('message'))
					<div class="alert alert-info">
						<strong>¡Edición de perfil!</strong> Tu perfil se ha editado adecuadamente.<br><br>
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
	            </div>
	            <!-- La clase input-grp sirve para realizar el controles en la funcion seg_user.edit() de javascript-->
	            {!! Form::open(array('id'=>'form_user','url' => 'user/save')) !!}						
					<div class="form-group input-grp">
						{!! Form::label('name', 'Usuario', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('name', Session::get('opaplus.usuario.name'), array('class' => 'form-control','placeholder'=>'Ingresa tu usuario','disabled' => 'disabled')) !!}
						</div>
					</div>
					
					<div class="form-group input-grp">
						{!! Form::label('names', 'Nombres', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('names', Session::get('opaplus.usuario.names'), array('class' => 'form-control','placeholder'=>'Ingresa tus nombres','disabled' => 'disabled')) !!}
						</div>
					</div>

					<div class="form-group input-grp">
						{!! Form::label('surnames', 'Apellidos', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('surnames', value(Session::get('opaplus.usuario.surnames')) ,array('class' => 'form-control','placeholder'=>'Ingresa tus apellidos','disabled' => 'disabled')) !!}
						</div>
					</div>
					
					<div class="form-group input-grp">
						{!! Form::label('identificacion', 'Identiticación', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('identificacion', value(Session::get('opaplus.usuario.identificacion')) ,array('class' => 'form-control','placeholder'=>'Ingresa tu identificación','disabled' => 'disabled')) !!}
						</div>
					</div>
					
					<div class="form-group input-grp">
						{!! Form::label('birthdate', 'Fecha de nacimiento', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('birthdate',value(Session::get('opaplus.usuario.birthdate')), array('class' => 'form-control','placeholder'=>'Ingresa tu fecha de nacimiento; aaa-mm-dd','disabled' => 'disabled')) !!}
						</div>
					</div>
					
					<div class="form-group input-grp">
						{!! Form::label('sex', 'Sexo', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::select('sex',array('Masculino' => 'Masculino', 'Femenino' => 'Femenino'),value(Session::get('opaplus.usuario.sex')), array('class' => 'form-control','placeholder'=>'Ingresa tu sexo','disabled' => 'disabled')) !!}
						</div>
					</div>
					
					<div class="form-group input-grp">
						{!! Form::label('adress', 'Dirección', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('adress',value(Session::get('opaplus.usuario.adress')), array('class' => 'form-control','placeholder'=>'Ingresa tu dirección','disabled' => 'disabled')) !!}
						</div>
					</div>
					
					<div class="form-group input-grp">
						{!! Form::label('movil_number', 'Movil', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('movil_number',value(Session::get('opaplus.usuario.movil_number')), array('class' => 'form-control','placeholder'=>'Ingresa tu numero de movil','disabled' => 'disabled')) !!}
						</div>
					</div>						

					<div class="form-group input-grp">
						{!! Form::label('email', 'Correo electronico', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('email',value(Session::get('opaplus.usuario.email')), array('class' => 'form-control','placeholder'=>'Ingresa tu numero de correo electonico','disabled' => 'disabled')) !!}
						</div>
					</div>	
									
					<div class="form-group input-grp">
						{!! Form::label('perfil_description', 'Descripción', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::text('perfil_description',value(Session::get('opaplus.usuario.perfil_description')), array('class' => 'form-control','placeholder'=>'Ingresa tu descripcion','disabled' => 'disabled')) !!}
						</div>
					</div>				
										
					<div class="form-group">
						<div class="col-md-12 col-md-offset-0 ">
							{!! Form::submit('Enviar', array('class' => 'btn btn-primary','style' => 'display:none;')) !!}																
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
	
	<div class="modal fade" id="img_modal" role="dialog">
	    <div class="modal-dialog">	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title">Cambiar imagen de perfil</h4>
	        </div>
	        <div class="modal-body">
	        	 <div class="row">
	        	 	 {!! Form::open(array('id'=>'form_img','url' => 'user/saveimg','method'=>'POST', 'files'=>true)) !!}		
		        	 	<div class="col-md-6">
		        	 		<p> <label class="col-md-offset-2" for="name">Elije una imagen</label>	</p>
		        	 			<div class = "col-md-offset-1 col-md-10">
		        	 				<img id="nueva_img" src="images/user/noimagen.png" class="" alt="Imagen no disponible" style="width: 100%; border:2px solid #78a5b1;border-radius: 3px;">					        	 			
		        	 			</div>
		        	 		<p>{!! Form::file('image',array('id'=>'img_user')) !!}</p>
		        	 	</div>
		        	 	<div class="col-md-6">
		        	 		<p> <label class="col-md-offset-2" for="name">Tu actual imagen</label></p>
			        	 		<div class = "col-md-offset-1 col-md-10">
				        	 		{{ Html::image('images/user/'.Session::get('opaplus.usuario.avatar'),'Imagen no disponible',array( 'style'=>'width: 100%; border:2px solid #78a5b1;border-radius: 3px;' ))}}				        	 					        	 			
		        	 			</div>		        	 			
		        	 	</div>		        	 	 
	        	 	{!! Form::close() !!}
	        	 </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
	          <input class="btn btn-primary" type="submit" value="Enviar" id="submit" form="form_img">         
	        </div>
	      </div>
	      
	    </div>
	</div>
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
		        {!! Form::open(array('id'=>'form_psw','url' => 'user/savepsw','method'=>'POST')) !!}
	        		<div class="form-group">
						{!! Form::label('contrasenia_uno', 'Contraseña Actual', array('class' => 'col-md-12 control-label')) !!}
						<div class="col-md-12">
							{!! Form::password('contrasenia_uno', array('class' => 'form-control','placeholder'=>'Ingresa tu actual contraseña')) !!}
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
		        
		        <!-- Form en blanco para capturar la url -->
		        {!! Form::open(array('id'=>'form_lugar','url' => 'user/lugar')) !!}
		        {!! Form::close() !!}
		        </div>
	        </div>
        </div>
	</div>
	</div>
@endsection

@section('script')		
	<script type="text/javascript">  	
  		javascript:seg_user.iniciarDatepiker('birthdate');//todavia no esta definido
  		$("#img_user").change(function(){
  			seg_user.changeImg(this);
  		});
  		/*Petición sin usar ajax*/
  		$('.bnt_lugar').click(function(e){  	  		
  	  		var datos = new Array();
  	  		datos['lugar'] = this.id;
  	  		seg_ajaxobject.peticionpost($('#form_lugar').attr('action'),datos,"seg_user.lugarRespuesta");
  		});  		
  		/*cambia el estado de la variable de session de lugar de consulta, llamado a ajax*/  		
  		/*
  		$('.bnt_lugar').click(function(e){
  	  		e.preventDefault();//evita que la pagina se refresque
  	  		var datos = new Array();
  	  		datos['lugar'] = this.id;
  	  		seg_ajaxobject.peticionajax($('#form_lugar').attr('action'),datos,"seg_user.lugarRespuesta");
  		});
  		*/		
  		
	</script>
@endsection

