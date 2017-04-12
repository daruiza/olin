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
	
	<div class="col-md-10 col-md-offset-1">
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
					<strong>¡Modulo Logs!</strong> La operación se ha realizado adecuadamente.<br><br>
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
    {!! Form::open(array('id'=>'form_lugar','url' => 'logs/lugar')) !!}
    {!! Form::close() !!}    
	</div>		
@endsection

@section('script')		
	<script type="text/javascript">  	
		javascript:oli_log.table = $('#example').DataTable( {
		    "responsive": true,
		    "processing": true,
		    "serverSide": true,	        
		    "ajax": "{{url('logs/listarajax')}}",	       
		    "columns": [				   
		        { "data": "name"},        	            
		        { "data": "action" },
		        { "data": "description" },
		        { "data": "date" }
		                   
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
		                "mColumns": [0,1,2,3],
		                "sTitle": "{{Session::get('modulo.modulo')}}"
		            },
		            {
		                "sExtends": "csv",
		                "mColumns": [0,1,2,3],
		                "sTitle": "{{Session::get('modulo.modulo')}}"
		            },
		            {
		                "sExtends": "xls",
		                "mColumns": [0,1,2,3],
		                "sTitle": "{{Session::get('modulo.modulo')}}",
		                "sFileName": "*.xls"
		            },
		            {
		                "sExtends": "pdf",
		                "mColumns": [0, 1, 2, 3],
		                "sTitle": "{{Session::get('modulo.modulo')}}"                        
		            }                    
		        ]
		    }
		});
		@if(Session::has('filtro'))
			oli_log.table.search( "{{Session::get('filtro')}}" ).draw();
		@endif	
		javascript:$('#example tbody').on( 'click', 'tr', function () {
		    if ($(this).hasClass('selected')) {
		        $(this).removeClass('selected');
		    }
		    else {
		    	oli_log.table.$('tr.selected').removeClass('selected');
		        $(this).addClass('selected');
		    }
		});
	</script>
@endsection