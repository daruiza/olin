function seg_modulo() {
	this.datos_pie = [];
	this.datos_categoria = [];
	this.datos_multibar = [];
	this.aux_datos = [];
	this.datos_categoria = [];
	this.table = '';
	
}	
	
seg_modulo.prototype.onjquery = function() {
};

seg_modulo.prototype.opt_ver = function() {
	if(seg_modulo.table.rows('.selected').data().length){
		
		$("#module_modal .modal-body .row_izq").html('<div class="col-md-2" >Modulo: </div><div class="col-md-10" >'+seg_modulo.table.rows('.selected').data()[0].module+'</div>');
		$("#module_modal .modal-body .row_izq").html($("#module_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Prefrerencias: </div><div class="col-md-10" >'+seg_modulo.table.rows('.selected').data()[0].preference+'</div>');
		$("#module_modal .modal-body .row_izq").html($("#module_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Descripción: </div><div class="col-md-10" >'+seg_modulo.table.rows('.selected').data()[0].description+'</div>');
		$("#module_modal .modal-body .row_izq").html($("#module_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Aplicación: </div><div class="col-md-10" >'+seg_modulo.table.rows('.selected').data()[0].app+'</div>');
		
		$("#module_modal").modal();
  	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};	

seg_modulo.prototype.opt_select = function(controlador,metodo) {	
	if(seg_modulo.table.rows('.selected').data().length){		
		window.location=metodo + "/" + seg_modulo.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};
seg_modulo.prototype.optVerRespuesta = function(result) {
	alert('llega respuesta');
};

var seg_modulo = new seg_modulo();
