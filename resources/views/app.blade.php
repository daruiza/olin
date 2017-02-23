<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	<title>{!! Session::get('app') !!}</title>	
	<link rel="shortcut icon" href="{{ url('images/icons/icon.png') }}">
	 
	{{ Html::style('css/app.css')}}

	<!-- Fonts -->
	{{ Html::style('css/lib/google.css')}}
	{{ Html::style('css/lib/jquery-ui.css')}}
	{{ Html::style('css/lib/bootstrap-submenu.min.css')}}
	{{ Html::style('css/lib/datatables.min.css')}}
	{{ Html::style('css/lib/datatables.tabletools.css')}}
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
		
</head>
<body>	
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{ url('/') }}">{{ Session::get('app') }}</a>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				
				@if (Auth::guest())
				<!--  
				<ul class="nav navbar-nav">
				<li class="dropdown">
					<a href="#" data-submenu="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" tabindex="0">Inicio<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">							
						<li><a href="{{ url('/auth/logout') }}">Documentacion</a></li>							
						<li><a href="{{ url('/auth/logout') }}">Politicas de seguridad</a></li>											
					</ul>
				</li>				
				</ul>
				-->
				@else
				<!--  
				<ul class="nav navbar-nav">
				<li class="dropdown">
					<a href="#" data-submenu="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Inicio<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">											
						
						<li class="dropdown-submenu"><a >Documentación</a>
							<ul class="dropdown-menu">
								<li><a href="{{ url('/docs/inicio') }}">Inicio con OLIN</a></li>
									
							</ul>
						</li>	
												
						<li><a href="{{ url('/auth/logout') }}">Politicas de seguridad</a></li>	
																				
					</ul>
				</li>				
				</ul>
				-->
				
				<!-- Para pintar los modulos de las aplicaciones -->
				<!-- Para importar los js de los modulos -->				
				@foreach (Session::get('opaplus.usuario.permisos') as $llave_permiso => $permiso)
				  <ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" data-submenu="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{$permiso['aplicacion']}}<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
						
						<!-- Por cada categoria -->
						@foreach ($permiso['modulos'] as $llave_categoria => $categoria)
							<li class="dropdown-header">{{ $llave_categoria  }}</li>
							
							<!-- Por cada modulo dentro de la categoria -->
							@foreach ($categoria as $llave_modulo => $modulo)
								<li class="dropdown-submenu"><a >{{$modulo['modulo']}}</a> 													
									<ul class="dropdown-menu">
										<!-- opción General solo para superadministrador -->
										@if(Session::get('opaplus.usuario.rol_id') == 1)
											<li><a href="{{ url(json_decode($modulo['preferencias'])->controlador)}}/index/{{$llave_modulo}}/{{$modulo['modulo']}}/{{$modulo['descripcion']}}/{{$llave_permiso}}/{{$llave_categoria}}" > General </a> </li>
										@endif	
										<!-- Por cada opcion del modulo -->	
										@foreach ($modulo['opciones'] as $llave_opcion => $opcion)
											@if($opcion['lugar'] == Session::get('opaplus.usuario.lugar.lugar'))
												@if($opcion['vista'] != 'listar')												
													<li><a href="{{ url(json_decode($modulo['preferencias'])->controlador)}}/{{($opcion['accion'])}}/{{$llave_permiso}}/{{$llave_categoria}}/{{$llave_modulo}}" >{{$opcion[$llave_opcion]}}</a> </li>           			
						            			@endif
											@endif																				
										@endforeach
									</ul>
								</li>
								<!-- Cargamos los js que hacer referencia alos modulos para el cliente -->
								{{ Html::script('js/'.json_decode($permiso['preferencias'])->js.'/'.json_decode($modulo['preferencias'])->js.'.js') }}							 
							
							@endforeach
							
							@if( $categoria != end($permiso['modulos']))
								<li role="separator" class="divider"></li>
							@endif
																				 
						@endforeach	
						 							
						</ul>
					</li>
				</ul>
				@endforeach	
				
				<ul class="nav navbar-nav navbar-right">
				
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Session::get('opaplus.usuario.names') }} <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<!-- Opción perfil para todos menos para los agentes -->
						@if(Session::get('opaplus.usuario.rol_id') != 4)
							<li><a href="{{ url('perfil_usuario') }}">Perfil</a></li>
						@endif						
						<!--  <li><a href="{{ url('buzon_usuario') }}">Buzón</a></li> -->
						<li><a href="{{ url('salida_segura') }}">Salida segura</a></li>
					</ul>
				</li>
				</ul>
				@endif
						
			</div>
		</div>
	</nav>

	@yield('content')
	@yield('modal')

	<!-- Scripts -->	  
	{{ Html::script('js/lib/jquery.min.js')}}
	{{ Html::script('js/lib/jquery-ui.js') }}
	{{ Html::script('js/lib/bootstrap.min.js') }}
	{{ Html::script('js/lib/bootstrap.submenu.min.js') }}	
	{{ Html::script('js/lib/highcharts.js') }}	
	{{ Html::script('js/lib/exporting.js') }}	
	{{ Html::script('js/lib/datatables.min.js') }}	
	{{ Html::script('js/lib/datatables.tabletools.js') }}
	{{ Html::script('js/lib/datatables.responsive.min.js') }}		
	{{ Html::script('js/seguridad/seg_user.js') }}
	{{ Html::script('js/seguridad/seg_ajaxobject.js') }}

	<!-- {{ Html::script('js/seguridad/ready.js') }} -->
	<script type="text/javascript">
		$('[data-submenu]').submenupicker();		
	</script>	
	
	@yield('script')
	
</body>
<footer>
	<div class="form-group">
		<div class="col-md-3 col-md-offset-5">
			<p>© 2016 {{ Session::get('copy') }}, Inc.</p>
		</div>		
	</div>	
</footer>		
</html>
<!--  {{ dd(Session::all()) }} -->
