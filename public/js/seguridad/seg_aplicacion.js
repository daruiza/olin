function seg_aplicacion() {
	this.datos_pie = [];
	this.datos_multibar = [];
	this.table = '';
};	
seg_aplicacion.prototype.onjquery = function() {
};

seg_aplicacion.prototype.opt_select = function(controlador,metodo) {
	
	if(seg_aplicacion.table.rows('.selected').data().length){		
		window.location=metodo + "/" + seg_aplicacion.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

seg_aplicacion.prototype.verRespuesta = function(result) {
	//primero los datos de la aplicación
	$("#app_modal .modal-body .row_izq").html('<div class="col-md-4" >Aplicación: </div><div class="col-md-8" >'+seg_aplicacion.table.rows('.selected').data()[0].app+'</div>');
	$("#app_modal .modal-body .row_izq").html($("#app_modal .modal-body .row_izq").html()+'<div class="col-md-4" >Descripción: </div><div class="col-md-8" >'+seg_aplicacion.table.rows('.selected').data()[0].description+'</div>');
	$("#app_modal .modal-body .row_izq").html($("#app_modal .modal-body .row_izq").html()+'<div class="col-md-4" >Preferencias: </div><div class="col-md-8" >'+seg_aplicacion.table.rows('.selected').data()[0].preferences+'</div>');
	
	
	//luego los datos de los modulos con result
	if(result.respuesta){
		$("#app_modal .modal-body .row_izq").html($("#app_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><hr size="1"/></div>');
		$("#app_modal .modal-body .row_izq").html($("#app_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><b>Modulos</b></div>');
		
		var preferencias;
		for(var i = 0; i < result.data.length; i++){
			preferencias = JSON.parse(result.data[i].preference);
			$("#app_modal .modal-body .row_izq").html($("#app_modal .modal-body .row_izq").html()+'<div class="col-md-4"><p><span class="'+preferencias.icono+'"></span> '+result.data[i].module+'</p></div><div class="col-md-8" style = "border-bottom: 1px solid black;">'+result.data[i].description+'</div>');
		}
	}	
	
	$("#app_modal").modal();
};

var seg_aplicacion = new seg_aplicacion();
