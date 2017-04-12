function seg_opcion() {
	this.datos_pila = [];
	this.datos_categoria = [];
	this.aux_datos = [];		
	this.table = '';
	
}

seg_opcion.prototype.onjquery = function() {
};

seg_opcion.prototype.opt_ver = function() {
	if(seg_opcion.table.rows('.selected').data().length){
		var preferences = jQuery.parseJSON(seg_opcion.table.rows('.selected').data()[0].preference);
		$("#option_modal .modal-body .row_izq").html('<div class="col-md-2" >Opción: </div><div class="col-md-10" >'+seg_opcion.table.rows('.selected').data()[0].option+'</div>');
		$("#option_modal .modal-body .row_izq").html($("#option_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Acción: </div><div class="col-md-10" >'+seg_opcion.table.rows('.selected').data()[0].action+'</div>');
		$("#option_modal .modal-body .row_izq").html($("#option_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Lugar: </div><div class="col-md-10" >'+preferences.lugar+'</div>');
		$("#option_modal .modal-body .row_izq").html($("#option_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Vista: </div><div class="col-md-10" >'+preferences.vista+'</div>');
		$("#option_modal .modal-body .row_izq").html($("#option_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Icono: </div><div class="col-md-10" ><span class="'+preferences.icono+'" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>'+preferences.icono+'</div>');
		
		
		$("#option_modal").modal();
  	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};	

seg_opcion.prototype.opt_select = function(controlador,metodo) {
	
	if(seg_opcion.table.rows('.selected').data().length){		
		window.location=metodo + "/" + seg_opcion.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};
	

var seg_opcion = new seg_opcion();
