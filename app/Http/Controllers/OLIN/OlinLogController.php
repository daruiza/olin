<?php namespace App\Http\Controllers\OLIN;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\OLIN\OlinLog;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class OlinLogController extends Controller {
	
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
		
		//consultas de logs
		//total
		try {
			$moduledata['total_logs']=\DB::table('oli_log')
			->select(\DB::raw('count(*) as total'))			
			->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('logs/general')->with('error', $message);
		}
		//total agrupado por accion		
		try {
			$moduledata['logs']=\DB::table('oli_log')
			->select('action', \DB::raw('count(*) as total'))			
			->groupBy('action')
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo.' de usuario';
			return Redirect::to('logs/general')->with('error', $message);
		}	
		
		return Redirect::to('logs/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('olin.log.log_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('logs/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Usuario','Acción','Descripción','Fecha'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		Session::flash('modulo', $moduledata);
		return view('olin.log.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=OlinLog::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['logs']=
			OlinLog::
			select('name','action','description','date')
			->join('seg_user', 'user_id', '=', 'seg_user.id')
			->where('seg_user.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->where(function ($query) {
				$query->where('seg_user.name', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_log.action', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_log.date', 'like', '%'.Session::get('search').'%');
				
			})
			->skip($request->input('start'))->take($request->input('length'))
			->orderBy('oli_log.date','desc')
			->get();		
			$moduledata['filtro'] = count($moduledata['logs']);
		}else{			
			$moduledata['logs']=\DB::table('oli_log')
			->select('name','action','description','date')
			->join('seg_user', 'user_id', '=', 'seg_user.id')
			->where('seg_user.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->skip($request->input('start'))->take($request->input('length'))
			->orderBy('oli_log.date','desc')
			->get();
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['logs']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
			
		return Redirect::to('rol/agregar');
	}
	public function getAgregar(){
	
		return view('seguridad.rol.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){
		
		$messages = [
				'required' => 'El campo :attribute es requerido.',				
		];
		
		$rules = array(
				'rol'    => 'required',
				'description' => 'required',				
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			
			$rol = new Rol();
			
			$rol->rol = $request->input()['rol'];
			$rol->description = $request->input()['description'];
						
			if($request->input()['edit']){
				//se pretende actualizar el rol				
				try {
					$rolAffectedRows = Rol::where('id', $request->input()['rol_id'])->update(array('rol' => $rol->rol,'description' => $rol->description));
					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'El rol o no se logro editar';
					return Redirect::to('rol/agregar')->with('error', $message)->withInput();
				}
				
				Session::flash('_old_input.rol', $rol[0]['rol']);
				Session::flash('_old_input.description', $rol[0]['description']);
				Session::flash('_old_input.rol_id', $request->input()['rol_id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');
				
				return Redirect::to('rol/agregar')->withInput()->with('message', 'Rol editado exitosamente');
				
			}else{
				try {					
					$rol->save();
					return Redirect::to('rol/agregar')->withInput()->with('message', 'Rol agregado exitosamente');
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'El rol no se logro agregar';					
					return Redirect::to('rol/agregar')->with('error', $e->getMessage())->withInput();
				}				
			}		
		}
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
				
		$rol =
		Rol::
		where('seg_rol.id', $id)
		->get()
		->toArray();
		
		Session::flash('_old_input.rol', $rol[0]['rol']);
		Session::flash('_old_input.description', $rol[0]['description']);		
		Session::flash('_old_input.rol_id', $id);
		Session::flash('_old_input.edit', true);
		Session::flash('titulo', 'Editar');
		
		return Redirect::to('rol/agregar');
	}
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Usuario','Acción','Descripción','Fecha'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('olin.log.listar');
		
	}
	
}
