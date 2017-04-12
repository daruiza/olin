function oli_log() {
	this.datos_pie = [];
	this.table = '';
	
}

oli_log.prototype.onjquery = function() {
};

oli_log.prototype.opt_select = function(controlador,metodo) {
	
	if(oli_log.table.rows('.selected').data().length){		
		window.location=metodo + "/" + oli_log.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};
	

var oli_log = new oli_log();
