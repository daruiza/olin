<DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">	
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>	
	</head>
	{{ Html::style('css/hompdf.css')}}	
	<body>
		<div class ="cnt_conteiner">
			<div class ="cabecera">					
				<div class = "cab1">
					<div class = "logo">
						<img src="{{url('/css/image10.png')}}">
					</div>
					<div class = "fecha" style="text-align: justify;"><font>FECHA</font>: {{date("Y-m-d H:i:s")}}</div>
				</div>
				<div class = "cab2">
					<div class = "cab"> <b>ASESORIA DEL SERVICIO</b></div>
					<div class = "cab"> <b>SOLICITUD INICAL DEL SERVICIO</b></div>
					<div class = "cab" > 
						<div style="width: 50%;float: left;"> 
							<div style="border-width: 0px 1px 0px 0px;border-style: solid;">CÓDIGO: F7-PR1PAU</div>
						</div>
						<div style="width: 50%;float: right;">
							<div style="">VERSIÓN 01</div>
						</div>
					</div>
					<div class = "cab"><font> HORA DEL REPORTE</font>: {{$homenaje[0]->created_at}}</div>
					<div class = "cab" style=" height: 36px;"> 
						<div style="width: 50%;float: left; height: 35px; border-width: 0px 1px 0px 0px;border-style: solid; ">
							<div style="">SIN TRAMITE: </div>							
						</div>
						<div style="float: right;margin-right: 68px;margin-top: -8px !important;">
							am
						</div>
						<div style="float: right;margin-right: 20px;margin-top: -8px !important;">
							pm
						</div>
						<div style="width: 50%;float: right;height: 35px;">
							<div style="">CON TRAMITE:</div>
						</div>
						<div style="float: right;margin-right: -390px;margin-top: -8px !important;">
							am
						</div>
						<div style="float: right;margin-right: -438px;margin-top: -8px !important;">
							pm
						</div>
					</div>

				</div>					
			</div>

		</div>
		<div class ="cuerpo">
			<div class="cab">
				<div style="width: 63%;float: left; border-width: 0px 1px 0px 0px;border-style: solid; ">
					<font>TITULAR:</font> {{strtoupper(explode("-", $homenaje[0]->name_headline)[0])}}
				</div>
				<div style="width: 35%;float: right; margin:1px">
					<font>DOCUMENTO:</font> {{$homenaje[0]->identification_headline}}
				</div>
			</div>		
			<div class="cab" style="height: 34px;">
				<div style="width: 63%;float: left; border-width: 0px 1px 0px 0px;border-style: solid; ">
					<font>CONTACTO:</font> {{strtoupper(explode("-", $homenaje[0]->name)[0])}}   -   <font>TELEFONOS</font>: {{$homenaje[0]->fhone}} - {{$homenaje[0]->cellfhone}}
				</div>
				<div style="width: 35%;float: right; margin:1px">
					<font>ENTIDAD:</font> {{$homenaje[0]->seat}}
				</div>
			</div>
			<div class="cab">
				<div style="width: 63%;float: left; border-width: 0px 1px 0px 0px;border-style: solid; ">
					<font>FALECIDO:</font>  {{strtoupper($homenaje[0]->name_homage)}}
				</div>
				<div style="width: 35%;float: right; margin:1px">
					<font>PARENTESCO:</font> 
				</div>
			</div>
			<div class="cab">
				<div style="width: 100%;float: left;">
					<font>UBICACIÒN:</font> {{$homenaje[0]->location_homage}}
				</div>
			</div>	
			<div class="cab" style="height: 4px;">
				<div style="width: 100%;float: left;">
					
				</div>
			</div>	
			<div class="cab" style="border-width: 1px 1px 1px 1px;border-style: solid; height: 450px;">
				<div style="width: 100%;float: left;">
					SEGUIMIENTO
					<div style="padding: 10px;">
					@foreach($homenaje as $hom)
					<div style="margin: 5px;">
						{{$hom->date_state}} - {{$hom->description_state}}
					</div>
					@endforeach
					</div>
				</div>
			</div>
			<div class="cab">
				<div style="width: 63%;float: left; border-width: 0px 1px 0px 0px;border-style: solid; ">
					<font>COORDINADOR:</font> 
				</div>
				<div style="width: 35%;float: right; margin:1px">
					<font>PEDIDO:</font>
				</div>
			</div>
			<div class="cab" style="border-width: 1px 0px 1px 0px;border-style: solid;  height: 150px;">
				<div style="width: 100%;float: left;">
					
				</div>
			</div>		
		</div>
	</body>
</html>
