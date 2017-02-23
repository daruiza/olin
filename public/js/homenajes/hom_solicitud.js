function hom_solicitud() {
	this.datos_pie = [];
	this.datos_pie_servicios = [];
	this.colores_pie = [];
	this.datos_company = [];//para autocomplete
	this.datos_categoria = [];
	this.datos_multibar = [];
	this.aux_datos = [];
	this.state = '';
	this.state_id = '';	
	this.table = '';
	
}

hom_solicitud.prototype.onjquery = function() {
};

hom_solicitud.prototype.opt_select = function(controlador,metodo) {
	
	if(hom_solicitud.table.rows('.selected').data().length){		
		window.location=metodo + "/" + hom_solicitud.table.rows('.selected').data()[0]['request_id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

hom_solicitud.prototype.opt_ver = function(controlador,metodo) {
	if(hom_solicitud.table.rows('.selected').data().length){
		var datos = new Array();
  		datos['id'] = hom_solicitud.table.rows('.selected').data()[0].request_id;	  	  		
  		seg_ajaxobject.peticionajax($('#form_ver').attr('action'),datos,"hom_solicitud.optVerRespuesta");
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}	
};

hom_solicitud.prototype.optVerRespuesta = function(result) {
	//verificamos la respuesta
	if(result.respuesta){
		//verificamos la consulta
		if(result.data.length){	
			$("#request_modal .modal-body .row_izq").html('<div class="col-md-6" >Solicitud: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].request_id+'</div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Estado: </div><div class="col-md-6" style="background-color:'+hom_solicitud.table.rows('.selected').data()[0].alert+' ;">'+hom_solicitud.table.rows('.selected').data()[0].state+'</div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Fecha: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].date+'</div>');
			
			if (hom_solicitud.table.rows('.selected').data()[0].description_state != "") {
				$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Observación: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].description_state+'</div>');
			}
			
			if (hom_solicitud.table.rows('.selected').data()[0].orden_service != "") {
				$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Orden de Servicio: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].orden_service+'</div>');				
			}
			if (hom_solicitud.table.rows('.selected').data()[0].date_service != '0000-00-00 00:00:00') {
				$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Fecha de Orden de Servicio: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].date_service+'</div>');
			}		
			
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><hr size="0"/></div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12"><b>Contacto</b></div>');

			if (hom_solicitud.table.rows('.selected').data()[0].name != "") {
				$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Nombre: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].name+'</div>');
			}
			if (hom_solicitud.table.rows('.selected').data()[0].fhone != "") {
				$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Teléfono: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].fhone+'</div>');				
			}
			if (hom_solicitud.table.rows('.selected').data()[0].cellfhone != "") {
				$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Celular: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].cellfhone+'</div>');
			}			
			
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><hr size="0"/></div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12"><b>Titular</b></div>');
			
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Nombre: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].name_headline+'</div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Identificación: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].identification_headline+'</div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Entidad: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].seat+'</div>');
			
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><hr size="0"/></div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12"><b>Homenaje</b></div>');
			
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Nombre: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].name_homage+'</div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Identificación: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].identification_homage+'</div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Ubicación: </div><div class="col-md-6" >'+hom_solicitud.table.rows('.selected').data()[0].location_homage+'</div>');
			
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><hr size="0"/></div>');
			$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12"><b>Trazada</b></div>');
			
			for(var i = 0; i < result.data.length; i++){
				
				$("#request_modal .modal-body .row_izq").html($("#request_modal .modal-body .row_izq").html()+'<div class="col-md-12" style="background-color:'+result.data[i].alert+'" > <div class="col-md-4" >'+result.data[i].state+'</div><div class="col-md-4" >'+result.data[i].description_state+'</div><div class="col-md-4" >'+result.data[i].date+'</div></div>');
				
			}
			hom_solicitud.table.$('tr.selected').removeClass('selected');
			$("#request_modal").modal();			
			
		}else{
			$('.alerts').html('<div class="alert alert-danger fade in"><strong>¡Los datos no se consultarón!</strong>El servidor no pudo responder la solicitud!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
		}
		
	}else{
		$('.alerts').html('<div class="alert alert-danger fade in"><strong>¡Los datos no se consultarón!</strong>El servidor no pudo responder la solicitud!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

hom_solicitud.prototype.consultaRespuestaTitular = function(result) {
	//verificamos la respuesta
	if(result.respuesta){
		//verificamos la consulta
		if(result.data.length){
			if(result.data[0].length){
				
				var names = [];
				for (i = 0; i < result.data[0].length; i++) {					
					//para no repetir entidades en el array
					if($.inArray(result.data[0][i].company,hom_solicitud.datos_company) < 0){
						hom_solicitud.datos_company.push(result.data[0][i].company);
						names.push(result.data[0][i].name + ' - ' + result.data[0][i].company);	
					}					
				}
				//autocomplete
				$( "#company" ).autocomplete({
				      source: hom_solicitud.datos_company
			    });
				
				$( "#name_headline" ).autocomplete({
				      source: names
			    });
			    
				hom_solicitud.datos_company = [];
				
				//input nombre				
				//$('#nombre_titular').val(result.data[0][0].name);
				//$('#name_headline').val(result.data[0][0].name);
			}			
		}else{
			//no hay datos.
			//se deberia informar que el afiliado no esta en el OLIN
		}				
	}else{
		
	}
		
}

hom_solicitud.prototype.consultaRespuestaHomage = function(result) {
	//verificamos la respuesta
	if(result.respuesta){
		//verificamos la consulta
		if(result.data.length){
			if(result.data[0].length){
				
				var names = [];
				for (i = 0; i < result.data[0].length; i++) {
					if($.inArray(result.data[0][i].company,hom_solicitud.datos_company) < 0){
						names.push(result.data[0][i].name + ' - ' + result.data[0][i].company);
					}
				}
				//autocomplete
				$( "#homage_name" ).autocomplete({
				      source: names
			    });				
			}			
		}else{
			//no hay datos.
			//se deberia informar que el afiliado no esta en el OLIN
		}				
	}else{
		
	}
}

hom_solicitud.prototype.changeRadio = function(obj) {
	if(hom_solicitud.state_id == parseInt(obj.value)){
		$(".desc_txt").prop("disabled", true);
		$(".desc_txt").text(hom_solicitud.state);
	}else{
		$(".desc_txt").prop("disabled", false);
		$(".desc_txt").text('');
	}		
	
}

hom_solicitud.prototype.copiarMessage = function(obj) {
	var aux =document.createElement("input");
	// Asigna el contenido del elemento especificado al valor del campo
	
	str = "";
	for(var i=0;i< $('.alert-info ul').children().length ; i++){
		str = str + $('.alert-info ul').children()[i].innerHTML +  "\n";
	}
	
	//aux.setAttribute("value", $('.alert-info').html());
	aux.setAttribute("value", str);
	//falta refinar el contenido
	document.body.appendChild(aux);
	aux.select();
	document.execCommand("copy");
	//falta emitir toggle de comiado OK
	document.body.removeChild(aux);
	obj.innerHTML = 'Mensaje Copiado!';
}

var hom_solicitud = new hom_solicitud();
