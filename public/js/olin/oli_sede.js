function oli_sede() {
	this.datos_pie = [];
	this.table = '';
	
}

oli_sede.prototype.onjquery = function() {
};

oli_sede.prototype.opt_select = function(controlador,metodo) {
	
	if(oli_sede.table.rows('.selected').data().length){		
		window.location=metodo + "/" + oli_sede.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

oli_sede.prototype.opt_ver = function() {
	
	if(oli_sede.table.rows('.selected').data().length){
		var datos = new Array();
  		datos['id'] = oli_sede.table.rows('.selected').data()[0].id;	  	  		
  		seg_ajaxobject.peticionajax($('#form_ver').attr('action'),datos,"oli_sede.optVerRespuesta");
  		
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}	
};	

oli_sede.prototype.optVerRespuesta = function(result) {	
	
	$("#sede_modal .modal-body .row_izq").html('<div class="col-md-2" >Sede: </div><div class="col-md-10" >'+oli_sede.table.rows('.selected').data()[0].seat+'</div>');
	$("#sede_modal .modal-body .row_izq").html($("#sede_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Teléfono: </div><div class="col-md-10" >'+oli_sede.table.rows('.selected').data()[0].phone+'</div>');
	$("#sede_modal .modal-body .row_izq").html($("#sede_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Descripción: </div><div class="col-md-10" >'+oli_sede.table.rows('.selected').data()[0].description+'</div>');
	
	//usuarios
	if(result.respuesta){
		$("#sede_modal .modal-body .row_izq").html($("#sede_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><hr size="1"/></div>');
		$("#sede_modal .modal-body .row_izq").html($("#sede_modal .modal-body .row_izq").html()+'<div class="col-md-12"><b>Usuarios</b></div>');
				
		for(var i = 0; i < result.data.length; i++){		
			$("#sede_modal .modal-body .row_izq").html($("#sede_modal .modal-body .row_izq").html()+'<div class="col-md-12"><span class="glyphicon glyphicon-link"></span> '+result.data[i].name+' - '+result.data[i].names+' '+result.data[i].surnames+'</div>');
		}	
	}
	
	
	$("#sede_modal").modal();
};

oli_sede.prototype.lugarRespuesta = function(result) {
	
	if(result.respuesta){
		if(result.opp == '0'){
			/*Reciclar*/
			oli_sede.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Reciclaje de sede!</strong>La sede ha sido reciclada.<br><br><ul><li>'+result.data+'</li></ul></div>');
		}
		if(result.opp == '1'){
			/*Restaurar*/
			oli_sede.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Restauración de sede!</strong>La sede ha sido restaurada.<br><br><ul><li>'+result.data+'</li></ul></div>');
		}

		
	}else{
		$('.alerts').html('<div class="alert alert-danger fade in"><strong>¡Reciclaje de sede!</strong>La sede no fue reciclada.<br><br><ul><li>'+result.data+'</li></ul></div>');
	}
};
	

var oli_sede = new oli_sede();
