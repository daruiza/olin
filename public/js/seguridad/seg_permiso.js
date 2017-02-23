function seg_permiso() {
	this.datos_pie = [];
	this.datos_pila = [];
	this.datos_categoria = [];
	this.aux_datos = [];
	this.table = '';
	
}

seg_permiso.prototype.onjquery = function() {
};

seg_permiso.prototype.opt_ver = function() {
	if(seg_permiso.table.rows('.selected').data().length){		
		$("#permiso_modal .modal-body .row_izq").html('<div class="col-md-2" >Rol: </div><div class="col-md-10" >'+seg_permiso.table.rows('.selected').data()[0].rol+'</div>');
		$("#permiso_modal .modal-body .row_izq").html($("#permiso_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Modulo: </div><div class="col-md-10" >'+seg_permiso.table.rows('.selected').data()[0].module+'</div>');
		$("#permiso_modal .modal-body .row_izq").html($("#permiso_modal .modal-body .row_izq").html()+'<div class="col-md-2" >Opción: </div><div class="col-md-10" >'+seg_permiso.table.rows('.selected').data()[0].option+'</div>');
		$("#permiso_modal").modal();
  	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};	

seg_permiso.prototype.opt_select = function(controlador,metodo) {
	
	if(seg_permiso.table.rows('.selected').data().length){		
		window.location=metodo + "/" + seg_permiso.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

//Metodo para recibir permiso borrado
seg_permiso.prototype.lugarRespuesta = function(result) {
	
	if(result.respuesta){
		seg_permiso.table.rows('.selected').remove().draw( false );
		$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Eliminación de permiso!</strong>Para recuperar el pemiso, comuniquese cone l administrador.<br><br><ul><li>Solo el administrador puede acceder permisos</li></ul></div>');
	}else{
		$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Eliminación de permiso!</strong>El permiso fue borrado previamente.<br><br><ul><li>Solo el administrador puede acceder permisos</li></ul></div>');
	}
};

var seg_permiso = new seg_permiso();
