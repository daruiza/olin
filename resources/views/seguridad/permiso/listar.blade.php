@extends('app')

@section('content')
	<style>
		.name_user{
			background-color: #009440;
		    color: #fff97f;		    		   
		    padding: 1%;
		    text-align: center;	    	
		}	
		.option_mod{		
			border: 1px solid #009440;
	    	border-radius: 1px;
	    	margin-top: 10px;
	    	padding: 3% 3% 3% 5%;
		}
		.message_mod{
			cursor:pointer;
			color: #009440;
			padding: 2%;
		}
		.message_mod:hover {
			background-color: #009440;
			color: #fff97f;
		}
		
	</style>
	<div class="col-md-2 col-md-offset-1">
		<div class="name_user">
			<div>			
				Modulo {{Session::get('modulo.modulo')}}
			</div>	
			<div>
				Opciones
			</div>				
		</div>
		<div class="option_mod">
			@if(Session::has('modulo'))
            	@foreach (Session::get('opaplus.usuario.permisos')[Session::get('modulo.id_app')]['modulos'][Session::get('modulo.categoria')][Session::get('modulo.id_mod')]['opciones'] as $key => $opc)            		
            		@if($opc['lugar'] == Session::get('opaplus.usuario.lugar.lugar'))
	            		@if($opc['vista'] == 'listar')
	            		<!-- si tiene opcion editar, esta tiene un trato diferentre -->
	            			@if($opc['accion'] == 'actualizar' OR $opc['accion'] == 'borrar')            			
	            				<div class = "message_mod" onclick="javascript:seg_permiso.opt_select('{{json_decode(Session::get('opaplus.usuario.permisos')[Session::get('modulo.id_app')]['modulos'][Session::get('modulo.categoria')][Session::get('modulo.id_mod')]['preferencias'])->controlador}}','{{($opc['accion'])}}/{{Session::get('modulo.id_app')}}/{{Session::get('modulo.categoria')}}/{{Session::get('modulo.id_mod')}}');">           				
	            					<span class="{{$opc['icono']}}" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>{{$opc[$key]}}
	            				</div>            			
	            			@elseif($opc['accion'] == 'mirar')
	            				<div class = "message_mod" onclick="javascript:seg_permiso.opt_ver()" >           				
	            					<span class="{{$opc['icono']}}" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>{{$opc[$key]}}
	            				</div>
	            			@elseif($opc['accion'] == 'botar')
	            				<div id = "0" class = "message_mod bnt_lugar" >           				
	            					<span class="{{$opc['icono']}}" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>{{$opc[$key]}}
	            				</div>	            			
	            			@elseif($opc['accion'] == 'recuperar')
	            				<div id = "1" class = "message_mod bnt_lugar" >           				
	            					<span class="{{$opc['icono']}}" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>{{$opc[$key]}}
	            				</div>
	            			@elseif($opc['accion'] == 'eliminar')
	            				<div id = "-1" class = "message_mod bnt_lugar" >           				
	            					<span class="{{$opc['icono']}}" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>{{$opc[$key]}}
	            				</div>
	            			@else	            			
	            			<a href="{{url(json_decode(Session::get('opaplus.usuario.permisos')[Session::get('modulo.id_app')]['modulos'][Session::get('modulo.categoria')][Session::get('modulo.id_mod')]['preferencias'])->controlador)}}/{{($opc['accion'])}}/{{Session::get('modulo.id_app')}}/{{Session::get('modulo.categoria')}}/{{Session::get('modulo.id_mod')}}" style = "text-decoration: none;">
	            				<div class = "message_mod">           				
	            					<span class="{{$opc['icono']}}" aria-hidden="true" style = "margin-right:5px; color:#666699;" ></span>{{$opc[$key]}}
	            				</div>
	            			</a>
	            			@endif   
		            	@endif
	            	@endif	            		       		
            	@endforeach
            @endif			
			
		</div>
	</div>
	<div class="col-md-8 col-md-offset-0">
	<div class = "alerts">
			@if (count($errors) > 0)
				<div class="alert alert-danger">
					<strong>Algo no va bien con el Modulo!</strong> Hay problemas con con los datos diligenciados.<br><br>
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			
			@if(Session::has('message'))
				<div class="alert alert-info">
					<strong>¡Modulo Roles!</strong> La operación se ha realizado adecuadamente.<br><br>
					<ul>								
						<li>{{ Session::get('message') }}</li>
					</ul>
				</div>
                
            @endif
            <!-- error llega cuando se esta recuperando la contraseña inadecuadamente -->
            @if(Session::has('error'))
				<div class="alert alert-danger">
					<strong>¡Algo no va bien!</strong> Hay problemas con los datos diligenciados.<br><br>
					<ul>								
						<li>{{ Session::get('error') }}</li>								
					</ul>
				</div>
                
            @endif
    </div>
	<table id="example" class="display " cellspacing="0" width="100%">
         <thead>
            <tr>
            	@if(Session::has('modulo.fillable'))
            		@foreach (Session::get('modulo.fillable') as $col)
            			<th>{{$col}}</th>
            		@endforeach
            	@endif               
            </tr>
        </thead>              
    </table> 
    <!-- Form en blanco para capturar la url editar y eliminar-->
    {!! Form::open(array('id'=>'form_lugar', 'url' => 'permiso/lugar','method'=>'POST')) !!}
    {!! Form::close() !!}    
	</div>		
@endsection

@section('modal')
	<div class="modal fade" id="permiso_modal" role="dialog">
	    <div class="modal-dialog">	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title">Modulo Permisos</h4>
	        </div>
	        <div class="modal-body">
	        	 <div class="row ">
	        	 	<div class="col-md-12 col-md-offset-0 row_izq">
	        	 	</div>	        	 		        	 	 
	        	 </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>        
	        </div>
	      </div>
	      
	    </div>
	</div>
@endsection

@section('script')		
	<script type="text/javascript">	
		javascript:seg_permiso.table = $('#example').DataTable( {
		    "responsive": true,
		    "processing": true,
		    "serverSide": true,	        
		    "ajax": "{{url('permiso/listarajax')}}",	       
		    "columns": [				   
		        { "data": "rol"},        	            
		        { "data": "module" },
		        { "data": "option" },
		        { "data": "rol_id","visible": false },
		        { "data": "module_id","visible": false },
		        { "data": "option_id","visible": false }          
		    ],	       
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
		    },
		    "sDom": 'T<"clear">lfrtip',            
		    "oTableTools": {
		        "sSwfPath": "/assets/swf/copy_csv_xls_pdf.swf",
		        "aButtons": [
		            {
		                "sExtends": "copy",
		                "mColumns": [0,1,2],
		                "sTitle": "{{Session::get('modulo.modulo')}}"
		            },
		            {
		                "sExtends": "csv",
		                "mColumns": [0,1,2],
		                "sTitle": "{{Session::get('modulo.modulo')}}"
		            },
		            {
		                "sExtends": "xls",
		                "mColumns": [0,1,2],
		                "sTitle": "{{Session::get('modulo.modulo')}}",
		                "sFileName": "*.xls"
		            },
		            {
		                "sExtends": "pdf",
		                "mColumns": [0, 1, 2],
		                "sTitle": "{{Session::get('modulo.modulo')}}"                        
		            }                    
		        ]
		    }
		});
		@if(Session::has('filtro'))
			seg_permiso.table.search( "{{Session::get('filtro')}}" ).draw();
		@endif	
		javascript:$('#example tbody').on( 'click', 'tr', function () {
		    if ($(this).hasClass('selected')) {
		        $(this).removeClass('selected');
		    }
		    else {
		    	seg_permiso.table.$('tr.selected').removeClass('selected');
		        $(this).addClass('selected');
		    }
		});
		//llamado del metodo botar
	    $('.bnt_lugar').click(function(e){
  	  		e.preventDefault();//evita que la pagina se refresque
	  	  	if(seg_permiso.table.rows('.selected').data().length){		
		  	  	var datos = new Array();
	  	  		datos['rol_id'] = seg_permiso.table.rows('.selected').data()[0].rol_id;
	  	  		datos['module_id'] = seg_permiso.table.rows('.selected').data()[0].module_id;
	  	  		datos['option_id'] = seg_permiso.table.rows('.selected').data()[0].option_id;
	  	  		seg_ajaxobject.peticionajax($('#form_lugar').attr('action'),datos,"seg_permiso.lugarRespuesta");
	  		}else{
	  			$('.alerts').html('<div class="alert alert-info fade in"><strong>¡Seleccione un registro!</strong>Esta opción requiere la selección de un registro!!!.<br><br><ul><li>Selecciona un registro dando click sobre él, luego prueba nuevamente la opción</li></ul></div>');
	  		}  	  		
  		});	
		
	</script>
@endsection