<DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">		
	</head>
	{{ Html::style('css/hompdf.css')}}	
	<body>		
		<div class ="cnt_conteiner">

			<div class ="cabecera">
				<div class = "cab1">
					<div class = "logo">
						<img src="{{url('/css/image10.png')}}">
					</div>
					<div class = "fecha" style="text-align: justify;">FECHA: {{date("Y-m-d H:i:s")}}</div>
				</div>
				<div class = "cab2">
					<div class = "cab"> ASESORIA DEL SERVICIO</div>
					<div class = "cab"> SOLICITUD INICAL DEL SERVICIO</div>
					<div class = "cab" style=" display: flex;padding: 0px;"> 
						<div style="width: 50%; border-style: solid;border-width: 0px 1px 0px 0px;padding: 4px;" > Código: {{$homenaje[0]->orden_service}}</div>	
						<div style="width: 50%;padding: 4px;"> Versiòn 01</div>					
					</div>
					<div class = "cab"> HORA DEL REPORTE: {{$homenaje[0]->created_at}}</div>
					@if($homenaje[0]->state_id > 1)
						<div class = "cab" style=" display: flex; height: 46px; padding: 0px;"> 
							<div style="width: 50%; border-style: solid;border-width: 0px 1px 0px 0px;" > SIN TRAMITE: </div>
							<div style="width: 50%;"> CON TRAMITE: OK </div>
						</div>
					@else
						<div class = "cab" style=" display: flex; height: 46px;padding: 0px;"> 
							<div style="width: 50%; border-style: solid;border-width: 0px 1px 0px 0px;" > SIN TRAMITE: OK</div>
							<div style="width: 50%;"> CON TRAMITE: </div>
						</div>
					@endif
				</div>
			</div>
			<div class="cabecera" style="height: 38px;text-align: justify;">
				<div class = "cuerpo1">TITULAR: {{strtoupper(explode("-", $homenaje[0]->name_headline)[0])}}</div>
				<div class = "cuerpo2">DOCUMENTO: {{$homenaje[0]->identification_headline}}</div>				
			</div>
			<div class="cabecera" style="text-align: justify;">
				<div class = "cuerpo1">TELEFONOS: {{$homenaje[0]->fhone}} - {{$homenaje[0]->cellfhone}}</div>
				<div class = "cuerpo2">ENTIDAD: {{strtoupper(explode("-", $homenaje[1]->name_headline)[0])}}</div>
			</div>
			<div class="cabecera" style="height: 38px;text-align: justify;">
				<div class = "cuerpo1">FALLECIDO: {{strtoupper($homenaje[0]->name_homage)}}</div>
				<div class = "cuerpo2">PARENTESCO</div>
			</div>
			<div class="cabecera" style="text-align: justify;">
				<div style="border-width: 1px 1px 0px 1px;border-style: solid;width: 100%;padding: 4px;">UBICACIÒN: {{$homenaje[0]->location_homage}}</div>		
			</div>
			<div class="cabecera" style="height: 19px;">
				<div style="border-width: 1px 1px 0px 1px;border-style: solid;width: 100%;"></div>
			</div>
			<div class="cabecera" style="text-align: justify;height: 700px;">
				<div style="border-width: 1px 1px 1px 1px;border-style: solid;width: 100%;">
					SEGIMIENTO
					<div style="padding: 10px;">
					@foreach($homenaje as $hom)
					<div style="margin: 5px;">
						{{$hom->date_state}} - {{$hom->description_state}}
					</div>
					@endforeach
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
