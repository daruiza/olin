function oli_afiliado() {
	this.datos_pie = [];
	this.table = '';
	
}

oli_afiliado.prototype.onjquery = function() {
};

oli_afiliado.prototype.opt_select = function(controlador,metodo) {
	
	if(oli_afiliado.table.rows('.selected').data().length){		
		window.location=metodo + "/" + oli_afiliado.table.rows('.selected').data()[0]['id'];
	}else{
		$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	}
};

oli_afiliado.prototype.changeSelect = function(object) {
	
	var datos = new Array();
	datos['id'] = object.options[object.selectedIndex].value;
	datos['select'] = object.id;
	seg_ajaxobject.peticionajax($('#form_select').attr('action'),datos,"oli_afiliado.selectRespuesta");
	
};

oli_afiliado.prototype.selectRespuesta = function(result) {
	if(result.respuesta){
		if(result.data != null){
			if(result.select == 'id_seat'){
				/*borramos los datos anteriores*/
				select = document.getElementById('id_company');				
			    select.innerHTML='<option value="" selected="selected">Ingresa la empresa</option>';
			    
			    select = document.getElementById('id_link');				
			    select.innerHTML='<option value="" selected="selected">Ingresa el vinculo</option>';
				
				select = document.getElementById('id_company');
				for(var i=0;i<result.data.length;i++){
					var opt = document.createElement('option');
				    opt.value = result.data[i].id;
				    opt.text = result.data[i].company;
				    select.appendChild(opt);
				}	
			}
			if(result.select == 'id_company'){
				/*borramos los atos anteriores*/
				select = document.getElementById('id_link');				
				select.innerHTML='<option value="" selected="selected">Ingresa el vinculo</option>';
				
				select = document.getElementById('id_link');
				for(var i=0;i<result.data.length;i++){
					var opt = document.createElement('option');
				    opt.value = result.data[i].id;
				    opt.text = result.data[i].link;
				    select.appendChild(opt);
				}	
			}
		}
		else{
			/*data null*/
			if(result.select == 'id_seat'){
				/*borramos todos los option de company y de links*/
				select = document.getElementById('id_company');				
			    select.innerHTML='<option value="" selected="selected">Ingresa la empresa</option>';
			    
			    select = document.getElementById('id_link');				
			    select.innerHTML='<option value="" selected="selected">Ingresa el vinculo</option>';
			}
			if(result.select == 'id_company'){
				/*borramos todos los option de links*/		    
			    select = document.getElementById('id_link');				
			    select.innerHTML='<option value="" selected="selected">Ingresa el vinculo</option>';
			}
		}
	}else{
		$('.alerts').html('<div class="alert alert-danger fade in"><strong>¡Un selector ha fallado !</strong>Los datos para agregar al selector no estan disponibles!!!.<br><br><ul><li>Intente de nuevo realizar la operación luego de refrescar esta pagina.</li></ul></div>');
	}	
    
};

oli_afiliado.prototype.changeSelectEntidad = function(object) {
	
	var datos = new Array();
	datos['id'] = object.options[object.selectedIndex].value;
	datos['select'] = object.id;
	seg_ajaxobject.peticionajax($('#form_select').attr('action'),datos,"oli_afiliado.selectRespuestaEntidad");
	
};

oli_afiliado.prototype.selectRespuestaEntidad = function(result) {
	if(result.respuesta){
		if(result.data != null){
			if(result.select == 'id_seate'){
				/*borramos los datos anteriores*/
				select = document.getElementById('id_companye');				
			    select.innerHTML='<option value="" selected="selected">Ingresa la empresa</option>';
			    
			    select = document.getElementById('id_linke');				
			    select.innerHTML='<option value="" selected="selected">Ingresa el vinculo</option>';
				
				select = document.getElementById('id_companye');
				for(var i=0;i<result.data.length;i++){
					var opt = document.createElement('option');
				    opt.value = result.data[i].id;
				    opt.text = result.data[i].company;
				    select.appendChild(opt);
				}	
			}
			if(result.select == 'id_companye'){
				/*borramos los atos anteriores*/
				select = document.getElementById('id_linke');				
				select.innerHTML='<option value="" selected="selected">Ingresa el vinculo</option>';
				
				select = document.getElementById('id_linke');
				for(var i=0;i<result.data.length;i++){
					var opt = document.createElement('option');
				    opt.value = result.data[i].id;
				    opt.text = result.data[i].link;
				    select.appendChild(opt);
				}	
			}
		}
		else{
			/*data null*/
			if(result.select == 'id_seate'){
				/*borramos todos los option de company y de links*/
				select = document.getElementById('id_companye');				
			    select.innerHTML='<option value="" selected="selected">Ingresa la empresa</option>';
			    
			    select = document.getElementById('id_linke');				
			    select.innerHTML='<option value="" selected="selected">Ingresa el vinculo</option>';
			}
			if(result.select == 'id_companye'){
				/*borramos todos los option de links*/		    
			    select = document.getElementById('id_linke');				
			    select.innerHTML='<option value="" selected="selected">Ingresa el vinculo</option>';
			}
		}
	}else{
		$('.alerts').html('<div class="alert alert-danger fade in"><strong>¡Un selector ha fallado !</strong>Los datos para agregar al selector no estan disponibles!!!.<br><br><ul><li>Intente de nuevo realizar la operación luego de refrescar esta pagina.</li></ul></div>');
	}	
    
};
	

var oli_afiliado = new oli_afiliado();
