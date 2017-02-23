<?php namespace App\Http\Controllers\OLIN;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\OLIN\OlinSeat;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class OlinSeatController extends Controller {
	
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
		
		//consultas de sedes
		//total
		try {
			$moduledata['total_sedes']=\DB::table('oli_seat')
			->select(\DB::raw('count(*) as total'))	
			->where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('sedes/general')->with('error', $message);
		}
		//total agrupado por empresas
		try {
			$moduledata['empresas']=\DB::table('oli_company')
			->select('seat', \DB::raw('count(*) as total'))			
			->join('oli_seat', 'seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->groupBy('seat_id')
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo.' de usuario';
			return Redirect::to('afiliados/general')->with('error', $message);
		}
		
		
		return Redirect::to('sedes/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('olin.seat.seat_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('sedes/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Sede','Telefono','Descripción'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		Session::flash('modulo', $moduledata);
		return view('olin.seat.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=OlinSeat::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['sedes']=
			OlinSeat::
			where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->where(function ($query) {
				$query->where('oli_seat.seat', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_seat.phone', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_seat.description', 'like', '%'.Session::get('search').'%');				
			})
			->skip($request->input('start'))->take($request->input('length'))
			->get();		
			$moduledata['filtro'] = count($moduledata['sedes']);
		}else{			
			$moduledata['sedes']=\DB::table('oli_seat')
			->where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->skip($request->input('start'))->take($request->input('length'))->get();
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['sedes']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		$moduledata['id']=$id_mod;
		$moduledata['id_app']=$id_app;
		//consultar los usuarios con aplicación olin id = 2		
		try {
			$moduledata['users']=\DB::table('seg_user')			
			->join('seg_user_profile', 'seg_user_profile.user_id', '=', 'seg_user.id')
			->join('seg_app_x_user', 'seg_app_x_user.user_id', '=', 'seg_user.id')
			->where('seg_app_x_user.app_id', '=' , 2)
			->where('seg_user.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de Usuario';
			return Redirect::to('sedes/agregar')->with('error', $message);
		}
		
		return Redirect::to('sedes/agregar')->with('modulo',$moduledata);
	}
	public function getAgregar(){
	
		return view('olin.seat.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){
		
		//consultar los usuarios con aplicación olin id = 2
		try {
			$moduledata['users']=\DB::table('seg_user')
			->join('seg_user_profile', 'seg_user_profile.user_id', '=', 'seg_user.id')
			->join('seg_app_x_user', 'seg_app_x_user.user_id', '=', 'seg_user.id')
			->where('seg_app_x_user.app_id', '=' , 2)
			->where('seg_user.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de Usuario';
			return Redirect::to('sedes/agregar')->with('error', $message);
		}
		
		$messages = [
				'required' => 'El campo :attribute es requerido.',				
		];
		
		$rules = array(
				'seat'    => 'required',
				'phone' => 'required',				
		);
		
		$checkbox = Array();
		foreach($request->input() as $key=>$value){
			if(strpos($key,$request->input()['mod_id']) !== false) $checkbox[$key] = $value;
		}
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput()->with('modulo',$moduledata);;
		}else{
			
			$olinseat = new OlinSeat();
			
			$olinseat->seat = $request->input()['seat'];
			$olinseat->phone = $request->input()['phone'];
			$olinseat->description = $request->input()['description'];
						
			if($request->input()['edit']){
				//se pretende actualizar la sede				
				try {
					$seatAffectedRows = OlinSeat::where('id', $request->input()['seat_id'])->update(array('seat' => $olinseat->seat,'phone' => $olinseat->phone,'description' => $olinseat->description));
					
					//borramos todas las entradas de la empresa
					\DB::table('oli_user_x_seat')->where('seat_id', (int)$request->input()['seat_id'])->delete();
					//relacion los usuarios
					foreach($checkbox as $value){
						\DB::table('oli_user_x_seat')->insert(array(
							'user_id'=>(int)$value,
							'seat_id'=>(int)$request->input()['seat_id']
							)
						);
					}
					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'La sede no se logro editar';
					return Redirect::to('sedes/agregar')->with('error', $message)->withInput()->with('modulo',$moduledata);
				}
				
				//consultar los usuarios con de la sede
				try {
					$array=\DB::table('seg_user')
					->join('seg_user_profile', 'seg_user_profile.user_id', '=', 'seg_user.id')
					->join('seg_app_x_user', 'seg_app_x_user.user_id', '=', 'seg_user.id')
					->join('oli_user_x_seat', 'oli_user_x_seat.user_id', '=','seg_user.id')
					->where('seg_app_x_user.app_id', '=' , 2)
					->where('oli_user_x_seat.seat_id', '=' , (int)$request->input()['seat_id'])
					->where('seg_user.active', '=' , Session::get('opaplus.usuario.lugar.active'))
					->get();
				}catch (ModelNotFoundException $e) {
					$message = 'Problemas al hallar datos de Usuario';
					return Redirect::to('sedes/agregar')->with('error', $message);
				}
				
				$com_seats = Array();
				foreach($array as $value)$com_seats[]=$value->user_id;
				$moduledata['users_seats']=$com_seats;
				
				Session::flash('_old_input.seat', $olinseat->seat);
				Session::flash('_old_input.phone', $olinseat->phone);
				Session::flash('_old_input.description',$olinseat->description);
				Session::flash('_old_input.seat_id', $request->input()['seat_id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');
				
				return Redirect::to('sedes/agregar')->withInput()->with('message', 'Sede editada exitosamente')->with('modulo',$moduledata);
				
			}else{
				try {					
					$olinseat->save();	
					
					//relacion los usuarios
					foreach($checkbox as $value){
						\DB::table('oli_user_x_seat')->insert(array(
							'user_id'=>(int)$value,
							'seat_id'=>$olinseat->id
							)
						);
					}
					
					$url = explode("/", Session::get('_previous.url'));
					$moduledata['fillable'] = ['Sede','Telefono','Descripción'];
					//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
					$moduledata['modulo'] = $url[count($url)-5];
					$moduledata['id_app'] = $url[count($url)-3];
					$moduledata['categoria'] = $url[count($url)-2];
					$moduledata['id_mod'] = $url[count($url)-1];
						
					Session::flash('modulo', $moduledata);
					Session::flash('filtro', $request->input()['seat']);
					Session::flash('message', 'La sede se ha agregado exitosamente');
					
					return view('olin.seat.listar');
					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'La sede no se logro agregar';					
					return Redirect::to('sedes/agregar')->with('error', $e->getMessage())->withInput()->with('modulo',$moduledata);
				}				
			}		
		}
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		
		$moduledata['id']=$id_mod;
		$moduledata['id_app']=$id_app;
		
		$olinseat =
		OlinSeat::
		where('oli_seat.id', $id)
		->get()
		->toArray();
		
		//consultar los usuarios con aplicación olin id = 2
		try {
			$moduledata['users']=\DB::table('seg_user')
			->join('seg_user_profile', 'seg_user_profile.user_id', '=', 'seg_user.id')
			->join('seg_app_x_user', 'seg_app_x_user.user_id', '=', 'seg_user.id')
			->where('seg_app_x_user.app_id', '=' , 2)
			->where('seg_user.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de Usuario';
			return Redirect::to('sedes/agregar')->with('error', $message);
		}
		
		//consultar los usuarios con de la sede
		try {
			$array=\DB::table('seg_user')
			->join('seg_user_profile', 'seg_user_profile.user_id', '=', 'seg_user.id')
			->join('seg_app_x_user', 'seg_app_x_user.user_id', '=', 'seg_user.id')
			->join('oli_user_x_seat', 'oli_user_x_seat.user_id', '=','seg_user.id')
			->where('seg_app_x_user.app_id', '=' , 2)
			->where('oli_user_x_seat.seat_id', '=' , $id)
			->where('seg_user.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de Usuario';
			return Redirect::to('sedes/agregar')->with('error', $message);
		}
		
		$com_seats = Array();
		foreach($array as $value)$com_seats[]=$value->user_id;
		$moduledata['users_seats']=$com_seats;
		
		Session::flash('_old_input.seat', $olinseat[0]['seat']);
		Session::flash('_old_input.phone', $olinseat[0]['phone']);
		Session::flash('_old_input.description', $olinseat[0]['description']);		
		Session::flash('_old_input.seat_id', $id);
		Session::flash('_old_input.edit', true);
		Session::flash('titulo', 'Editar');
		
		return Redirect::to('sedes/agregar')->with('modulo',$moduledata);;
	}
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Sede','Telefono','Descripción'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('olin.seat.listar');
		
	}
	
	public function postVer(Request $request){
		
		//consulta de usuarios que tienen la sede
		try {
			$olinseat =
			\DB::table('seg_user')
			->join('seg_user_profile', 'seg_user_profile.user_id', '=', 'seg_user.id')
			->join('seg_app_x_user', 'seg_app_x_user.user_id', '=', 'seg_user.id')
			->join('oli_user_x_seat', 'oli_user_x_seat.user_id', '=','seg_user.id')
			->where('seg_app_x_user.app_id', '=' , 2)
			->where('oli_user_x_seat.seat_id', '=' , $request->input()['id'])
			->where('seg_user.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get();		
			
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo.' de usuario';
			return response()->json(['respuesta'=>false,'data'=>$e->getMessage()]);
		}
		
		if(count($olinseat)){
			return response()->json(['respuesta'=>true,'data'=>$olinseat]);
		}
		
		return response()->json(['respuesta'=>false,'data'=>null]);
	}
	
	public function postLugar(Request $request){
		/*Consultamos la sede
		 * */		
		$olinseat =
		OlinSeat::
		where('oli_seat.id', $request->input()['seat_id'])
		->get()
		->toArray();
		
		if(count($olinseat)){
			
			$olinseatAffectedRows = OlinSeat::where('id', $request->input()['seat_id'])->update(array('active' => $request->input()['activo']));
			
			if($olinseatAffectedRows){
				if($request->input()['activo']) return response()->json(['respuesta'=>true,'data'=>'La sede se restaurado adecuadamente, para administarla habra que ir hasta el escritorio','opp'=>$request->input()['activo']]);
				return response()->json(['respuesta'=>true,'data'=>'La sede se reciclo adecuadamente, para recuperarla habra que ir hasta la papelera','opp'=>$request->input()['activo']]);
			}
				
		}
		
		
		return response()->json(['respuesta'=>false,'data'=>'La sede no se logro reciclar']);
	}
	
	
	
}
