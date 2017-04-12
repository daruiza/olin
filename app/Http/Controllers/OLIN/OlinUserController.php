<?php namespace App\Http\Controllers\OLIN;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\OLIN\OlinUser;
use App\Core\OLIN\OlinCompany;
use App\Core\OLIN\OlinSeat;
use App\Core\OLIN\OlinRefine;
use Illuminate\Support\Facades\Input;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Files\ExcelFile;

class OlinUserController extends Controller {
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	protected $auth;
	
	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
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
				
		//consultas de afiliados
		//total
		try {
			$moduledata['total_afiliados']=OlinUser::
			join('oli_company', 'company_id', '=', 'oli_company.id')		
			->join('oli_seat', 'oli_company.seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->count();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('afiliados/general')->with('error', $message);
		}
		
		try {
			$moduledata['total_vinculos']=\DB::table('oli_link')
			->select(\DB::raw('count(*) as total'))
			->where('oli_link.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('vinculos/general')->with('error', $message);
		}
		
		try {
			$moduledata['total_sedes']=\DB::table('oli_seat')
			->select(\DB::raw('count(*) as total'))
			->where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('sedes/general')->with('error', $message);
		}
		
		//operaciones de analisis
		//total agrupado por EMPRESAS y afiliados
		try {
			$moduledata['empresas']=\DB::table('oli_user')
			->select('company','seat', \DB::raw('count(*) as total'))
			->join('oli_company', 'company_id', '=', 'oli_company.id')
			->join('oli_seat', 'oli_company.seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->groupBy('company_id')
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo.' de usuario';
			return Redirect::to('afiliados/general')->with('error', $message);
		}	
		
		//total agrupado por vinculos
		try {
			$moduledata['vinculos']=\DB::table('oli_user')
			->select('link', \DB::raw('count(*) as total'))
			->join('oli_link', 'link_id', '=', 'oli_link.id')
			->join('oli_company', 'company_id', '=', 'oli_company.id')
			->join('oli_seat', 'oli_company.seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->where('oli_link.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->groupBy('link_id')
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('vinculos/general')->with('error', $message);
		}
		
		try {
			$moduledata['sedes']=\DB::table('oli_company')
			->select('seat', \DB::raw('count(*) as total'))
			->join('oli_seat', 'seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->groupBy('seat_id')
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo.' de usuario';
			return Redirect::to('afiliados/general')->with('error', $message);
		}
				
		return Redirect::to('afiliados/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('olin.user.user_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('afiliados/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Identificación','Nombre','Empresa','Vinculo','Teléfono','Sede','Fecha Actualización'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		Session::flash('modulo', $moduledata);
		return view('olin.user.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=
		OlinUser::
		join('oli_company', 'company_id', '=', 'oli_company.id')		
		->join('oli_seat', 'oli_company.seat_id', '=', 'oli_seat.id')
		->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
		->count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);	
					
			
			$moduledata['afiliados']=\DB::table('oli_user')			
			->select('identification','name','company','link','seat','phone','date')			
			->join('oli_company', 'company_id', '=', 'oli_company.id')
			->join('oli_link', 'link_id', '=', 'oli_link.id')
			->join('oli_seat', 'oli_company.seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->where(function ($query) {
				$query->where('oli_user.identification', 'like', Session::get('search'))
				->orWhere('oli_user.name', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_seat.seat', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_company.company', 'like', '%'.Session::get('search').'%');
			})
			->skip($request->input('start'))->take($request->input('length'))
			->get();
				
			$moduledata['filtro'] = count($moduledata['afiliados']);
			
			//registamos el log de acceso
			\DB::insert("INSERT INTO `oli_log` (
				`id`,
				`action`,
				`description`,
				`date`,
				`user_id`,
				`created_at`,
				`updated_at`) VALUES (
				NULL,
				'Busqueda',
				'Listar: ".$request->input('search')['value']."',
				'".date('Y-m-d')."',
				'".Session::get('opaplus.usuario.id')."',
				NULL,
				NULL)"
			);
			
		}else{	
			
			$moduledata['afiliados']=\DB::table('oli_user')
			->select('identification','name','company','link','seat','phone','date')
			->join('oli_company', 'company_id', '=', 'oli_company.id')
			->join('oli_link', 'link_id', '=', 'oli_link.id')
			->join('oli_seat', 'oli_company.seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->skip($request->input('start'))->take($request->input('length'))
			->get();
				
			$moduledata['filtro'] = $moduledata['total'];		
			
			//registamos el log de acceso
			\DB::insert("INSERT INTO `oli_log` (
				`id`,
				`action`,
				`description`,
				`date`,
				`user_id`,
				`created_at`,
				`updated_at`) VALUES (
				NULL,
				'Paginar',
				'Inicial: ".$request->input('start')." Final: ".$request->input('length')." Hora: ".date('H:I:s')."',
				'".date('Y-m-d')."',
				'".Session::get('opaplus.usuario.id')."',
				NULL,
				NULL)"
			);
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['afiliados']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null,$entidad=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
				
		//consultamos la sedes de usuario
		$seats = OlinSeat::
		join('oli_user_x_seat', 'oli_user_x_seat.seat_id', '=','oli_seat.id')
		->where('oli_user_x_seat.user_id', '=' ,  Session::get('opaplus.usuario.id'))
		->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
		->get();
		
		foreach ($seats as $seat){
			$seatss[$seat['id']] = $seat['seat'];
		}
		$moduledata['seats']=$seatss;
		$moduledata['entidad']=$entidad; //para saber la entidad
		
		return Redirect::to('afiliados/agregar')->with('modulo',$moduledata);
	}
	public function getAgregar(){		
		
		return view('olin.user.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){	
						
		//sedes
		$seats = OlinSeat::where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))->get();
		
		foreach ($seats as $seat){
			$seatss[$seat['id']] = $seat['seat'];
		}
		$moduledata['seats']=$seatss;		
		
		$file = array('carga' => Input::file('carga'));
		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener maximo :max. caracteres',
			'mimes' => 'La :attribute debe ser de tipo csv',
		];
		$rules1 = array(			
			'com_id' => 'required',
			'link_id' => 'required',
			'seat_id' => 'required',
		);
		$rules2 = array(
			'carga' => 'required|mimes:text/csv,csv,txt',				
		);	
		
		$validator1 = Validator::make($request->input(), $rules1,$messages);
		$validator2 = Validator::make($file, $rules2,$messages);
		
		if ($validator1->fails()) {
			return Redirect::back()->withErrors($validator1)->with('modulo',$moduledata);;
		}	
		if ($validator2->fails()) {
			return Redirect::back()->withErrors($validator2)->with('modulo',$moduledata);;
		}
		
		//verificamos el campo de sobreescritura
		if(array_key_exists('check_carga', $request->input())){		
			\DB::table('oli_user')			
			->where('oli_user.company_id', '=' ,  $request->input()["com_id"])
			->where('oli_user.link_id', '=' ,  $request->input()["link_id"])
			->delete();	
		}		
		
		$contents = array();
		$today = date('Y-m-d');
		$inicio = date('G:i:s');		
		if ($request->file('carga')->isValid()) {
			$nro_user = 0;
			$index = 0;
			foreach(file($file['carga']->getPathname()) as $line) {	
				$user = explode(",",trim($line,"\n"));
				$contents[$index][] = array(
					'identification'=>$user[0],
					'name'=>$user[1],
					'date'=>$today,
					'company_id'=>$request->input()["com_id"],
					'link_id'=>$request->input()["link_id"]
				);
				
				if(count($contents[$index])>9999)$index++;
				$nro_user++;
			}
			try {
				foreach($contents as $array) {
					\DB::table('oli_user')->insert($array);
				}
				
				/*
				\DB::table('oli_user')->insert(array(
					'identification'=>$user[0],
					'name'=>$user[1],
					'date'=>$today,
					'company_id'=>$request->input()["com_id"],
					'link_id'=>$request->input()["link_id"]
				));
				*/
				/*
				\DB::insert("INSERT INTO `oli_user` (
					`id`,
					`identification`,
					`name`,
					`date`,
					`company_id`,
					`link_id`,
					`created_at`,
					`updated_at`) VALUES (
					NULL,
					'$user[0]',
					'$user[1]',
					'".$today."',
					'".$request->input()["com_id"]."',
					'".$request->input()["link_id"]."',
					NULL,
					NULL)"
				);
				*/
				/*
				OlinUser::create([
					'identification'=>$user[0],
					'name'=>$user[1],
					'date'=>$today,
					'company_id'=>$request->input()["com_id"],
					'link_id'=>$request->input()["link_id"]]
				);
				*/			
				
			}catch (\Illuminate\Database\QueryException $e) {
				$message = 'El Archivo no logro se cargar';
				return Redirect::to('afiliados/agregar')->with('error', $e->getMessage())->with('modulo',$moduledata);
			}			
					
			return Redirect::to('afiliados/agregar')->with('message', 'Archivo cargado exitosamente: '.$nro_user.' registros. Tiempo inicio: '.$inicio.' Final: '. date('G:i:s').' Total: '.date('i:s',strtotime(date('G:i:s'))-strtotime($inicio)))->with('modulo',$moduledata);	
		}
		return Redirect::to('afiliados/agregar')->with('error', 'Archivo no se cargado exitosamente')->with('modulo',$moduledata);
	}	
	
	public function postSaveseat(Request $request){
		//sedes
		$seats = OlinSeat::where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))->get();
		
		foreach ($seats as $seat){
			$seatss[$seat['id']] = $seat['seat'];
		}
		$moduledata['seats']=$seatss;
					
		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener maximo :max. caracteres',
			'mimes' => 'La :attribute debe ser de tipo xls',
		];
		$rules1 = array(
			'com_ide' => 'required',
			'link_ide' => 'required',
			'seat_ide' => 'required',
		);	
				
		$validator1 = Validator::make($request->input(), $rules1,$messages);
				
		if ($validator1->fails()) {
			return Redirect::back()->withErrors($validator1)->with('modulo',$moduledata);;
		}
		
		$mimeTypes = [
				'application/csv', 'application/excel',
				'application/vnd.ms-excel', 'application/vnd.msexcel',
				'text/csv', 'text/anytext', 'text/plain', 'text/x-c','text/csv','csv','txt','application/octet-stream',
				'text/comma-separated-values',
				'inode/x-empty',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		];
				
		$file = request()->hasFile('carga_seat');		
		if ($file) {
			$file = array('carga_seat' => Input::file('carga_seat'));
			if (!in_array(request()->file('carga_seat')->getClientMimeType(), $mimeTypes)) {
				return Redirect::back()->with('error', 'Archivo no se puede cargar, no es un archivo valido')->with('modulo',$moduledata);				
			}			
		}
				
		//llamado al metodo correspondiente
		//primero preguntamos si se tiene soporte	
		$metodo = 'getRefinator'.$request->input()['seat_ide'].''.$request->input()['com_ide'];	
		
		if(!method_exists($this,$metodo)){
			return Redirect::back()->with('message', 'El cargador de archivos aún no tiene soporte para la entidad')->with('modulo',$moduledata);
		}
		
		//verificamos el campo de sobreescritura
		if(array_key_exists('check_carga', $request->input())){
			\DB::table('oli_user')
			->where('oli_user.company_id', '=' ,  $request->input()["com_id"])
			->where('oli_user.link_id', '=' ,  $request->input()["link_id"])
			->delete();
		}
		
		//llamamos al metodo refinador				
		$inicio = date('G:i:s');
		$nro_user = $this->$metodo($request->input(),$file);		
 		if($nro_user > 0){
 			return Redirect::to('afiliados/agregar')->with('message', 'Archivo cargado exitosamente: '.$nro_user.' registros. Tiempo inicio: '.$inicio.' Final: '. date('G:i:s').' Total: '.date('i:s',strtotime(date('G:i:s'))-strtotime($inicio)))->with('modulo',$moduledata);
 		}
 		if($nro_user == -1){
 			return Redirect::to('afiliados/agregar')->with('error', 'El archivo no se cargo; lastimosamente es demasiado grande, debera refinarce manualmente')->with('modulo',$moduledata);
 		}
 		return Redirect::to('afiliados/agregar')->with('error', 'El archivo no se cargo')->with('modulo',$moduledata);
		
	}
	
	public function getRefinator12($inputs=null,$file = null){
			
		if ($file['carga_seat']->isValid()) {
		
			$com_id = $inputs["com_ide"];
			$link_id = $inputs["link_ide"];
			$today = date('Y-m-d');
			Session::flash('nro_user', 0);
				
			//hasta aqui el archivo puede ser .csv o xls
			$mimeTypes2 = [
				'application/excel',
				'application/vnd.ms-excel', 'application/vnd.msexcel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			];
				
			if (in_array(request()->file('carga_seat')->getClientMimeType(), $mimeTypes2)) {
				//solo archivos .xls pueden pasar
				if($file['carga_seat']->getClientSize() < 2097152){
					//.xls que se pueden cargar
					\Excel::filter('chunk')->load($file['carga_seat']->getPathname(),'UTF-8', true)->noHeading()->formatDates(FALSE)->chunk(7000, function($results) use($today, $com_id, $link_id){
						$olirefine = new OlinRefine();
							
						foreach($results as $row){
							// Creamos el array
							$filas[] = array(
								'identification'=>(int)$row->FkAsociado,
								'name'=>$olirefine->cero_espacios($row->Nombre),
								'date'=>$today,
								'company_id'=>$com_id,
								'link_id'=>$link_id
							);
							Session::flash('nro_user', Session::get('nro_user') + 1);
						}
							
						try {
							\DB::table('oli_user')->insert($filas);
						}catch (\Illuminate\Database\QueryException $e) {
							$message = 'El Archivo no logro se cargar';
							return 0;
						}							
							
					});
						return Session::get('nro_user');
				}else{
					//.xls que solo se pueden refinar
					//pero no todos los archivos se pueden refinar
					if($file['carga_seat']->getClientSize() < 3670016){
						$olirefine = new OlinRefine();
						$dates = \Excel::load($file['carga_seat']->getPathname(), function($reader) {
							$reader->select(array('FkAsociado','Nombre'))->get();
						})->get();
						$i=0;
						foreach ($dates->all() as $date){
							$filas[$i][0] = $date->all()['FkAsociado'];
							$filas[$i][1] = $olirefine->cero_espacios($date->all()['Nombre']);
							$i++;
						}
		
						\Excel::create($file['carga_seat']->getFilename(), function($excel) use ($filas){
							$excel->sheet('Datos', function($sheet) use($filas) {
								$sheet->fromArray($filas, null, 'A1', true,false);
							});
						})->export('csv');
					}
					return -1;
				}
			}else{
				//aqui .csv vive
				$contents = array();
				$index = 0;
				foreach(file($file['carga_seat']->getPathname()) as $line) {
		
					$user = explode(",",trim($line,"\n"));
		
					$contents[$index][] = array(
						'identification'=>$user[0],
						'name'=>$user[1],
						'date'=>$today,
						'company_id'=>$com_id,
						'link_id'=>$link_id
					);
		
					if(count($contents[$index])>9999)$index++;
					Session::flash('nro_user', Session::get('nro_user') + 1);
				}
				try {
					foreach($contents as $array) {
						\DB::table('oli_user')->insert($array);
					}
				}catch (\Illuminate\Database\QueryException $e) {
					return 0;
				}
				return Session::get('nro_user');
			}
		}
		
		return 0;		
		
	}
	
	public function getRefinator13($inputs=null,$file = null){
		
		if ($file['carga_seat']->isValid()) {
		
			$com_id = $inputs["com_ide"];
			$link_id = $inputs["link_ide"];
			$today = date('Y-m-d');
			Session::flash('nro_user', 0);
		
			//hasta aqui el archivo puede ser .csv o xls
			$mimeTypes2 = [
				'application/excel',
				'application/vnd.ms-excel', 'application/vnd.msexcel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			];
		
			if (in_array(request()->file('carga_seat')->getClientMimeType(), $mimeTypes2)) {
				//solo archivos .xls pueden pasar
				if($file['carga_seat']->getClientSize() < 2097152){
					//.xls que se pueden cargar
					\Excel::filter('chunk')->load($file['carga_seat']->getPathname(),'UTF-8', true)->noHeading()->formatDates(FALSE)->chunk(7000, function($results) use($today, $com_id, $link_id){
						$olirefine = new OlinRefine();							
						foreach($results as $hoja){
							// Creamos el array
							foreach($hoja as $row){
								$filas[] = array(
									'identification'=>(int)$row->cedula,
									'name'=>$olirefine->cero_espacios($row->nombre),
									'date'=>$today,
									'company_id'=>$com_id,
									'link_id'=>$link_id
								);
								Session::flash('nro_user', Session::get('nro_user') + 1);
							}
						}
							
						try {
							\DB::table('oli_user')->insert($filas);
						}catch (\Illuminate\Database\QueryException $e) {
							$message = 'El Archivo no logro se cargar';
							return 0;
						}	
					});
					
					return Session::get('nro_user');
					
				}else{
					//.xls que solo se pueden refinar
					//pero no todos los archivos se pueden refinar
					//este archivo no se puede refinar porque tiene el archivo tres hojas
					return -1;
				}
			}else{
				//aqui .csv vive
				$contents = array();
				$index = 0;
				foreach(file($file['carga_seat']->getPathname()) as $line) {
		
					$user = explode(",",trim($line,"\n"));
		
					$contents[$index][] = array(
						'identification'=>$user[0],
						'name'=>$user[1],
						'date'=>$today,
						'company_id'=>$com_id,
						'link_id'=>$link_id
					);
		
					if(count($contents[$index])>9999)$index++;
					Session::flash('nro_user', Session::get('nro_user') + 1);
				}
				try {
					foreach($contents as $array) {
						\DB::table('oli_user')->insert($array);
					}
				}catch (\Illuminate\Database\QueryException $e) {
					return 0;
				}
				return Session::get('nro_user');
			}
		}		
		return 0;		
	}
	
	public function getRefinator14($inputs=null,$file = null){
		
		//este archivo no necesita refinado ya que practicamente esta refinada.
		$contents = array();
		$today = date('Y-m-d');
		$inicio = date('G:i:s');		
		if ($file['carga_seat']->isValid()) {
			$nro_user = 0;
			$index = 0;
			$olirefine = new OlinRefine();
			
			foreach(file($file['carga_seat']->getPathname()) as $line) {
							
				$contents[$index][] = array(
					'identification'=>$olirefine->cero_espacios(substr(($line),0,12)),
					'name'=>$olirefine->cero_espacios(substr(($line),13,24)).' '.$olirefine->cero_espacios(substr(($line),37,25)),
					'date'=>$today,
					'company_id'=>$inputs["com_ide"],
					'link_id'=>$inputs["link_ide"]
				);
			
				if(count($contents[$index])>9999)$index++;
				$nro_user++;				
			}
			
		try {			
			foreach($contents as $array) {
				\DB::table('oli_user')->insert($array);
			}	
		}catch (\Illuminate\Database\QueryException $e) {
			$message = 'El Archivo no logro se cargar';
			return 0;
		}
			
		}
		
		return $nro_user;
	}
	
	public function getRefinator15($inputs=null,$file = null){
		
		if ($file['carga_seat']->isValid()) {
		
			$com_id = $inputs["com_ide"];
			$link_id = $inputs["link_ide"];
			$today = date('Y-m-d');
			Session::flash('nro_user', 0);
			
			//hasta aqui el archivo puede ser .csv o xls		
			$mimeTypes2 = [
				'application/excel',
				'application/vnd.ms-excel', 'application/vnd.msexcel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			];
			
			if (in_array(request()->file('carga_seat')->getClientMimeType(), $mimeTypes2)) {
				//solo archivos .xls pueden pasar
				if($file['carga_seat']->getClientSize() < 2097152){
					//.xls que se pueden cargar 
					\Excel::filter('chunk')->load($file['carga_seat']->getPathname(),'UTF-8', true)->noHeading()->formatDates(FALSE)->chunk(7000, function($results) use($today, $com_id, $link_id){
						$olirefine = new OlinRefine();
					
						foreach($results as $row){
							// Creamos el array
							$filas[] = array(
								'identification'=>(int)$row->cedula,
								'name'=>$olirefine->cero_espacios($row->nombre),
								'date'=>$today,
								'company_id'=>$com_id,
								'link_id'=>$link_id
							);
							Session::flash('nro_user', Session::get('nro_user') + 1);
						}
					
						try {
							\DB::table('oli_user')->insert($filas);
						}catch (\Illuminate\Database\QueryException $e) {
							$message = 'El Archivo no logro se cargar';
							return 0;
						}				
					});
						return Session::get('nro_user');
				}else{
					//.xls que solo se pueden refinar
					//pero no todos los archivos se pueden refinar
					if($file['carga_seat']->getClientSize() < 3670016){
						$olirefine = new OlinRefine();
						$dates = \Excel::load($file['carga_seat']->getPathname(), function($reader) {
							$reader->select(array('cedula','nombre'))->get();
						})->get();
						$i=0;
						foreach ($dates->all() as $date){			
							$filas[$i][0] = $date->all()['cedula'];
							$filas[$i][1] = $olirefine->cero_espacios($date->all()['nombre']);
							$i++;
						}
						
						\Excel::create($file['carga_seat']->getFilename(), function($excel) use ($filas){
							$excel->sheet('Datos', function($sheet) use($filas) {
								$sheet->fromArray($filas, null, 'A1', true,false);
							});
						})->export('csv');						
					}
					return -1;				
				}
			}else{
				//aqui .csv vive
				$contents = array();				
				$index = 0;
				foreach(file($file['carga_seat']->getPathname()) as $line) {
						
					$user = explode(",",trim($line,"\n"));
						
					$contents[$index][] = array(
						'identification'=>$user[0],
						'name'=>$user[1],
						'date'=>$today,
						'company_id'=>$com_id,
						'link_id'=>$link_id
					);
						
					if(count($contents[$index])>9999)$index++;
					Session::flash('nro_user', Session::get('nro_user') + 1);
				}
				try {
					foreach($contents as $array) {
						\DB::table('oli_user')->insert($array);
					}
				}catch (\Illuminate\Database\QueryException $e) {
					return 0;
				}
				return Session::get('nro_user');
			}					
		}
		
		return 0;
	}
	
	public function getRefinator16($inputs=null,$file = null){
	
		if ($file['carga_seat']->isValid()) {
	
			$com_id = $inputs["com_ide"];
			$link_id = $inputs["link_ide"];
			$today = date('Y-m-d');
			Session::flash('nro_user', 0);
				
			//hasta aqui el archivo puede ser .csv o xls
			$mimeTypes2 = [
				'application/excel',
				'application/vnd.ms-excel', 'application/vnd.msexcel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			];
				
			if (in_array(request()->file('carga_seat')->getClientMimeType(), $mimeTypes2)) {
				//solo archivos .xls pueden pasar
				if($file['carga_seat']->getClientSize() < 2097152){
					//.xls que se pueden cargar
					\Excel::filter('chunk')->load($file['carga_seat']->getPathname(),'UTF-8', true)->noHeading()->formatDates(FALSE)->chunk(7000, function($results) use($today, $com_id, $link_id){
						$olirefine = new OlinRefine();							
						foreach($results as $row){
							// Creamos el array
							$filas[] = array(
								'identification'=>(int)$row->nit,
								'name'=>$olirefine->cero_espacios($row->nomapel),
								'date'=>$today,
								'company_id'=>$com_id,
								'link_id'=>$link_id
							);
							Session::flash('nro_user', Session::get('nro_user') + 1);
						}
							
						try {
							\DB::table('oli_user')->insert($filas);
						}catch (\Illuminate\Database\QueryException $e) {
							$message = 'El Archivo no logro se cargar';
							return 0;
						}
					});
						return Session::get('nro_user');
				}else{
					//.xls que solo se pueden refinar
					//pero no todos los archivos se pueden refinar
					if($file['carga_seat']->getClientSize() < 3670016){
						$olirefine = new OlinRefine();
						$dates = \Excel::load($file['carga_seat']->getPathname(), function($reader) {
							$reader->select(array('nit','nomapel'))->get();
						})->get();
						$i=0;
						foreach ($dates->all() as $date){
							$filas[$i][0] = $date->all()['nit'];
							$filas[$i][1] = $olirefine->cero_espacios($date->all()['nomapel']);
							$i++;
						}
	
						\Excel::create($file['carga_seat']->getFilename(), function($excel) use ($filas){
							$excel->sheet('Datos', function($sheet) use($filas) {
								$sheet->fromArray($filas, null, 'A1', true,false);
							});
						})->export('csv');
					}
					return -1;
				}
			}else{
				//aqui .csv vive
				$contents = array();
				$index = 0;
				foreach(file($file['carga_seat']->getPathname()) as $line) {
	
					$user = explode(",",trim($line,"\n"));
	
					$contents[$index][] = array(
						'identification'=>$user[0],
						'name'=>$user[1],
						'date'=>$today,
						'company_id'=>$com_id,
						'link_id'=>$link_id
					);
	
					if(count($contents[$index])>9999)$index++;
					Session::flash('nro_user', Session::get('nro_user') + 1);
				}
				try {
					foreach($contents as $array) {
						\DB::table('oli_user')->insert($array);
					}
				}catch (\Illuminate\Database\QueryException $e) {
					return 0;
				}
				return Session::get('nro_user');
			}
		}
	
		return 0;
	}
	
	public function getRefinator17($inputs=null,$file = null){
	
		if ($file['carga_seat']->isValid()) {
	
			$com_id = $inputs["com_ide"];
			$link_id = $inputs["link_ide"];
			$today = date('Y-m-d');
			Session::flash('nro_user', 0);
	
			//hasta aqui el archivo puede ser .csv o xls
			$mimeTypes2 = [
					'application/excel',
					'application/vnd.ms-excel', 'application/vnd.msexcel',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			];
	
			if (in_array(request()->file('carga_seat')->getClientMimeType(), $mimeTypes2)) {
				//solo archivos .xls pueden pasar
				if($file['carga_seat']->getClientSize() < 2097152){
					//.xls que se pueden cargar
					
					$dates = \Excel::load($file['carga_seat']->getPathname(), function($reader) {
						//$reader->select(array(1,2,3))->get();
						$reader->noHeading();
					})->get();
					
					$olirefine = new OlinRefine();
					$i=0;
					foreach ($dates->all() as $date){					
						$filas[] = array(
							'identification'=>(int)$date->all()[2],
							'name'=>$olirefine->cero_espacios($date->all()[4].' '.$date->all()[3]),
							'date'=>$today,
							'company_id'=>$com_id,
							'link_id'=>$link_id
						);
						$i++;
					}
					
					try {
						\DB::table('oli_user')->insert($filas);
					}catch (\Illuminate\Database\QueryException $e) {
						$message = 'El Archivo no logro se cargar';
						return 0;
					}					
					
					return $i;					
					
				}else{
					//.xls que solo se pueden refinar
					//pero no todos los archivos se pueden refinar
					if($file['carga_seat']->getClientSize() < 3670016){
						$olirefine = new OlinRefine();
						$dates = \Excel::load($file['carga_seat']->getPathname(), function($reader) {
							$reader->noHeading();
						})->get();
						$i=0;
						foreach ($dates->all() as $date){
							$filas[$i][0] = (int)$date->all()[2];
							$filas[$i][1] = $olirefine->cero_espacios($date->all()[4].' '.$date->all()[3]);
							$i++;
						}
	
						\Excel::create($file['carga_seat']->getFilename(), function($excel) use ($filas){
							$excel->sheet('Datos', function($sheet) use($filas) {
								$sheet->fromArray($filas, null, 'A1', true,false);
							});
						})->export('csv');
					}
					return -1;
				}
			}else{
				//aqui .csv vive
				$contents = array();
				$index = 0;
				foreach(file($file['carga_seat']->getPathname()) as $line) {
	
					$user = explode(",",trim($line,"\n"));
	
					$contents[$index][] = array(
						'identification'=>$user[0],
						'name'=>$user[1],
						'date'=>$today,
						'company_id'=>$com_id,
						'link_id'=>$link_id
					);
	
					if(count($contents[$index])>9999)$index++;
					Session::flash('nro_user', Session::get('nro_user') + 1);
				}
				try {
					foreach($contents as $array) {
						\DB::table('oli_user')->insert($array);
					}
				}catch (\Illuminate\Database\QueryException $e) {
					return 0;
				}
				return Session::get('nro_user');
			}
		}
	
		return 0;
	}
	
	public function getRefinator18($inputs=null,$file = null){
	
		if ($file['carga_seat']->isValid()) {
	
			$com_id = $inputs["com_ide"];
			$link_id = $inputs["link_ide"];
			$today = date('Y-m-d');
			Session::flash('nro_user', 0);
	
			//hasta aqui el archivo puede ser .csv o xls
			$mimeTypes2 = [
				'application/excel',
				'application/vnd.ms-excel', 'application/vnd.msexcel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			];
	
			if (in_array(request()->file('carga_seat')->getClientMimeType(), $mimeTypes2)) {
				//solo archivos .xls pueden pasar
				if($file['carga_seat']->getClientSize() < 2097152){
					//.xls que se pueden cargar
					\Excel::filter('chunk')->load($file['carga_seat']->getPathname(),'UTF-8', true)->noHeading()->formatDates(FALSE)->chunk(7000, function($results) use($today, $com_id, $link_id){
						$olirefine = new OlinRefine();
						foreach($results as $row){
							// Creamos el array
							$filas[] = array(
								'identification'=>(int)$row->ident,
								'name'=>$olirefine->cero_espacios($row->nombre),
								'date'=>$today,
								'company_id'=>$com_id,
								'link_id'=>$link_id
							);
							Session::flash('nro_user', Session::get('nro_user') + 1);
						}
							
						try {
							\DB::table('oli_user')->insert($filas);
						}catch (\Illuminate\Database\QueryException $e) {
							$message = 'El Archivo no logro se cargar';
							return 0;
						}
					});
						return Session::get('nro_user');
				}else{
					//.xls que solo se pueden refinar
					//pero no todos los archivos se pueden refinar
					if($file['carga_seat']->getClientSize() < 3670016){
						$olirefine = new OlinRefine();
						$dates = \Excel::load($file['carga_seat']->getPathname(), function($reader) {
							$reader->select(array('ident','nombre'))->get();
						})->get();
						$i=0;
						foreach ($dates->all() as $date){
							$filas[$i][0] = $date->all()['ident'];
							$filas[$i][1] = $olirefine->cero_espacios($date->all()['nombre']);
							$i++;
						}
	
						\Excel::create($file['carga_seat']->getFilename(), function($excel) use ($filas){
							$excel->sheet('Datos', function($sheet) use($filas) {
								$sheet->fromArray($filas, null, 'A1', true,false);
							});
						})->export('csv');
					}
					return -1;
				}
			}else{
				//aqui .csv vive
				$contents = array();
				$index = 0;
				foreach(file($file['carga_seat']->getPathname()) as $line) {
	
					$user = explode(",",trim($line,"\n"));
	
					$contents[$index][] = array(
						'identification'=>$user[0],
						'name'=>$user[1],
						'date'=>$today,
						'company_id'=>$com_id,
						'link_id'=>$link_id
					);
	
					if(count($contents[$index])>9999)$index++;
					Session::flash('nro_user', Session::get('nro_user') + 1);
				}
				try {
					foreach($contents as $array) {
						\DB::table('oli_user')->insert($array);
					}
				}catch (\Illuminate\Database\QueryException $e) {
					return 0;
				}
				return Session::get('nro_user');
			}
		}
	
		return 0;
	}
	
	public function getRefinator19($inputs=null,$file = null){
	
		if ($file['carga_seat']->isValid()) {
	
			$com_id = $inputs["com_ide"];
			$link_id = $inputs["link_ide"];
			$today = date('Y-m-d');
			Session::flash('nro_user', 0);
	
			//hasta aqui el archivo puede ser .csv o xls
			$mimeTypes2 = [
				'application/excel',
				'application/vnd.ms-excel', 'application/vnd.msexcel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			];
	
			if (in_array(request()->file('carga_seat')->getClientMimeType(), $mimeTypes2)) {
				//solo archivos .xls pueden pasar
				if($file['carga_seat']->getClientSize() < 2097152){
					//.xls que se pueden cargar
					\Excel::filter('chunk')->load($file['carga_seat']->getPathname(),'UTF-8', true)->noHeading()->formatDates(FALSE)->chunk(7000, function($results) use($today, $com_id, $link_id){
						$olirefine = new OlinRefine();
						foreach($results as $row){
							// Creamos el array
							$filas[] = array(
									'identification'=>(int)$row->cedula,
									'name'=>$olirefine->cero_espacios($row->nombre),
									'date'=>$today,
									'company_id'=>$com_id,
									'link_id'=>$link_id
							);
							Session::flash('nro_user', Session::get('nro_user') + 1);
						}
							
						try {
							\DB::table('oli_user')->insert($filas);
						}catch (\Illuminate\Database\QueryException $e) {
							$message = 'El Archivo no logro se cargar';
							return 0;
						}
					});
						return Session::get('nro_user');
				}else{
					//.xls que solo se pueden refinar
					//pero no todos los archivos se pueden refinar
					if($file['carga_seat']->getClientSize() < 3670016){
						$olirefine = new OlinRefine();
						$dates = \Excel::load($file['carga_seat']->getPathname(), function($reader) {
							$reader->select(array('cedula','nombre'))->get();
						})->get();
						$i=0;
						foreach ($dates->all() as $date){
							$filas[$i][0] = $date->all()['cedula'];
							$filas[$i][1] = $olirefine->cero_espacios($date->all()['nombre']);
							$i++;
						}
	
						\Excel::create($file['carga_seat']->getFilename(), function($excel) use ($filas){
							$excel->sheet('Datos', function($sheet) use($filas) {
								$sheet->fromArray($filas, null, 'A1', true,false);
							});
						})->export('csv');
					}
					return -1;
				}
			}else{
				//aqui .csv vive
				$contents = array();
				$index = 0;
				foreach(file($file['carga_seat']->getPathname()) as $line) {
	
					$user = explode(",",trim($line,"\n"));
	
					$contents[$index][] = array(
							'identification'=>$user[0],
							'name'=>$user[1],
							'date'=>$today,
							'company_id'=>$com_id,
							'link_id'=>$link_id
					);
	
					if(count($contents[$index])>9999)$index++;
					Session::flash('nro_user', Session::get('nro_user') + 1);
				}
				try {
					foreach($contents as $array) {
						\DB::table('oli_user')->insert($array);
					}
				}catch (\Illuminate\Database\QueryException $e) {
					return 0;
				}
				return Session::get('nro_user');
			}
		}
	
		return 0;
	}
	
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Identificación','Nombre','Empresa','Vinculo','Teléfono','Sede','Fecha Actualización'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo		
		//$moduledata['modulo'] = $url[count($url)-4];
		//$moduledata['id_app'] = $url[count($url)-2];
		//$moduledata['categoria'] = $url[count($url)-1];
		//$moduledata['id_mod'] = $url[count($url)-5];
		
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		
		//registamos el log de acceso
		\DB::insert("INSERT INTO `oli_log` (
			`id`,
			`action`,
			`date`,
			`user_id`,
			`created_at`,
			`updated_at`) VALUES (
			NULL,
			'Busqueda: ".$request->input()['names']."',
			'".date('Y-m-d')."',
			'".Session::get('opaplus.usuario.id')."',
			NULL,
			NULL)");
		
		return view('olin.user.listar');
		
	}
	
	public function postSelect(Request $request){
		
		if($request->input()["select"] == "id_seat" || $request->input()["select"] == "id_seate"){
			//select de sedes
			if($request->input()["id"]){
				//seleccionamos las empresas con sede correspondiente
				$comps = OlinCompany::select('id','company')
				->where('oli_company.seat_id', (int)$request->input()['id'])
				->where('oli_company.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
				->get();						
				return response()->json(['respuesta'=>true,'select'=>$request->input()["select"],'data'=>$comps]);
			}else{
				//se ha seleccionado el option con valor 0
				return response()->json(['respuesta'=>true,'select'=>$request->input()["select"],'data'=>null]);
			}			
			
		}
		
		if($request->input()["select"] == "id_company" || $request->input()["select"] == "id_companye"){
			//select de sedes
			if($request->input()["id"]){
				//seleccionamos los vinculos correspondiente			
				$links =
				OlinCompany::
				select('oli_link.id','oli_link.link')
				->join('oli_link_x_company','oli_company.id','=','oli_link_x_company.company_id')
				->join('oli_link', 'oli_link_x_company.link_id', '=', 'oli_link.id')
				->where('oli_company.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
				->where('oli_link.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
				->where('oli_company.id', (int)$request->input()['id'])
				->get();	
				
				return response()->json(['respuesta'=>true,'select'=>$request->input()["select"],'data'=>$links]);
			}else{
				//se ha seleccionado el option con valor 0
				return response()->json(['respuesta'=>true,'select'=>$request->input()["select"],'data'=>null]);
			}
				
		}
		
		return response()->json(['respuesta'=>false,'data'=>null]);
	}
	
	public function getEliminar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
	
		//sedes
		$seats = OlinSeat::where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))->get();
		
		foreach ($seats as $seat){
			$seatss[$seat['id']] = $seat['seat'];
		}
		$moduledata['seats']=$seatss;
	
		return Redirect::to('afiliados/borrar')->with('modulo',$moduledata);
	}
	
	public function getBorrar(){
		return view('olin.user.borrar');
	}
	
	public function postDelete(Request $request){
		
		//sedes
		$seats = OlinSeat::where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))->get();
		
		foreach ($seats as $seat){
			$seatss[$seat['id']] = $seat['seat'];
		}
		$moduledata['seats']=$seatss;
		$inicio = date('G:i:s');
		$nro_users = 0;
		if(array_key_exists('all',$request->input())){
			//se limpiaratoda la base de datos
			if(!$request->input()['seat_id']){				
				$nro_users=\DB::table('oli_user')->delete();
			}
			return Redirect::to('afiliados/borrar')->with('message', 'Todos datos se limpiaron exitosamente: '. $nro_users.' Tiempo transcurrido: '.date('i:s',strtotime(date('G:i:s'))-strtotime($inicio)))->with('modulo',$moduledata);
		};
		
		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener maximo :max. caracteres',
			'mimes' => 'La :attribute debe ser de tipo csv',
		];
		$rules = array(
			'com_id' => 'required',
			'link_id' => 'required',
			'seat_id' => 'required',
		);
		
		$validator = Validator::make($request->input(), $rules,$messages);		
		
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput()->with('modulo',$moduledata);;
		}
		else{			
			$nro_users = \DB::table('oli_user')
			->where('oli_user.company_id', '=' ,  $request->input()["com_id"])
			->where('oli_user.link_id', '=' ,  $request->input()["link_id"])
			->delete();
			if($nro_users){
				return Redirect::to('afiliados/borrar')->with('message', 'Los datos se limpiaron exitosamente: '. $nro_users.' Tiempo treancurrido: '.date('i:s',strtotime(date('G:i:s'))-strtotime($inicio)) )->with('modulo',$moduledata);
			}
			return Redirect::to('afiliados/borrar')->with('message', 'Para la configuración ingresada, no hay datos que limpiar')->with('modulo',$moduledata);
			
		}
		
		return Redirect::to('afiliados/borrar')->with('error', 'Los datos no se lograron limpiar')->with('modulo',$moduledata);
		
		
	}
	
}
