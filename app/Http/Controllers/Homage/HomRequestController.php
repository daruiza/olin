<?php namespace App\Http\Controllers\Homage;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Homage\HomRequest;
use App\Core\Homage\HomState;
use App\Core\OLIN\OlinCompany;
use App\Core\OLIN\OlinUser;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use DateTime;
use Mail;

class HomRequestController extends Controller {

	protected $auth;
		
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
		$this->middleware('guest');
	}
	
	public function getIndex($id=null, $modulo=null, $descripcion= null, $id_aplicacion = null, $categoria = null){
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		//preparación de los datos
		$moduledata['id']=$id;
		$moduledata['modulo']=$modulo;
		$moduledata['description']=$descripcion;
		$moduledata['id_aplicacion']=$id_aplicacion;
		$moduledata['categoria']=$categoria;
		
		//consultas de solicitudes
		//total, la solicitud solo se creara una vez.
		try {
			$moduledata['total_solicitudes']=\DB::table('hom_request')
			->select(\DB::raw('count(*) as total'))			
			->get()[0]->total;
		}catch (ModelNotFoundException $e) {			
			return Redirect::to('solicitud/general')->with('error', $e->getMessage());
		}
		
		//el estado que se consulta es el untimo estado de la solicitud agrupado
		$moduledata['solicitudes'] = \DB::table('hom_request_x_state AS rq1')		
		->select('hom_state.state','hom_state.alert',\DB::raw('count(*) as total'))
		->leftJoin('hom_request_x_state AS rq2', function( $query ){
			$query->on( 'rq1.request_id', '=', 'rq2.request_id' );
			$query->on( 'rq1.date', '<', 'rq2.date' );
		})
		->whereNull('rq2.date')
		->join('hom_request', 'rq1.request_id', '=', 'hom_request.id')
		->join('hom_state', 'rq1.state_id', '=', 'hom_state.id')
		->groupBy('rq1.state_id')
		->orderBy('hom_state.order','asc')		
		->get();
		
		//Total servicios
		$moduledata['total_servicios'] = \DB::table('hom_request')
		->select(\DB::raw('count(*) as total'))
		->join('hom_request_x_state', 'hom_request_x_state.request_id', '=', 'hom_request.id')
		->where('hom_request_x_state.state_id',4)		
		->get()[0]->total;
		
		//Solicitudes finalizadas x entidades
		$moduledata['servicios_entidad'] = \DB::table('hom_request')
		->select('hom_request.seat',\DB::raw('count(*) as total'))
		->join('hom_request_x_state', 'hom_request_x_state.request_id', '=', 'hom_request.id')
		->where('hom_request_x_state.state_id',4)
		->groupBy('hom_request.seat')
		->get();
				
		//consulta de datos para histograma
		//solicitudes almacena la información de las solicitudes ya finalizadas
		$solicitudes = \DB::table('hom_request_x_state AS rq1')
		//->select('rq1.*','hom_request.*','hom_state.*')
		->select('rq1.date','hom_request.created_at','hom_request.seat')
		->leftJoin('hom_request_x_state AS rq2', function( $query ){
			$query->on( 'rq1.request_id', '=', 'rq2.request_id' );
			$query->on( 'rq1.date', '<', 'rq2.date' );
		})
		->whereNull('rq2.date')
		->join('hom_request', 'rq1.request_id', '=', 'hom_request.id')
		->join('hom_state', 'rq1.state_id', '=', 'hom_state.id')
		->where(function ($query) {
			$query->where('hom_request.id', 'like', '%'.Session::get('search').'%')
			->orWhere('hom_request.name', 'like', '%'.Session::get('search').'%')
			->orWhere('hom_request.identification_headline', 'like', '%'.Session::get('search').'%')
			->orWhere('hom_state.state', 'like', '%'.Session::get('search').'%');			
		})	
		->where('rq1.state_id',4)
		->orderBy('hom_state.order','asc')
		->get();
		
		//creamos los datos para histograma
		$datos = Array('categoria'=>array(),'datos'=>array());
		
		$datos['datos']['mas_de_venticuatro']=array();
		$datos['datos']['menos_de_venticuatro']=array();
		$datos['datos']['menos_de_doce']=array();
		$datos['datos']['menos_de_seis']=array();
		$datos['datos']['menos_de_tres']=array();
		
		//creación de array de categorias
		$categorias = array();//este es el array innverso de gategorias
		$i = 0;
		foreach ($solicitudes as $value){
			if(!(in_array($value->seat,$datos['categoria']))){
				$datos['categoria'][] = $value->seat;
				$categorias[$value->seat] = $i;
				$i++;
			}
		}
		//creamos el dato diferencia para cada solicitud
		foreach ($solicitudes as $solicitud){
			$from = new DateTime($solicitud->date);
			$to = new DateTime($solicitud->created_at);
			$solicitud->diff = $to->diff($from, true);
		}
		
		//array de datos, rellenamos cada array uno por uno
		foreach ($datos['datos'] as $dat=>$dato){
			foreach ($solicitudes as $sol){						
				foreach ($datos['categoria'] as $cat=>$categoria){					
					$datos['datos'][$dat][$cat]=0;
				}
			}
		}
		
		//array de datos, ubicamos cada solicitud una por una
		foreach ($solicitudes as $sol){			
			if($sol->diff->d > 3){
				if($sol->diff->d > 6){
					if($sol->diff->d > 12){
						if($sol->diff->d > 24){
							$datos['datos']['mas_de_venticuatro'][$categorias[$sol->seat]]++; 
						}else{
							$datos['datos']['menos_de_venticuatro'][$categorias[$sol->seat]]++;
						}
					}else{
						$datos['datos']['menos_de_doce'][$categorias[$sol->seat]]++;
					}
				}else{
					$datos['datos']['menos_de_seis'][$categorias[$sol->seat]]++;
				}
			}else{
				$datos['datos']['menos_de_tres'][$categorias[$sol->seat]]++;
			}
		}
		
		$moduledata['datos']=$datos;
		
		return Redirect::to('solicitud/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('homenaje.request.request_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('solicitud/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['id','Contacto','Telefono','Titular','Estado','Fecha','Intervalo'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		Session::flash('modulo', $moduledata);
		return view('homenaje.request.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=HomRequest::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['solicitudes'] = \DB::table('hom_request_x_state AS rq1')
			->select('rq1.*','hom_request.*','hom_state.*','rq1.date')
			->leftJoin('hom_request_x_state AS rq2', function( $query ){
				$query->on( 'rq1.request_id', '=', 'rq2.request_id' );
				$query->on( 'rq1.date', '<', 'rq2.date' );
			})
			->whereNull('rq2.date')
			->join('hom_request', 'rq1.request_id', '=', 'hom_request.id')
			->join('hom_state', 'rq1.state_id', '=', 'hom_state.id')
			->where(function ($query) {
				$query->where('hom_request.id', 'like', '%'.Session::get('search').'%')
				->orWhere('hom_request.name', 'like', '%'.Session::get('search').'%')
				->orWhere('hom_request.identification_headline', 'like', '%'.Session::get('search').'%')
				->orWhere('hom_request.identification_homage', 'like', '%'.Session::get('search').'%')				
				->orWhere('hom_state.state', 'like', '%'.Session::get('search').'%')
				->orWhere('hom_request.id', '=', Session::get('search'));
			})
			->skip($request->input('start'))->take($request->input('length'))
			->orderBy('hom_state.order','asc')
			->get();			
			
			$solicitudes = array();
			//consultamos las tuplas de las solicitudes
			foreach ($moduledata['solicitudes'] as $solicitud){
				$tupla=\DB::table('hom_request_x_state')
				->select('*')
				->join('hom_request', 'hom_request_x_state.request_id', '=', 'hom_request.id')				
				->join('hom_state', 'hom_request_x_state.state_id', '=', 'hom_state.id')
				->where('hom_request_x_state.request_id', $solicitud->request_id)
				->orderBy('hom_request_x_state.date','asc')
				->get();
				$solicitudes[]=$tupla;
			}				
				
			$date_diff = array();
			//cada solicitud es una array donde estan todas las tuplas
			foreach ($solicitudes as $solicitud){
				$from = new DateTime($solicitud[0]->date);
				$to = new DateTime(end($solicitud)->date);
				if(count($solicitud)<2){
					$to = new DateTime();
				}
				$date_diff[] = $to->diff($from, true);
			}
				
			for($i=0; $i<count($date_diff); $i++){
					
				if($date_diff[$i]->y){
					$date_diff[$i] = $date_diff[$i]->format('%y-%m-%d %h:%i:%s');
				}else{
					if($date_diff[$i]->m){
						$date_diff[$i] = $date_diff[$i]->format('%m-%d %h:%i:%s');
					}else{
						if($date_diff[$i]->d){
							$date_diff[$i] = $date_diff[$i]->format('%d %h:%i:%s');
						}else{
							$date_diff[$i] = $date_diff[$i]->format('%h:%i:%s');
						}
					}	
				}	
					
				$moduledata['solicitudes'][$i]->diff = $date_diff[$i];
			}
			
			$moduledata['filtro'] = count($moduledata['solicitudes']);
		}else{			
			
			//el estado que se consulta es el untimo estado de la solicitud						
			$moduledata['solicitudes'] = \DB::table('hom_request_x_state AS rq1')
			->select('rq1.*','hom_request.*','hom_state.*','rq1.date')
			->leftJoin('hom_request_x_state AS rq2', function( $query ){
				$query->on( 'rq1.request_id', '=', 'rq2.request_id' );
				$query->on( 'rq1.date', '<', 'rq2.date' );
			})
			->whereNull('rq2.date')
			->join('hom_request', 'rq1.request_id', '=', 'hom_request.id')
			->join('hom_state', 'rq1.state_id', '=', 'hom_state.id')
			->orderBy('hom_state.order','asc')
			->skip($request->input('start'))->take($request->input('length'))
			->get();			
			
			$solicitudes = array();
			//consultamos las tuplas de las solicitudes	para luego hacer la diferencia		
			foreach ($moduledata['solicitudes'] as $solicitud){					
				$tupla=\DB::table('hom_request_x_state')
				->select('*')
				->join('hom_request', 'hom_request_x_state.request_id', '=', 'hom_request.id')
				->join('hom_state', 'hom_request_x_state.state_id', '=', 'hom_state.id')
				->where('hom_request_x_state.request_id', $solicitud->request_id)
				->orderBy('hom_request_x_state.date','asc')
				->get();
				$solicitudes[]=$tupla;				
			}
			
			
			$date_diff = array();
			//cada solicitud es una array donde estan todas las tuplas
			foreach ($solicitudes as $solicitud){
				$from = new DateTime($solicitud[0]->date);
				$to = new DateTime(end($solicitud)->date);
				if(count($solicitud)<2){
					//si solo hay un estado
					$to = new DateTime();
				}
				$date_diff[] = $to->diff($from, true);
			}
			
			for($i=0; $i<count($date_diff); $i++){
					
				if($date_diff[$i]->y){
					$date_diff[$i] = $date_diff[$i]->format('%y-%m-%d %h:%i:%s');
				}else{
					if($date_diff[$i]->m){
						$date_diff[$i] = $date_diff[$i]->format('%m-%d %h:%i:%s');
					}else{
						if($date_diff[$i]->d){
							$date_diff[$i] = $date_diff[$i]->format('%d %h:%i:%s');
						}else{
							$date_diff[$i] = $date_diff[$i]->format('%h:%i:%s');
						}
					}	
				}	
					
				$moduledata['solicitudes'][$i]->diff = $date_diff[$i];					
			}
			
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['solicitudes']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		$moduledata['id']=$id_mod;
		$moduledata['id_app']=$id_app;
		
		//entidades para el autocomplete
		try{
			$companys = OlinCompany::
			where('active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get()
			->toArray();			
		}catch (ModelNotFoundException $e) {			
			return Redirect::to('solicitud/general')->with('error', $e->getMessage());
		}
		
		foreach ($companys as $company){
			$companyss[] = $company['company'];
		}		
		$moduledata['companys']=$companyss;		
						
		return Redirect::to('solicitud/agregar')->with('modulo',$moduledata);
	}
	public function getAgregar(){
	
		return view('homenaje.request.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){
		
		//creación de datos para el formulario de agregar
		//entidades para el autocomplete
		try{
			$companys = OlinCompany::
			where('active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get()
			->toArray();
		}catch (ModelNotFoundException $e) {
			return Redirect::to('solicitud/general')->with('error', $e->getMessage());
		}
		
		foreach ($companys as $company){
			$companyss[] = $company['company'];
		}
		$moduledata['companys']=$companyss;
						
		$messages = [
			'required' => 'El campo :attribute es requerido.',	
			'numeric' => 'El :attribute  debe ser un número',
		];
		
		$rules = array(
			'nombre_contacto'    => 'required',			
			'identification_titular' => 'numeric',
			'identification_homenaje' => 'numeric',
		);
		
		//validación numero telefonico o numero celular solo para guardar primer estado
		if(!$request->input()['edit']){
			if(empty($request->input()['telefono_contacto'])){
				if(empty($request->input()['celular_contacto'])){
					return Redirect::to('solicitud/agregar')->with('error', 'Se bebe ingresar el telefono o el celular del contacto')->withInput()->with('modulo',$moduledata);
				}
					
			}
			
		}	
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput()->with('modulo',$moduledata);
			
		}else{
					
			$homrequest = new HomRequest();
			
			$homrequest->name = $request->input()['nombre_contacto'];
			$homrequest->fhone = $request->input()['telefono_contacto'];
			$homrequest->cellfhone = $request->input()['celular_contacto'];
			
			$homrequest->identification_headline = $request->input()['identification_titular'];
			$homrequest->name_headline = $request->input()['nombre_titular'];
			$homrequest->seat = $request->input()['entidad'];
			
			$homrequest->identification_homage = $request->input()['identification_homenaje'];
			$homrequest->name_homage = $request->input()['nombre_homenaje'];
			$homrequest->location_homage = $request->input()['ubicacion_homenaje'];		
					
			//listamos todas los estados, tambien se usan para armar al message
			try{
				$states = HomState::
				where('active', '=' , Session::get('opaplus.usuario.lugar.active'))
				->orderBy('hom_state.order','asc')
				->get()
				->toArray();
			}catch (ModelNotFoundException $e) {
				return Redirect::to('solicitud/general')->with('error', $e->getMessage());
			}
			foreach ($states as $state){
				$estados[$state['id']] = $state['state'];
				$statess['id'][] = $state['id'];
				$statess['state'][] = $state['state'];
				$statess['alert'][] = $state['alert'];
			}
			$moduledata['states']=$statess;
						
			if($request->input()['edit']){
				
				$homrequest->orden_service = $request->input()['orden_service'];
				$homrequest->date_service = $request->input()['date_service'];
								
				//se cargan todas la variables
				Session::flash('_old_input.nombre_contacto',  $request->input()['nombre_contacto']);
				Session::flash('_old_input.telefono_contacto', $request->input()['telefono_contacto']);
				Session::flash('_old_input.celular_contacto', $request->input()['celular_contacto']);
				
				Session::flash('_old_input.identification_titular', $request->input()['identification_titular']);
				Session::flash('_old_input.nombre_titular', $request->input()['nombre_titular']);
				Session::flash('_old_input.entidad', $request->input()['entidad']);
				
				Session::flash('_old_input.identification_homenaje', $request->input()['identification_homenaje']);
				Session::flash('_old_input.nombre_homenaje', $request->input()['nombre_homenaje']);
				Session::flash('_old_input.ubicacion_homenaje', $request->input()['ubicacion_homenaje']);
				
				Session::flash('_old_input.orden_service', $request->input()['orden_service']);
				Session::flash('_old_input.date_service', $request->input()['date_service']);
				
				Session::flash('_old_input.description', $request->input()['description']);
					
				Session::flash('_old_input.solicitud_id', $request->input()['solicitud_id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');
				
				$moduledata['state'] = $request->input()['state'];
				$moduledata['id']=$request->input()['mod_id'];
				$moduledata['id_app']=$request->input()['app_id'];
				
				
				//se pretende actualizar la solicitud				
				try {
					//actualizacion de solicitud
					$requestAffectedRows = HomRequest::where('id', $request->input()['solicitud_id'])->update(array('name' => $homrequest->name,'fhone' => $homrequest->fhone,'cellfhone'=>$homrequest->cellfhone,'identification_headline'=>$homrequest->identification_headline,'name_headline'=>$homrequest->name_headline,'seat'=>$homrequest->seat,'identification_homage'=>$homrequest->identification_homage,'name_homage'=>$homrequest->name_homage,'location_homage'=>$homrequest->location_homage,'orden_service'=>$homrequest->orden_service,'date_service'=>$homrequest->date_service));
										
					//actualización de estado
					if($request->input()['state'] != $request->input()['state_old']){
						if($requestAffectedRows){
							$state = \DB::table('hom_request_x_state')->insert(array('state_id' => $request->input()['state'],'request_id'=>$request->input()['solicitud_id'],'date'=>date("Y-m-d G:i:s"),'description_state' => $request->input()['description']));
						}
					}
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'La solicitud no se logro editar';
					return Redirect::to('solicitud/agregar')->with('error', $message)->withInput()->with('modulo',$moduledata);
				}			
								
				return Redirect::to('solicitud/agregar')->withInput()->with('message', 'Solicitud editada exitosamente')->with('modulo',$moduledata);
				
			}else{
				try {	
					
					$homrequest->save();
					
					//se procede a guardar el estado de la solicitud
					$state = \DB::table('hom_request_x_state')->insert(array('state_id' => $request->input()['state'],'request_id'=>$homrequest->id,'date'=>date("Y-m-d G:i:s"),'description_state' => $request->input()['description']));
					
					/* Datos del mensaje tabla
					$message = array();
					$message[] = 'Solicitud agregada exitosamente';
					$message[] = 'Solicitud: '.$homrequest->id;
					$message[] = 'Estado: '.$estados[$request->input()['state']];
					$message[] = 'Fecha: '.date("Y-m-d G:i:s");
					$message[] = 'Observación: '.$request->input()['description'];
					$message[] = 'DATOS CONTACTO';
					if(!empty($homrequest->name)) $message[] = 'Nombre: '.$homrequest->name;
					if(!empty($homrequest->fhone)) $message[] = 'Telefono: '.$homrequest->fhone;
					if(!empty($homrequest->cellfhone)) $message[] = 'Telefono: '.$homrequest->cellfhone;					
					$message[] = 'DATOS TITULAR';
					if(!empty($homrequest->identification_headline)) $message[] = 'Identificación: '.$homrequest->identification_headline;
					if(!empty($homrequest->name_headline)) $message[] = 'Nombre: '.$homrequest->name_headline;
					if(!empty($homrequest->seat)) $message[] = 'Entidad: '.$homrequest->seat;
					$message[] = 'DATOS HOMENAJE';
					if(!empty($homrequest->identification_homage)) $message[] = 'Identificación: '.$homrequest->identification_homage;
					if(!empty($homrequest->name_homage)) $message[] = 'Nombre: '.$homrequest->name_homage;
					if(!empty($homrequest->location_homage)) $message[] = 'Entidad: '.$homrequest->location_homage;
					*/
					$message = array();
					$message[0][0] = 'SOLICITUD';$message[0][1] = $homrequest->id;
					$message[1][0] = 'ESTADO';$message[1][1] = $estados[$request->input()['state']];
					
					if(!empty($homrequest->name)) {$message[2][0] = 'NOMBRE CONTACTO';$message[2][1] = $homrequest->name;}
					if(!empty($homrequest->fhone)) {$message[3][0] = 'TELEFONO CONTACTO';$message[3][1] = $homrequest->fhone;}
					if(!empty($homrequest->cellfhone)) {$message[4][0] = 'CELULAR CONTACTO';$message[4][1] = $homrequest->cellfhone;}
					
					if(!empty($homrequest->name_headline)) {$message[5][0] = 'NOMBRE TITULAR';$message[5][1] = $homrequest->name_headline;}
					if(!empty($homrequest->identification_headline)) {$message[6][0] = 'IDENTIFICACIÓN TITULAR';$message[6][1] = $homrequest->identification_headline;}
					if(!empty($homrequest->seat)) {$message[7][0] = 'ENTIDAD AFILIADO';$message[7][1] = $homrequest->seat;}
					
					if(!empty($homrequest->name_homage)) {$message[8][0] = 'NOMBRE SER QUERIDO';$message[8][1] = $homrequest->name_homage;}
					if(!empty($homrequest->identification_homage)) {$message[9][0] = 'IDENTIFICACIÓN SER QUERIDO';$message[9][1] = $homrequest->identification_homage;}
					if(!empty($homrequest->location_homage)) {$message[10][0] = 'UBICACIÓN FALLECIDO';$message[10][1] = $homrequest->location_homage;}
					
					$message[11][0] = 'FECHA';$message[11][1] = date("Y-m-d G:i:s");
					$message[12][0] = 'OBSERVACIÓN';$message[12][1] = $request->input()['description'];
					
					//Aqui todo el codigo para enviar correos electronicos
					//consultamos los emails receptores del mensaje
					try {
						$model = \DB::table('hom_reciver')
						->where('topic', '=', 'servicio')
						->where('active', '=', 1)
						->get();
					}catch (ModelNotFoundException $e) {
						$msg = 'La consulta de email no fue valida';
						return Redirect::back()->with('error', $msg)->with('message_table', $message);
					}
					//preguntamos si hay almenos un email
						
					$data = array(
						'name' => Session::get('copy'),
						'mail' => Session::get('mail'),
						'msg' => $message,
					);
					
					Mail::send('email.service',$data,function($msg) use ($model) {
						$msg->from(Session::get('mail'),Session::get('copy'));
						foreach($model as $mod ){
							$msg->to($mod->email,$mod->name)->subject('Solicitud de Servicio');
						}
						
					});
					
					return Redirect::to('solicitud/agregar')->with('message_table', $message)->with('modulo',$moduledata);
					//return Redirect::to('solicitud/agregar')->with('message_add', $message)->with('modulo',$moduledata);
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'La solicitud no se logro agregar';					
					return Redirect::to('solicitud/agregar')->with('error', $e->getMessage())->withInput()->with('modulo',$moduledata);
				}				
			}		
			
		}
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		
		$moduledata['id']=$id_mod;
		$moduledata['id_app']=$id_app;
		
		//entidades para el autocomplete
		try{
			$companys = OlinCompany::
			where('active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get()
			->toArray();
		}catch (ModelNotFoundException $e) {
			return Redirect::to('solicitud/general')->with('error', $e->getMessage());
		}		
		foreach ($companys as $company){
			$companyss[] = $company['company'];
		}
		$moduledata['companys']=$companyss;
		
		//listamos todas los estados
		try{
			$states = HomState::
			where('active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->orderBy('hom_state.order','asc')
			->get()
			->toArray();
		}catch (ModelNotFoundException $e) {
			return Redirect::to('solicitud/general')->with('error', $e->getMessage());
		}
		foreach ($states as $state){
			$statess['id'][] = $state['id'];
			$statess['state'][] = $state['state'];
			$statess['alert'][] = $state['alert'];
		}
		$moduledata['states']=$statess;
				
		$solicitud=\DB::table('hom_request_x_state')
		->select('*')
		->join('hom_request', 'hom_request_x_state.request_id', '=', 'hom_request.id')
		->join('hom_state', 'hom_request_x_state.state_id', '=', 'hom_state.id')
		->where('hom_request_x_state.request_id', $id)
		->orderBy('hom_request_x_state.date','desc')
		->get();
		
		Session::flash('_old_input.nombre_contacto', $solicitud[0]->name);
		Session::flash('_old_input.telefono_contacto', $solicitud[0]->fhone);
		Session::flash('_old_input.celular_contacto', $solicitud[0]->cellfhone);
		
		Session::flash('_old_input.identification_titular', $solicitud[0]->identification_headline);
		Session::flash('_old_input.nombre_titular', $solicitud[0]->name_headline);
		Session::flash('_old_input.entidad', $solicitud[0]->seat);		
				
		Session::flash('_old_input.identification_homenaje', $solicitud[0]->identification_homage);
		Session::flash('_old_input.nombre_homenaje', $solicitud[0]->name_homage);
		Session::flash('_old_input.ubicacion_homenaje', $solicitud[0]->location_homage);
		
		Session::flash('_old_input.orden_service', $solicitud[0]->orden_service);
		Session::flash('_old_input.date_service', null);
		
		if($solicitud[0]->date_service != '0000-00-00 00:00:00'){			
			Session::flash('_old_input.date_service', $solicitud[0]->date_service);
		}
		
		
		Session::flash('_old_input.description', $solicitud[0]->description_state);
			
		Session::flash('_old_input.solicitud_id', $id);
		Session::flash('_old_input.edit', true);
		Session::flash('titulo', 'Editar');		
		
		$moduledata['state'] = $solicitud[0]->state_id;
				
		return Redirect::to('solicitud/agregar')->with('modulo',$moduledata);
	}
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['id','Contacto','Telefono','Titular','Estado','Fecha','Intervalo'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('homenaje.request.listar');
		
	}
	
	public function postVer(Request $request){
		try {
			$solicitudes=\DB::table('hom_request_x_state')
			->select('*')
			->join('hom_request', 'hom_request_x_state.request_id', '=', 'hom_request.id')
			->join('hom_state', 'hom_request_x_state.state_id', '=', 'hom_state.id')
			->where('hom_request_x_state.request_id', $request->input()['id'])	
			->orderBy('hom_request_x_state.date','asc')
			->get();
			return response()->json(['respuesta'=>true,'data'=>$solicitudes]);
			
		}catch (ModelNotFoundException $e) {
			return response()->json(['respuesta'=>false,'data'=>null]);
		}
		return response()->json(['respuesta'=>false,'data'=>null]);
	}
	
	public function postConsultartitular(Request $request){
		$array = Array();
		try{
			$olinuser = OlinUser::
			select('identification','name','company')
			->leftJoin('oli_company','oli_user.company_id','=','oli_company.id')
			->where('identification', '=' , $request->input()['id'])
			->get()
			->toArray();
				
		}catch (ModelNotFoundException $e) {
			return Redirect::to('solicitud/general')->with('error', $e->getMessage());
		}
	
		$array[] = $olinuser;
	
		return response()->json(['respuesta'=>true,'data'=>$array]);
	}
	
	public function postConsultarhomage(Request $request){
		$array = Array();
		try{
			$olinuser = OlinUser::
			select('identification','name','company')
			->leftJoin('oli_company','oli_user.company_id','=','oli_company.id')
			->where('identification', '=' , $request->input()['id'])
			->get()
			->toArray();
				
		}catch (ModelNotFoundException $e) {
			return Redirect::to('solicitud/general')->with('error', $e->getMessage());
		}
	
		$array[] = $olinuser;
	
		return response()->json(['respuesta'=>true,'data'=>$array]);
	}
	
}
