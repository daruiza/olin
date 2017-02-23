function seg_user() {
	
	this.btn_editar = 1 ;
}
	
seg_user.prototype.onjquery = function() {	
};

seg_user.prototype.lugarRespuesta = function(result) {
	//se evalua la respuesta
	if(result.respuesta){		
		$('.bnt_lugar').toggle();
		//crear alert
		if(result.data.usuario.lugar.active){
			$('.alerts').html('<div class="alert alert-success fade in"><strong>¡Activación de Escritorio!</strong> el escritorio esta activado.<br><br><ul><li>Ahora todos los mudulos consultan directamente a al escritorio</li></ul></div>');
		}else{
			$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Activación de Papelera!</strong> La pepelera esta activada.<br><br><ul><li>Ahora todos los mudulos consultan directamente a la papelera</li></ul></div>');			
		}
		location.reload();
	}else{
		alert('Problemas con el cambio de lugar');		
	}
	
};
	
seg_user.prototype.edit = function(this_val) {
	if($("#form_user").size()){
		
		this.btn_editar = this.btn_editar*-1;
		var j=0;
		if(this.btn_editar == -1){
			$($(this_val.children).get(1)).html('Deshabilitar edición');
			for(var i=0; i < $("#form_user").children().length; i++){
				if($("#form_user").children().get(i).classList.contains('input-grp')){
					$($("#form_user").children().get(i).children[1]).children().prop("disabled", false);
				}				
			}
			//mostramos el boton
			$($("#form_user").children().get($("#form_user").children().length-1)).children().children().show();
			
		}else{
			$($(this_val.children).get(1)).html('Habilitar edición');
			for(var i=0; i < $("#form_user").children().length; i++){
				if($("#form_user").children().get(i).classList.contains('input-grp')){
					$($("#form_user").children().get(i).children[1]).children().prop("disabled", true)
				}				
			}
			//ocultar el boton
			$($("#form_user").children().get($("#form_user").children().length-1)).children().children().hide()
		}			
	}		
};
	
seg_user.prototype.iniciarDatepiker = function(obj) {
	$( "#"+obj ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
};
seg_user.prototype.changeImg = function(input){
	if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#nueva_img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
};

seg_user.prototype.iniciarPie= function(contenedor_id,titulo,datos,colores){	
	if (colores === undefined || colores === null) {
		colores = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9','#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1']
	}	
	$(contenedor_id).highcharts({
        chart: {
        	renderTo: 'chartcontainer',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'            
        },
        title: {
            text: titulo
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        lang:{
        	printChart : 'Descargar imagen',
        	downloadPNG : 'Descargar imagen PNG',        	
        	downloadJPEG : 'Descargar imagen JPEG',
        	downloadPDF : 'Descargar imagen PDF',
        	downloadSVG : 'Descargar imagen vectorial SVG',
        },
        
        plotOptions: {        	  
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',                              
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'                      
                    
                    }
                }
            }
        },
        colors: colores,
        series: [{
            name: "Porcentaje",
            colorByPoint: true,
            data: datos,
            point:{
                events:{
                    click: function (event) {
                        //alert(this.x + " " + this.y + " " + this.name);
                    }
                }
            }  
        }]
    });
};

seg_user.prototype.iniciarBar= function(contenedor_id,titulo_uno,titulo_dos,datos,categorias){
	$(contenedor_id).highcharts({
		chart: {
			renderTo: 'chartcontainer',
            type: 'bar'
        },
        title: {
            text: titulo_uno
        },
        xAxis: {
            categories: categorias
        },
        yAxis: {
            min: 0,
            title: {
                text: titulo_dos
            }
        },
        legend: {
            reversed: true
        },
        lang:{
        	printChart : 'Descargar imagen',
        	downloadPNG : 'Descargar imagen PNG',        	
        	downloadJPEG : 'Descargar imagen JPEG',
        	downloadPDF : 'Descargar imagen PDF',
        	downloadSVG : 'Descargar imagen vectorial SVG',
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: datos
	});
};

seg_user.prototype.iniciarMultiBar= function(contenedor_id,titulo_uno,titulo_dos,titulo_tres,datos,categorias){
	$(contenedor_id).highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: titulo_uno
        },
        subtitle: {
            text: titulo_dos
        },
        xAxis: {
            categories: categorias,
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: titulo_tres
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: datos
    });
};

seg_user.prototype.iniciarPila= function(contenedor_id,titulo_uno,titulo_dos,datos,categorias){
	$(contenedor_id).highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: titulo_uno
        },
        xAxis: {
            categories: categorias
        },
        yAxis: {
            min: 0,
            title: {
                text: titulo_dos
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },
        series: datos
    });
};


var seg_user = new seg_user();

