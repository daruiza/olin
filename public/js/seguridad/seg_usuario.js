function seg_usuario() {
	this.datos_pie = [];
	this.datos_fecha = [[],[],[]];
	this.table = '';
}

seg_usuario.prototype.onjquery = function() {
	
};

seg_usuario.prototype.opt_select = function(controlador,metodo) {
	
	if(seg_usuario.table.rows('.selected').data().length){		
		window.location=metodo + "/" + seg_usuario.table.rows('.selected').data()[0]['user_id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego pruebanuevamente la opción</li></ul></div>');
	}
};

seg_usuario.prototype.opt_ver = function() {
	if(seg_usuario.table.rows('.selected').data().length){
		var datos = new Array();
  		datos['id'] = seg_usuario.table.rows('.selected').data()[0].user_id;	  	  		
  		seg_ajaxobject.peticionajax($('#form_ver').attr('action'),datos,"seg_usuario.optVerRespuesta");
  		
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}	
};

seg_usuario.prototype.optVerRespuesta = function(result) {	
		
	var fechaActual = new Date()
	var diaActual = fechaActual.getDate();
	var mmActual = fechaActual.getMonth() + 1;
	var yyyyActual = fechaActual.getFullYear();
	FechaNac = seg_usuario.table.rows('.selected').data()[0].birthdate.split("-");
	var yyyyCumple = FechaNac[0];
	var mmCumple = FechaNac[1];
	var diaCumple = FechaNac[2];
		            		
	//retiramos el primer cero de la izquierda
	if (mmCumple.substr(0,1) == 0) {
	mmCumple= mmCumple.substring(1, 2);
	}
	//retiramos el primer cero de la izquierda
	if (diaCumple.substr(0, 1) == 0) {
	diaCumple = diaCumple.substring(1, 2);
	}
	var edad = yyyyActual - yyyyCumple;
	//validamos si el mes de cumpleaños es menor al actual
	//o si el mes de cumpleaños es igual al actual
	//y el dia actual es menor al del nacimiento
	//De ser asi, se resta un año
	if ((mmActual < mmCumple) || (mmActual == mmCumple && diaActual < diaCumple)) {
	edad--;
	}
			
	$("#user_modal .modal-body .row_izq").html('<div class="col-md-6" >Nombres: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].names+'</div>');
	// $("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Apellidos: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].surnames+'</div>');
	$("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Identificación: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].identificacion+'</div>');				
	$("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Email: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].email+'</div>');
	$("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Dirección: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].adress+'</div>');
	$("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Movil: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].movil_number+'</div>');
	// $("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Fijo: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].fix_number+'</div>');
	// $("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Fecha Nacimiento: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].birthdate+'</div>');
	// $("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Edad: </div><div class="col-md-6" >'+edad+'</div>');
	// $("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Genero: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].sex+'</div>');
	// $("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Descripcion: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].description+'</div>');
	// $("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Usuario: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].name+'</div>');
	// $("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6" >Rol: </div><div class="col-md-6" >'+seg_usuario.table.rows('.selected').data()[0].rol+'</div>');
	
	//aplicaciones
	if(result.respuesta){
		$("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-12" ><hr size="1"/></div>');
		$("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-12"><b>Aplicaciones</b></div>');
		
		var preferencias;
		var estado;
		for(var i = 0; i < result.data.length; i++){
			preferencias = JSON.parse(result.data[i].preferences);
			estado = "Inactivo";
			if(result.data[i].active) estado = "Activo";
			$("#user_modal .modal-body .row_izq").html($("#user_modal .modal-body .row_izq").html()+'<div class="col-md-6"><span class="'+preferencias.icono+'"></span> '+result.data[i].app+'</div><div class="col-md-6">'+estado+'</div>');
		}		
	}	
	
	$("#user_modal .modal-body .row_der").html('<div class="col-md-12" ><img id="nueva_img" src="http://opaplus.local/images/user/'+seg_usuario.table.rows('.selected').data()[0].avatar+'" class="" alt="Imagen no disponible" style="width: 100%; border:2px solid #78a5b1;border-radius: 3px;"></div>');
	seg_usuario.table.$('tr.selected').removeClass('selected');
	$("#user_modal").modal();	
	
};

//Metodo que recibe la respuesta del cambio de estado
seg_usuario.prototype.lugarRespuesta = function(result) {
	if(result.data.opp == '1'){
		/*Restaurar
		 * */
		if(result.data.respuesta){
			//seg_usuario.table.rows('.selected').removeClass('selected');
			seg_usuario.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Recuperación de usuario!</strong>El usuario de ha recuperado adecuadamente.<br><br><ul><li>Ahora el usuario se halla en al lista del escritorio</li></ul></div>');
		}else{
			$('.alerts').html('<div class="alert alert-error fade in"><strong>¡La aplicación ha experiemtnedo un rror!</strong>Comuniquese con el administrador.<br><br><ul><li>Ahora para recuperarlo debera cambiar la perspectivade de escritorio a papelera</li></ul></div>');
		}		
	}else if( result.data.opp == '0'){
		/*Botar
		 * */
		if(result.data.respuesta){
			//seg_usuario.table.rows('.selected').removeClass('selected');
			seg_usuario.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Reciclage de usuario!</strong>El usuario de ha reciclado adecuadamente.<br><br><ul><li>Ahora para recuperarlo debera cambiar la perspectivade de escritorio a papelera</li></ul></div>');			
		}else{
			$('.alerts').html('<div class="alert alert-error fade in"><strong>¡La aplicación ha experiemtnedo un rror!</strong>Comuniquese con el administrador.<br><br><ul><li>Ahora para recuperarlo debera cambiar la perspectivade de escritorio a papelera</li></ul></div>');
		}
		
	}else{
		/*eliminar
		 * */
		if(result.data.respuesta){
			//seg_usuario.table.rows('.selected').removeClass('selected');
			seg_usuario.table.rows('.selected').remove().draw( false );
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Eliminación de usuario!</strong>Para recuperar el usuario, comuniquese cone l administrador.<br><br><ul><li>Ahora solo puede recuperar el usuario a travez de petición SQL por medio del administrador</li></ul></div>');			
		}else{
			$('.alerts').html('<div class="alert alert-error fade in"><strong>¡La aplicación ha experiemtnedo un rror!</strong>Comuniquese con el administrador.<br><br><ul><li>Ahora para recuperarlo debera cambiar la perspectivade de escritorio a papelera</li></ul></div>');
		}
	}
	
};

var seg_usuario = new seg_usuario();
