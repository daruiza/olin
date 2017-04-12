<?php namespace App\Http\Controllers\Security;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Security\Rol;
use App\Core\Security\Permit;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class PermisoController extends Controller {
	
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
		
		$permisos = array();
		//permisos
		try {
			$permisos=\DB::table('seg_permit')
			->select('rol', 'module', 'option')
			->join('seg_rol', 'rol_id', '=', 'seg_rol.id')
			->join('seg_module', 'module_id', '=', 'seg_module.id')
			->join('seg_option', 'option_id', '=', 'seg_option.id')			
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de las '.$modulo;
			return Redirect::to('permiso/general')->with('error', $message);
		}
		
		$datos = Array('categoria'=>array(),'datos'=>array());
		foreach($permisos as $value){
			//categorias
			if(!(in_array($value->rol,$datos['categoria']))){
				$datos['categoria'][] = $value->rol;
			}
			//datos
			if(!(key_exists($value->module,$datos['datos']))){
				$datos['datos'][$value->module]=array();
			}
			if(!(key_exists($value->rol,$datos['datos'][$value->module]))){
				$datos['datos'][$value->module][$value->rol]=1;
			}else $datos['datos'][$value->module][$value->rol]++;
		}
		
		$aux_datos = array();
		foreach ($datos['datos'] as $key=>$value){
			$aux_datos[$key] = array();;
			foreach ($value as $val){
				$aux_datos[$key][] = $val;
			}
		}
		$datos['datos'] = $aux_datos;
		
		//permiso total		
		$moduledata['total_permisos']=count($permisos);
		//total agrupado por roles
		try {
			$moduledata['permisos']=\DB::table('seg_permit')
			->select('rol', \DB::raw('count(*) as total'))
			->join('seg_rol', 'rol_id', '=', 'seg_rol.id')			
			->groupBy('seg_permit.rol_id')
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de las '.$modulo;
			return Redirect::to('permiso/general')->with('error', $message);
		}
		
		$moduledata['per']=$permisos;
		$moduledata['datos']=$datos;
		//dd($moduledata);	
		
		return Redirect::to('permiso/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('seguridad.permiso.permiso_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('permiso/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Rol','Modulo','Opción'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		Session::flash('modulo', $moduledata);
		return view('seguridad.permiso.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=Permit::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['permisos']=
			Permit::
			select('rol', 'module', 'option','rol_id','module_id','option_id')
			->join('seg_rol', 'rol_id', '=', 'seg_rol.id')
			->join('seg_module', 'module_id', '=', 'seg_module.id')
			->join('seg_option', 'option_id', '=', 'seg_option.id')
			->where(function ($query) {
				$query->where('seg_rol.rol', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_module.module', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_option.option', 'like', '%'.Session::get('search').'%');
			})
			->skip($request->input('start'))->take($request->input('length'))
			->get();		
			$moduledata['filtro'] = count($moduledata['permisos']);
		}else{
			$moduledata['permisos']=\DB::table('seg_permit')
			->select('rol', 'module', 'option','rol_id','module_id','option_id')
			->join('seg_rol', 'rol_id', '=', 'seg_rol.id')
			->join('seg_module', 'module_id', '=', 'seg_module.id')
			->join('seg_option', 'option_id', '=', 'seg_option.id')
			->skip($request->input('start'))->take($request->input('length'))->get();
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['permisos']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		
		$roles=Rol::select('id','rol')->get()->toArray();
		foreach ($roles as $rol){
			$rols[$rol['id']] = $rol['rol'];
		}
		$modulos=\DB::table('seg_module')->select('id','module')->get();
		foreach ($modulos as $module){
			$modules[$module->id] = $module->module;
		}
		$opciones=\DB::table('seg_option')->select('id','option')->get();
		foreach ($opciones as $option){
			$options[$option->id] = $option->option;
		}
		
		$moduledata['opciones']=$options;
		$moduledata['modulos']=$modules;
		$moduledata['roles']=$rols;
		
		return Redirect::to('permiso/agregar')->with('modulo',$moduledata);
	}
	public function getAgregar(){
	
		return view('seguridad.permiso.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){
		
		//preparación d edatos
		$roles=Rol::select('id','rol')->get()->toArray();
		foreach ($roles as $rol){
			$rols[$rol['id']] = $rol['rol'];
		}
		$modulos=\DB::table('seg_module')->select('id','module')->get();
		foreach ($modulos as $module){
			$modules[$module->id] = $module->module;
		}
		$opciones=\DB::table('seg_option')->select('id','option')->get();
		foreach ($opciones as $option){
			$options[$option->id] = $option->option;
		}
		
		$moduledata['opciones']=$options;
		$moduledata['modulos']=$modules;
		$moduledata['roles']=$rols;
		
		$messages = [
				'required' => 'El campo :attribute es requerido.',				
		];
		
		
		$rules = array(
				'rol'    => 'required',
				'module' => 'required',	
				'option' => 'required',
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput()->with('modulo',$moduledata);
		}else{
			
			$permit = new Permit();
			
			$permit->rol_id = $request->input()['rol'];
			$permit->module_id = $request->input()['module'];
			$permit->option_id = $request->input()['option'];						
			
			try {
				//primero verificamos la existencia del permiso 
				$permiso = Permit::
				where('rol_id', $permit->rol_id)
				->where('module_id', $permit->module_id)
				->where('option_id', $permit->option_id)
				->exists();
				
				if($permiso){
					return Redirect::to('permiso/agregar')->withInput()->with('message', 'Permiso agregado anteriormente')->with('modulo',$moduledata);;
				}					
				
				$permit->save();
				return Redirect::to('permiso/agregar')->withInput()->with('message', 'Permiso agregado exitosamente')->with('modulo',$moduledata);;
			}catch (\Illuminate\Database\QueryException $e) {
				$message = 'El permiso no se logro agregar';					
				return Redirect::to('permiso/agregar')->with('error', $e->getMessage())->withInput()->with('modulo',$moduledata);;
			}				
				
		}
		
	}
		
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Rol','Modulo','Opción'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('seguridad.permiso.listar');
		
	}
	
	public function postLugar(Request $request){
				
		//primero verificamos la existencia del permiso 
		try {
			$permiso = Permit::
			where('rol_id', $request->input()['rol_id'])
			->where('module_id', $request->input()['module_id'])
			->where('option_id', $request->input()['option_id'])
			->get();		
			
			
		}catch (ModelNotFoundException $e) {			
			return response()->json(['respuesta'=>false,'data'=>$e->getMessage()]);			
		}
		
		if($permiso){
			try {
				Permit::
				where('rol_id', $request->input()['rol_id'])
				->where('module_id', $request->input()['module_id'])
				->where('option_id', $request->input()['option_id'])
				->delete();					
					
			}catch (ModelNotFoundException $e) {
				return response()->json(['respuesta'=>false,'data'=>$e->getMessage()]);
			}
			return response()->json(['respuesta'=>true,'data'=>null]);
		}		
		
		return response()->json(['respuesta'=>false,'data'=>'El permiso fue borrado previamente']);
	}
	
	
}
