@extends('app')

@section('content')
<!--  <script src="{{ asset('/js/lib/ckeditor/ckeditor.js') }}"></script> -->
<style>
	.ui-autocomplete{
	    color: #555555;
    	background-color: #ffffff;
    	background-image: none;
    	font-size: 14px;
    	line-height: 1.42857143;
    	font-family: inherit;
	}
	
	
    .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
	.ui-timepicker-div dl { text-align: left; }
	.ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
	.ui-timepicker-div dl dd { margin: 0 10px 10px 40%; }
	.ui-timepicker-div td { font-size: 90%; }
	.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
	.ui-timepicker-div .ui_tpicker_unit_hide{ display: none; }
	
	.ui-timepicker-div .ui_tpicker_time .ui_tpicker_time_input { background: none; color: inherit; border: none; outline: none; border-bottom: solid 1px #555; width: 95%; }
	.ui-timepicker-div .ui_tpicker_time .ui_tpicker_time_input:focus { border-bottom-color: #aaa; }
	
	.ui-timepicker-rtl{ direction: rtl; }
	.ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
	.ui-timepicker-rtl dl dt{ float: right; clear: right; }
	.ui-timepicker-rtl dl dd { margin: 0 40% 10px 10px; }
	
	/* Shortened version style */
	.ui-timepicker-div.ui-timepicker-oneLine { padding-right: 2px; }
	.ui-timepicker-div.ui-timepicker-oneLine .ui_tpicker_time, 
	.ui-timepicker-div.ui-timepicker-oneLine dt { display: none; }
	.ui-timepicker-div.ui-timepicker-oneLine .ui_tpicker_time_label { display: block; padding-top: 2px; }
	.ui-timepicker-div.ui-timepicker-oneLine dl { text-align: right; }
	.ui-timepicker-div.ui-timepicker-oneLine dl dd, 
	.ui-timepicker-div.ui-timepicker-oneLine dl dd > div { display:inline-block; margin:0; }
	.ui-timepicker-div.ui-timepicker-oneLine dl dd.ui_tpicker_minute:before,
	.ui-timepicker-div.ui-timepicker-oneLine dl dd.ui_tpicker_second:before { content:':'; display:inline-block; }
	.ui-timepicker-div.ui-timepicker-oneLine dl dd.ui_tpicker_millisec:before,
	.ui-timepicker-div.ui-timepicker-oneLine dl dd.ui_tpicker_microsec:before { content:'.'; display:inline-block; }
	.ui-timepicker-div.ui-timepicker-oneLine .ui_tpicker_unit_hide,
	.ui-timepicker-div.ui-timepicker-oneLine .ui_tpicker_unit_hide:before{ display: none; }
    
    table, th, td {
   		border: 1px solid black;
    	border-collapse: collapse;    	
	}
	th, td {
    	padding: 4px;
	}
	table {
		width:85%;
	}
	
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">		
			<div class="panel panel-default">
				<div class="panel-heading">@if(Session::has('titulo')) {{Session::get('titulo')}} @else Nueva @endif Solicitud</div>
				<div class="panel-body">
				<div class = "alerts">		
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
					@if(Session::has('message_add'))
						<div class="alert alert-info">
							<strong>¡Ingreso de la Solicitud!</strong> La solicitud se ha agregado adecuadamente.<br><br>
							<ul>
								@foreach (Session::get('message_add') as $message)
									<li>{{ $message }}</li>
								@endforeach													
							</ul>
							</br>
							<!--  <button class=" col-md-offset-4 btn" onclick="hom_solicitud.copiarMessage(this)">Copiar Mensaje</button> -->
						</div>                
					@endif	
					@if(Session::has('message_table'))
						<div class="alert alert-info">
							<strong>¡Ingreso de la Solicitud!</strong> El estado se ha agregado adecuadamente.<br><br>
							<table>								
								@foreach (Session::get('message_table') as $message)
									<tr>
										<td style = "background-color: #009440; color: #fff95f;">{{ $message[0] }}</td>
										<td>{{ $message[1] }}</td>
									</tr>
								@endforeach													
							</table>
							</br>
							<!--  <button class=" col-md-offset-4 btn" onclick="hom_solicitud.copiarMessage(this)">Copiar Mensaje</button> -->
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
				<!-- Datos para autocomplete inicial -->				
				@foreach (Session::get('modulo.companys') as $company)							
					<script type="text/javascript">  hom_solicitud.datos_company.push("{{$company}}"); </script>
				@endforeach	
						
				{!! Form::open(array('url' => 'solicitud/save')) !!}			
				
					<ul class="nav nav-tabs">
					  <li role="presentation" class="active"><a href="#tab1" data-toggle="tab">Contacto</a></li>
					  <li role="presentation"><a href="#tab2" data-toggle="tab">Titular</a></li>
					  <li role="presentation"><a href="#tab3" data-toggle="tab">Homenaje</a></li>				 
					</ul>
					
					<div class="panel-body">
						<div class="tab-content">
						
							<div class="tab-pane fade in active" id="tab1">								
								
								<div class="form-group">
									{!! Form::label('nombre_contacto', 'Nombres', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.nombre_contacto'))
												{!! Form::text('name_contacto', old('nombre_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el nombre', 'autofocus'=>'autofocus','disabled' => 'disabled'))!!}
												{!! Form::hidden('nombre_contacto', old('nombre_contacto'), array('class' => 'form-control'))!!}					
											@else
												{!! Form::text('nombre_contacto', old('nombre_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el nombre', 'autofocus'=>'autofocus'))!!}
											@endif
										@else
											{!! Form::text('nombre_contacto', old('nombre_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el nombre', 'autofocus'=>'autofocus'))!!}
										@endif										
									</div>								
								</div>
								
								<div class="form-group">
									{!! Form::label('telefono_contacto', 'Telefono', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.telefono_contacto'))
												{!! Form::text('phone_contacto', old('telefono_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el Teléfono','disabled' => 'disabled' ))!!}
												{!! Form::hidden('telefono_contacto', old('telefono_contacto'), array('class' => 'form-control'))!!}
											@else
												{!! Form::text('telefono_contacto', old('telefono_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el Teléfono', ))!!}
											@endif
										@else
											{!! Form::text('telefono_contacto', old('telefono_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el Teléfono', ))!!}
										@endif										
									</div>								
								</div>
								
								<div class="form-group">
									{!! Form::label('celular_contacto', 'Celular', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.celular_contacto'))
												{!! Form::text('cellfohone_contacto', old('celular_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el Celular','disabled' => 'disabled' ))!!}
												{!! Form::hidden('celular_contacto', old('celular_contacto'), array('class' => 'form-control'))!!}
											@else
												{!! Form::text('celular_contacto', old('celular_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el Celular', ))!!}
											@endif
										@else
											{!! Form::text('celular_contacto', old('celular_contacto'), array('class' => 'form-control','placeholder'=>'Ingresa el Celular', ))!!}
										@endif
										
									</div>								
								</div>								
								
								
							</div>
							
							<div class="tab-pane fade" id="tab2">
							
								<div class="form-group">
									{!! Form::label('identification_titular', 'Identificaión', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.identification_titular'))
												{!! Form::text('id_titular', old('identification_titular'), array('class' => 'form-control id_headline','placeholder'=>'Ingresa la identificación de titular','disabled' => 'disabled' ))!!}
												{!! Form::hidden('identification_titular', old('identification_titular'), array('class' => 'form-control'))!!}
											@else
												{!! Form::text('identification_titular', old('identification_titular'), array('class' => 'form-control id_headline','placeholder'=>'Ingresa la identificación de titular', ))!!}
											@endif
										@else
											{!! Form::text('identification_titular', old('identification_titular'), array('class' => 'form-control id_headline','placeholder'=>'Ingresa la identificación de titular', ))!!}
											
										@endif										
									</div>								
								</div>
								
								<div class="form-group">
									{!! Form::label('nombre_titular', 'Nombres Titular', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.entidad'))
												{!! Form::text('name_titular', old('nombre_titular'), array('id'=>'name_headline','class' => 'form-control','placeholder'=>'Ingresa el nombre','disabled' => 'disabled' ))!!}
												{!! Form::hidden('nombre_titular', old('nombre_titular'), array('class' => 'form-control'))!!}
											@else
												{!! Form::text('nombre_titular', old('nombre_titular'), array('id'=>'name_headline','class' => 'form-control','placeholder'=>'Ingresa el nombre' ))!!}
											@endif
										@else
											{!! Form::text('nombre_titular', old('nombre_titular'), array('id'=>'name_headline','class' => 'form-control','placeholder'=>'Ingresa el nombre' ))!!}
										@endif										
									</div>								
								</div>
								
								<div class="form-group">
									{!! Form::label('entidad', 'Entidad', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.entidad'))
												{!! Form::text('seat', old('entidad'), array('id'=>'company','class' => 'form-control','placeholder'=>'Ingresa la entidad','disabled' => 'disabled' ))!!}
												{!! Form::hidden('entidad', old('entidad'), array('class' => 'form-control'))!!}
											@else
												{!! Form::text('entidad', old('entidad'), array('id'=>'company','class' => 'form-control','placeholder'=>'Ingresa la entidad'))!!}
											@endif
										@else
											{!! Form::text('entidad', old('entidad'), array('id'=>'company','class' => 'form-control','placeholder'=>'Ingresa la entidad'))!!}
										@endif
									</div>								
								</div>
							
							</div>
							
							<div class="tab-pane fade" id="tab3">
							
								<div class="form-group">
									{!! Form::label('identification_homenaje', 'Identificaión', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.identification_homenaje'))
												{!! Form::text('id_homenaje', old('identification_homenaje'), array('class' => 'form-control ','placeholder'=>'Ingresa la identificación de homenaje', 'disabled' => 'disabled'))!!}
												{!! Form::hidden('identification_homenaje', old('identification_homenaje'), array('class' => 'form-control'))!!}
											@else
												{!! Form::text('identification_homenaje', old('identification_homenaje'), array('class' => 'form-control id_homage','placeholder'=>'Ingresa la identificación de homenaje', ))!!}
											@endif
										@else
											{!! Form::text('identification_homenaje', old('identification_homenaje'), array('class' => 'form-control id_homage','placeholder'=>'Ingresa la identificación de homenaje', ))!!}
										@endif	
									</div>								
								</div>
								
								<div class="form-group">
									{!! Form::label('nombre_homenaje', 'Nombres', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.nombre_homenaje'))
												{!! Form::text('name_homenaje', old('nombre_homenaje'), array('class' => 'form-control','placeholder'=>'Ingresa el nombre', 'disabled' => 'disabled'))!!}
												{!! Form::hidden('nombre_homenaje', old('nombre_homenaje'), array('class' => 'form-control'))!!}
											@else
												{!! Form::text('nombre_homenaje', old('nombre_homenaje'), array('id'=>'homage_name','class' => 'form-control','placeholder'=>'Ingresa el nombre', ))!!}
											@endif
										@else
											{!! Form::text('nombre_homenaje', old('nombre_homenaje'), array('id'=>'homage_name','class' => 'form-control','placeholder'=>'Ingresa el nombre', ))!!}
										@endif
									</div>								
								</div>
								
								<div class="form-group">
									{!! Form::label('ubicacion_homenaje', 'Ubicación', array('class' => 'col-md-4 control-label')) !!}							
									<div class="col-md-12">
										@if(Session::get('_old_input.solicitud_id'))
											@if(Session::get('_old_input.ubicacion_homenaje'))
												{!! Form::text('location_homenaje', old('ubicacion_homenaje'), array('class' => 'form-control','placeholder'=>'Ingresa la ubicación', 'disabled' => 'disabled'))!!}
												{!! Form::hidden('ubicacion_homenaje', old('ubicacion_homenaje'), array('class' => 'form-control'))!!}
											@else
												{!! Form::text('ubicacion_homenaje', old('ubicacion_homenaje'), array('class' => 'form-control','placeholder'=>'Ingresa la ubicación', ))!!}
											@endif
										@else
											{!! Form::text('ubicacion_homenaje', old('ubicacion_homenaje'), array('class' => 'form-control','placeholder'=>'Ingresa la ubicación', ))!!}
										@endif
									</div>								
								</div>								
								
							</div>
							
						</div>
					</div>					
					
					
					<div class="panel-body panel-form-pie">
						<div class="panel-body panel-form-pie">				
						@if(Session::get('_old_input.solicitud_id'))
							<!-- Los siguientes 2 divs son de interacción con javascript -->
							<div id="description_state" data-field-id="{{Session::get('_old_input.description')}}"></div>
							<div id="id_state" data-field-id="{{Session::get('modulo.state')}}"></div>						

							<div class="form-group">								
								<div class="col-md-12">
								@if(Session::has('modulo.states'))									
									@foreach (Session::get('modulo.states.id') as $key => $stt_id)
										<!-- Pintamos el elegido  -->
										@if($stt_id == Session::get('modulo.state'))											
											<div class="col-md-12 col-md-offset-0 " style="background-color: {{Session::get('modulo.states.alert')[$key]}} " >
												{!! Form::radio('state', $stt_id,true,array('onchange' => 'javascript:hom_solicitud.changeRadio(this)')) !!}	
												<span class="">{{Session::get('modulo.states.state')[$key]}} -- <i>Estado actual</i> </span>															
											</div>																													
										@else
											<!-- Pintamos solo los estados proximos -->
											@if($stt_id >= Session::get('modulo.state'))
												<div class="col-md-12 col-md-offset-0 " style="background-color: {{Session::get('modulo.states.alert')[$key]}} ">
													{!! Form::radio('state', $stt_id,false,array('onchange' => 'javascript:hom_solicitud.changeRadio(this)')) !!}	
													<span class="">{{Session::get('modulo.states.state')[$key]}}</span>															
												</div>
											@endif
										@endif
																				
									@endforeach
								@endif
								</div>
							</div>
						@else
							<div class="form-group">
								{!! Form::label('label', 'Atención Inmediata', array('class' => 'col-md-4 control-label')) !!}
								<div class="col-md-12">
									<div class="col-md-12 col-md-offset-0 ">
										{!! Form::radio('state', 1,true) !!}	
										<span class="glyphicon glyphicon-earphone"> Procesar llamada</span>															
									</div>
									<div class="col-md-12 col-md-offset-0 ">
										{!! Form::radio('state', 2) !!}	
										<span class="glyphicon glyphicon-send"> Devolver llamada</span>															
									</div>
								</div>
							</div>
						@endif
						</div>
						<div class="form-group">
							
							<div class="col-md-6">
								@if(Session::get('_old_input.solicitud_id'))
									{!! Form::label('orden_service', 'Orden de Servicio', array('class' => 'col-md-12 control-label')) !!}								
									@if(Session::get('_old_input.orden_service'))
										{!! Form::text('orden_servicio', old('orden_service'), array('class' => 'form-control','placeholder'=>'Ingresa la orden de servicio', 'disabled' => 'disabled'))!!}
										{!! Form::hidden('orden_service', old('orden_service'), array('class' => 'form-control'))!!}
									@else
										{!! Form::text('orden_service', old('orden_service'), array('class' => 'form-control','placeholder'=>'Ingresa la orden de servicio'))!!}
									@endif
								@endif
							</div>
							<div class="col-md-6">
								@if(Session::get('_old_input.solicitud_id'))
									{!! Form::label('date_service', 'Fecha de Orden de Servicio', array('class' => 'col-md-12 control-label')) !!}
								
									@if(Session::get('_old_input.date_service'))
										{!! Form::text('fecha_servicio', old('date_service'), array('class' => 'form-control date_service','placeholder'=>'Ingresa la fecha de la orden de servicio', 'disabled' => 'disabled'))!!}
										{!! Form::hidden('date_service', old('date_service'), array('class' => 'form-control date_service'))!!}
									@else
										{!! Form::text('date_service', old('date_service'), array('class' => 'form-control date_service','placeholder'=>'Ingresa la fecha de la orden de servicio'))!!}
									@endif
								@endif
							</div>
						</div>
							
						<div class="form-group">
							{!! Form::label('descripcion', 'Observación', array('class' => 'col-md-12 control-label')) !!}
							<div class="col-md-12">
								@if(Session::get('_old_input.solicitud_id'))
									@if(Session::get('_old_input.description'))
										{!! Form::textarea('descripcion', old('description'), array('class' => 'form-control desc_txt','placeholder'=>'Ingresa las observaciones', 'disabled' => 'disabled'))!!}
										{!! Form::hidden('description', old('description'), array('class' => 'form-control'))!!}
									@else
										{!! Form::textarea('description', old('description'), array('class' => 'form-control desc_txt','placeholder'=>'Ingresa las observaciones'))!!}
									@endif									
								@else
									{!! Form::textarea('description', old('description'), array('class' => 'form-control desc_txt','placeholder'=>'Ingresa las observaciones'))!!}
								@endif
							</div>
						</div>	
						
					</div>
					</br>
					<div class="form-group">
						<div class="col-md-3 col-md-offset-0 ">
							{!! Form::submit('Enviar', array('class' => 'btn btn-primary')) !!}																
						</div>
					</div>
					
					
					{!! Form::hidden('edit', old('edit')) !!}
					{!! Form::hidden('solicitud_id', old('solicitud_id')) !!}
					
					{!! Form::hidden('mod_id', Session::get('modulo.id')) !!}
					{!! Form::hidden('app_id', Session::get('modulo.id_app')) !!}
					{!! Form::hidden('state_old', Session::get('modulo.state')) !!}
									
				{!! Form::close() !!}	
								
							
				</div>
			</div>		
		</div>
	</div>
	<!-- Form en blanco para consultar titulares-->
    {!! Form::open(array('id'=>'form_consult_headline','url' => 'solicitud/consultartitular')) !!}
    {!! Form::close() !!}	
    <!-- Form en blanco para consultar Homage -->
    {!! Form::open(array('id'=>'form_consult_homage','url' => 'solicitud/consultarhomage')) !!}
    {!! Form::close() !!}	
</div>	 
@endsection

@section('script')
	<script type="text/javascript" src="{{ asset('/js/lib/datetimepiker.js') }}"></script>		
	<script type="text/javascript">	
		//llamado del metodo consultar asincrono
		$('.id_headline').focusout(function() {		    
		   if($('.id_headline').val().length > 4){
			   if($.isNumeric($('.id_headline').val())){
				   var datos = new Array();
				   datos['id'] = $('.id_headline').val();				   
				   seg_ajaxobject.peticionajax($('#form_consult_headline').attr('action'),datos,"hom_solicitud.consultaRespuestaTitular");
			   }else{
				   $('.alerts').html('<div class="alert alert-warning fade in"><strong>¡Identificación invalida!</strong>La identificación no debe contener caracteres especiales!!!.<br><br><ul><li>Intenta nuevamente con una identificación numerica</li></ul></div>');
			   }			   			   
		   }else{
			   $('.alerts').html('<div class="alert alert-warning fade in"><strong>¡Identificación invalida!</strong>La identificación no debe ser de menos de 5 caracteres!!!.<br><br><ul><li>Intenta nuevamente con una identificación numerica</li></ul></div>');
		   }
		  
	  	});

		$('.id_homage').focusout(function() {
			if($('.id_homage').val().length > 4){
				 if($.isNumeric($('.id_homage').val())){
					 var datos = new Array();
					 datos['id'] = $('.id_homage').val();				   
					 seg_ajaxobject.peticionajax($('#form_consult_homage').attr('action'),datos,"hom_solicitud.consultaRespuestaHomage");
				 }else{
					 $('.alerts').html('<div class="alert alert-warning fade in"><strong>¡Identificación invalida!</strong>La identificación no debe contener caracteres especiales!!!.<br><br><ul><li>Intenta nuevamente con una identificación numerica</li></ul></div>');
				 }
			}else{
				$('.alerts').html('<div class="alert alert-warning fade in"><strong>¡Identificación invalida!</strong>La identificación no debe ser de menos de 5 caracteres!!!.<br><br><ul><li>Intenta nuevamente con una identificación numerica</li></ul></div>');
			}			
		});

	  	
	  	//autocomplete con los datos iniciales
		$( "#company" ).autocomplete({
		      source: hom_solicitud.datos_company
	    });
	    
		hom_solicitud.datos_company= [];
		hom_solicitud.state = $('#description_state').data("field-id");
		hom_solicitud.state_id = $('#id_state').data("field-id");

		//datepiker
		$('.date_service').datetimepicker({
			dateFormat: "yy-mm-dd",
			timeFormat: "hh:mm:ss",
			
		});	
		//javascript:seg_user.iniciarDatepiker('birthdate');
	</script>
@endsection