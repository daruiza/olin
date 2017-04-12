<?php namespace App\Http\Controllers\Security;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Security\Rol;
use App\Core\Security\Module;
use App\Core\Security\Aplications;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ModuloController extends Controller {
	
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
		
		//consulta de modulos
		//total
		try {		
			$moduledata['modulos'] =Module::
			where('seg_module.active', Session::get('opaplus.usuario.lugar.active'))
			->join('seg_app', 'seg_app.id', '=', 'seg_module.app_id')
			->orderBy('seg_module.app_id', 'asc')
			->get()->toArray();
			
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de modulo';
			return Redirect::to('modulo/general')->with('error', $message);
		}
		
		//creamos los datos 
		$datos = Array('categoria'=>array(),'datos'=>array());
		foreach ($moduledata['modulos'] as $value){
			if(!(in_array(json_decode($value['preference'])->categoria,$datos['categoria']))){
				$datos['categoria'][] = json_decode($value['preference'])->categoria;
			}		
		}
		
		//arreglamos un array para hacer el grafico de barras modulos, aplicaciones y categorias
		foreach ($moduledata['modulos'] as $value){
			foreach ($datos['categoria'] as $categoria){
				if(!(key_exists($value['app'],$datos['datos']))){
					$datos['datos'][$value['app']] = array();					
				}
				if(!(key_exists($categoria,$datos['datos'][$value['app']]))){
					//prefunta si es poseedor de la categoria
					if($categoria == json_decode($value['preference'])->categoria){
						$datos['datos'][$value['app']][$categoria] = 1;
					}else{
						$datos['datos'][$value['app']][$categoria] = 0;
					}				
				}else{
					//prefunta si es poseedor de la categoria
					if($categoria == json_decode($value['preference'])->categoria){
						$datos['datos'][$value['app']][$categoria]++;
					}
				}
				
			}			
		}		
		
		$aux = array();
		foreach ($datos['datos'] as $key => $value){
			foreach ($value as  $val){
				$aux[$key][]=$val;
			}
		}
		
		$datos['datos'] = $aux;		
		$moduledata['datos'] = $datos;
		
		$moduledata['total_modulos'] = count($moduledata['modulos']);
		$moduledata['total_categorias'] = count($datos['categoria']);
		
		//total agrupado por modulos
		try {
			$moduledata['aplicaciones']=\DB::table('seg_app')
            ->select('app', \DB::raw('count(*) as total'))
            ->join('seg_module', 'app_id', '=', 'seg_app.id')           
            ->groupBy('seg_app.id')
            ->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de las '.$modulo;
			return Redirect::to('modulo/general')->with('error', $message);
		}	
		
		return Redirect::to('modulo/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('seguridad.modulo.modulo_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('modulo/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Modulo','Categoria','Descripción','Aplicación','JS','Controlador','uiicono','icono'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
				
		Session::flash('modulo', $moduledata);
		return view('seguridad.modulo.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=Module::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['modulos']=
			Module::
			select('seg_module.id','seg_module.module','seg_module.preference','seg_module.description','seg_app.app')
			->where(function ($query) {
				$query->where('seg_module.module', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_module.description', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_module.preference', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_app.app', 'like', '%'.Session::get('search').'%');
			})
			->join('seg_app', 'seg_app.id', '=', 'seg_module.app_id')
			->orderBy('seg_module.app_id', 'asc')
			->skip($request->input('start'))->take($request->input('length'))
			->get();		
			$moduledata['filtro'] = count($moduledata['modulos']);
		}else{
			
			$moduledata['modulos'] =Module::
			select('seg_module.id','seg_module.module','seg_module.preference','seg_module.description','seg_app.app')
			->join('seg_app', 'seg_app.id', '=', 'seg_module.app_id')
			->orderBy('seg_module.app_id', 'asc')
			->skip($request->input('start'))->take($request->input('length'))
			->get()->toArray();
			
			$moduledata['filtro'] = $moduledata['total'];
		}
				
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['modulos']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		
		//preparación de los datos
		//consultamos las aplicaciones
		$apps = Aplications::all()->toArray();
		
		foreach ($apps as $app){
			$appes[$app['id']] = $app['app'];
		}
		$moduledata['apps']=$appes;
		
		return Redirect::to('modulo/agregar')->with('modulo',$moduledata);
	}
	public function getAgregar(){
	
		return view('seguridad.modulo.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){
		
		//preparación de los datos
		//consultamos las aplicaciones
		$apps = Aplications::all()->toArray();
		
		foreach ($apps as $app){
			$appes[$app['id']] = $app['app'];
		}
		$moduledata['apps']=$appes;
		
		$messages = [
				'required' => 'El campo :attribute es requerido.',				
		];
		
		$rules = array(
				'module'    => 'required',				
				'description' => 'required',
				'app_id' => 'required',
				'js' => 'required',
				'categoria' => 'required',
				'controlador' => 'required',
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput()->with('modulo',$moduledata);
		}else{
			
			$modulo = new Module();
			
			$modulo->module = $request->input()['module'];			
			$modulo->description = $request->input()['description'];
			$modulo->app_id = $request->input()['app_id'];
			
			$preferencias = array();
			$preferencias['js'] = $request->input()['js'];
			$preferencias['categoria'] = $request->input()['categoria'];
			$preferencias['controlador'] = $request->input()['controlador'];
			$preferencias['uiicono'] = $request->input()['uiicono'];
			$preferencias['icono'] = $request->input()['icono'];
			
			$modulo->preference = json_encode($preferencias);
						
			if($request->input()['edit']){
				//se pretende actualizar el rol				
				try {
					$ModuleAffectedRows = Module::where('id', $request->input()['modulo_id'])->update(array('module' => $modulo->module,'description' => $modulo->description, 'preference' => $modulo->preference, 'app_id' => $modulo->app_id));
					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'El Modulo no se logro editar';
					return Redirect::to('modulo/agregar')->with('error', $message)->withInput()->with('modulo',$moduledata);;
				}		
				
				return Redirect::to('modulo/agregar')->withInput()->with('message', 'Modulo editado exitosamente')->with('modulo',$moduledata);;
				
			}else{
				try {					
					$modulo->save();
					return Redirect::to('modulo/agregar')->withInput()->with('message', 'Modulo agregado exitosamente')->with('modulo',$moduledata);
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'El modulo no se logro agregar';					
					return Redirect::to('modulo/agregar')->with('error', $e->getMessage())->withInput()->with('modulo',$moduledata);
				}				
			}		
		}
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
				
		$modulo =
		Module::
		where('seg_module.id', $id)
		->get()
		->toArray();
		
		$preferencias = json_decode($modulo[0]['preference']);	
		
		Session::flash('_old_input.module', $modulo[0]['module']);
		Session::flash('_old_input.description', $modulo[0]['description']);		
		Session::flash('_old_input.js', $preferencias->js);
		Session::flash('_old_input.categoria', $preferencias->categoria);
		Session::flash('_old_input.controlador', $preferencias->controlador);
		Session::flash('_old_input.uiicono', $preferencias->uiicono);
		Session::flash('_old_input.icono', $preferencias->icono);
		Session::flash('_old_input.app_id', $modulo[0]['app_id']);
		Session::flash('_old_input.modulo_id', $id);
		Session::flash('_old_input.edit', true);
		Session::flash('titulo', 'Editar');
		
		$apps = Aplications::all()->toArray();
		
		foreach ($apps as $app){
			$appes[$app['id']] = $app['app'];
		}
		$moduledata['apps']=$appes;
		
		return Redirect::to('modulo/agregar')->with('modulo',$moduledata);
	}
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Modulo','Categoria','Descripción','Aplicación','JS','Controlador','uiicono','icono'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('seguridad.modulo.listar');
		
	}
	
}
