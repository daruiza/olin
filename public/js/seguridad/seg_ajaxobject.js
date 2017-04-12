function seg_ajaxobject() {

}

seg_ajaxobject.prototype.peticionajax = function(url, datos, object, async) {	
	if (async === undefined){async = true;}
	var stringDatos = '{APP: "true"';
    for (var d in datos) {
        stringDatos += ',' + d + ': "' + datos[d] + '"';
    }
    stringDatos += '}';	
	
    
	$.ajax(url, {		
		"type": "post",
		"data": eval('(' + stringDatos + ')'),
		"async": async,
		"success": function(result) {
            eval(object + "(result);");			
        },
        "error": function(result) {
            alert("El recurso que intentas accesar esta fuera de rango, probablemente has perdido tu sesión. Desea informar ");
            //se debe informar sobre el error inesperado con otra peticion
        }	 
	});
	
};

seg_ajaxobject.prototype.peticionpost = function(url, datos, object) {	
	var stringDatos = '{APP: "true"';
    for (var d in datos) {
        stringDatos += ',' + d + ': "' + datos[d] + '"';
    }
    stringDatos += '}';
    $.post(url,eval('(' + stringDatos + ')'),function(result) {
    	 eval(object + "(result);");
    }).fail(function() {
        alert( "error en Peticion Post" );
    });
    
};

var seg_ajaxobject = new seg_ajaxobject();