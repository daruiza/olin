function oli_vinculo() {
	this.datos_pie = [];
	this.table = '';
	
}

oli_vinculo.prototype.onjquery = function() {
};

oli_vinculo.prototype.opt_ver = function() {
	if(oli_vinculo.table.rows('.selected').data().length){		
		$("#link_modal .modal-body .row_izq").html('<div class="col-md-2" >Vinculo: </div><div class="col-md-10" >'+oli_vinculo.table.rows('.selected').data()[0].link+'</div>');		
		$("#link_modal .modal-body .row_izq").html($("#sede_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Descripción: </div><div class="col-md-10" >'+oli_vinculo.table.rows('.selected').data()[0].description+'</div>');
			
		$("#link_modal").modal();
		
  	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};	

oli_vinculo.prototype.opt_select = function(controlador,metodo) {
	
	if(oli_vinculo.table.rows('.selected').data().length){		
		window.location=metodo + "/" + oli_vinculo.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

oli_vinculo.prototype.lugarRespuesta = function(result) {
	
	if(result.respuesta){
		if(result.opp == '0'){
			/*Reciclar*/
			oli_vinculo.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Reciclaje de sede!</strong>La sede ha sido reciclada.<br><br><ul><li>'+result.data+'</li></ul></div>');
		}
		if(result.opp == '1'){
			/*Restaurar*/
			oli_vinculo.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Restauración de sede!</strong>La sede ha sido restaurada.<br><br><ul><li>'+result.data+'</li></ul></div>');
		}

		
	}else{
		$('.alerts').html('<div class="alert alert-danger fade in"><strong>¡Reciclaje de sede!</strong>La sede no fue reciclada.<br><br><ul><li>'+result.data+'</li></ul></div>');
	}
};

var oli_vinculo = new oli_vinculo();
