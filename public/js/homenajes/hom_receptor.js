function hom_receptor() {
	this.datos_pie = [];
	this.table = '';
	
}

hom_receptor.prototype.onjquery = function() {
};

hom_receptor.prototype.opt_select = function(controlador,metodo) {
	
	if(hom_receptor.table.rows('.selected').data().length){		
		window.location=metodo + "/" + hom_receptor.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};
hom_receptor.prototype.lugarRespuesta = function(result) {
	if(result.respuesta){
		if(result.opp == '0'){
			hom_receptor.table.rows('.selectd').remove().draw(false);
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Reciclaje de estado!</strong>El estado ha sido reciclado.<br><br><ul><li>'+result.data+'</li></ul></div>');
			
		}
		if(result.opp =='1'){		
			hom_receptor.table.rows('.selected').remove().draw(false);
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Restauración de estado!</strong>El estado ha sido restaurado.<br><br><ul><li>'+result.data+'</li></ul></div>');
		}
	}else{
		$('.alerts').html('<div class="alert alert-danger fade in"><strong>¡Reciclaje de Estado!</strong>El estado no fue reciclada.<br><br><ul><li>'+result.data+'</li></ul></div>');
	}
	
	
}
	

var hom_receptor = new hom_receptor();
