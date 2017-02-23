function oli_empresa() {
	this.datos_pie = [];
	this.table = '';
	
}

oli_empresa.prototype.onjquery = function() {
};

oli_empresa.prototype.opt_select = function(controlador,metodo) {
	
	if(oli_empresa.table.rows('.selected').data().length){		
		window.location=metodo + "/" + oli_empresa.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

oli_empresa.prototype.opt_ver = function() {
	if(oli_empresa.table.rows('.selected').data().length){
		var datos = new Array();
  		datos['id'] = oli_empresa.table.rows('.selected').data()[0].id;	  	  		
  		seg_ajaxobject.peticionajax($('#form_ver').attr('action'),datos,"oli_empresa.optVerRespuesta");
  		
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
	
};
oli_empresa.prototype.optVerRespuesta = function(result) {	
	
	$("#company_modal .modal-body .row_izq").html('<div class="col-md-6" >Empresa: </div><div class="col-md-6" >'+oli_empresa.table.rows('.selected').data()[0].company+'</div>');
	$("#company_modal .modal-body .row_izq").html($("#company_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Descripción: </div><div class="col-md-6" >'+oli_empresa.table.rows('.selected').data()[0].description+'</div>');
	$("#company_modal .modal-body .row_izq").html($("#company_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Sede: </div><div class="col-md-6" >'+oli_empresa.table.rows('.selected').data()[0].seat+'</div>');
	$("#company_modal .modal-body .row_izq").html($("#company_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Teléfono sede: </div><div class="col-md-6" >'+oli_empresa.table.rows('.selected').data()[0].phone+'</div>');
	
	//vinculos
	if(result.respuesta){
		$("#company_modal .modal-body .row_izq").html($("#company_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><hr size="1"/></div>');
		$("#company_modal .modal-body .row_izq").html($("#company_modal .modal-body .row_izq").html()+'<div class="col-md-12"><b>Vinculos</b></div>');
				
		var estado;
		for(var i = 0; i < result.data.length; i++){		
			$("#company_modal .modal-body .row_izq").html($("#company_modal .modal-body .row_izq").html()+'<div class="col-md-8"><span class="glyphicon glyphicon-link"></span> '+result.data[i].link+'</div><div class="col-md-4">'+result.data[i].description+'</div>');
		}		
	}	
	
	/*oli_empresa.table.$('tr.selected').removeClass('selected');*/
	$("#company_modal").modal();
};

oli_empresa.prototype.lugarRespuesta = function(result) {
	
	if(result.respuesta){
		if(result.opp == '0'){
			/*Reciclar*/
			oli_empresa.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Reciclaje de empresa!</strong>La empresa ha sido reciclada.<br><br><ul><li>'+result.data+'</li></ul></div>');
		}
		if(result.opp == '1'){
			/*Restaurar*/
			oli_empresa.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Restauración de empresa!</strong>La empresa ha sido restaurada.<br><br><ul><li>'+result.data+'</li></ul></div>');
		}

		
	}else{
		$('.alerts').html('<div class="alert alert-danger fade in"><strong>¡Reciclaje de sede!</strong>La sede no fue reciclada.<br><br><ul><li>'+result.data+'</li></ul></div>');
	}
};
	

var oli_empresa = new oli_empresa();
