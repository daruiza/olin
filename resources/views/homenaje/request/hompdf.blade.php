<DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Carnet PDF</title>
	</head>
	{{ Html::style('css/hompdf.css')}}	
	<body>		
		<div class ="cnt_conteiner">
			<table style="width:100%">
				<tr>
					<th width="34%"></th>
					<th width="20%"></th>
					<th width="20%"></th>
					<th width="26%"></th>
				</tr>
				<tr>
					<td rowspan="6" class="logo" ></td>
					<td colspan="3" style="text-align: center;">ASESORIA DEL SERVICIO</td>					
				</tr>
				<tr>					
					<td colspan="3" style="text-align: center;">SOLICITUD INICAL DEL SERVICIO</td>
				</tr>
				<tr>
					<td colspan="2">Código {{$homenaje[0]->orden_service}}</td>
					<td>Versión 01</td>
				</tr>
				<tr>					
					<td colspan="3" style="text-align: center;">HORA DEL REPORTE: {{$homenaje[0]->created_at}} </td>
				</tr>
				@if($homenaje[0]->state_id > 1)
				<tr>
					<td colspan="3"  style="text-align: center;">
						<div style="float: left; margin-left: 20px;">SIN TRAMITE:</div>
						<div style="float: right; margin-right: 20px;">CON TRAMITE: OK</div> 
					</td>
				</tr>
				@else
				<tr>
					<td colspan="3"  style="text-align: center;">
						<div style="float: left; margin-left: 20px;">SIN TRAMITE: OK</div>
						<div style="float: right; margin-right: 20px;">CON TRAMITE:</div> 
					</td>
				</tr>
				@endif
				<tr>
					<td colspan="3"  style="text-align: center;"></td>							
				</tr>
				<tr>
					<td>FECHA: </td>
					<td colspan="3"  style="text-align: center;"></td>											
				</tr>
				<tr>
					<td>1</td>
					<td>2</td>
					<td>3</td>
					<td>4</td>					
				</tr>
				<tr>
					<td>1</td>
					<td>2</td>
					<td>3</td>
					<td>4</td>					
				</tr>

			</table>


		<!--
			<div class ="cabecera">
				<div class = "cab1">
					<div class = "logo"></div>
					<div class = "fecha">FECHA:</div>

				</div>
				<div class = "cab2">
					<div class = "cab"> ASESORIA DEL SERVICIO</div>
					
				</div>

			</div>
		-->
			
		</div>
	</body>
</html>
