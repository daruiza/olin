function seg_rol() {
	this.datos_pie = [];
	this.table = '';
	
}

seg_rol.prototype.onjquery = function() {
};

seg_rol.prototype.opt_select = function(controlador,metodo) {
	
	if(seg_rol.table.rows('.selected').data().length){		
		window.location=metodo + "/" + seg_rol.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};
	

var seg_rol = new seg_rol();
