<html lang="en">	
	<body style = "text-align: center;">
		<h1>Nueva Solicitud de Servicio</h1>
		<p>El servicio fué radicado con el consecutivo:  {{ $msg[0][1] }}</p>
		
		</br>
		<table style="border: 1px solid black; border-collapse: collapse;margin: 0 auto;">			
		@foreach ($msg as $msj)
			<tr>
				<td style = "border: 1px solid black; border-collapse: collapse; background-color: #009440; color: #fff95f; padding: 4px;">{{ $msj[0] }}</td>
				<td style=" border: 1px solid black; border-collapse: collapse; padding: 4px;text-align: left;">{{ $msj[1] }}</td>
			</tr>
		@endforeach	
		</table>	
	</body>
</html>