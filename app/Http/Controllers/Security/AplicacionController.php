<?php namespace App\Http\Controllers\Security;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Security\Aplications;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class AplicacionController extends Controller {
	
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
		
		//consultas de aplicaciones
		//total
		try {
			$moduledata['total_aplicaciones']=\DB::table('seg_app')
			->select(\DB::raw('count(*) as total'))			
			->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('aplicacion/general')->with('error', $message);
		}
		//total agrupado por modulos
		try {
			$moduledata['aplicaciones']=\DB::table('seg_app')
            ->select('app', \DB::raw('count(*) as total'))
            ->join('seg_module', 'app_id', '=', 'seg_app.id')           
            ->groupBy('seg_app.id')
            ->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de las '.$modulo;
			return Redirect::to('aplicacion/general')->with('error', $message);
		}		
		
		return Redirect::to('aplicacion/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('seguridad.aplicacion.aplicacion_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('aplicacion/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Aplicación','Descripción','Icono','JS'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
				
		Session::flash('modulo', $moduledata);
		
		return view('seguridad.aplicacion.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=Aplications::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['apps']=
			Aplications::			
			where(function ($query) {
				$query->where('seg_app.app', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_app.description', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_app.preferences', 'like', '%'.Session::get('search').'%');
			})
			->skip($request->input('start'))->take($request->input('length'))
			->get();		
			$moduledata['filtro'] = count($moduledata['apps']);
		}else{
			$moduledata['apps']=
			Aplications::all();
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['apps']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
			
		return Redirect::to('aplicacion/agregar');
	}
	public function getAgregar(){
	
		return view('seguridad.aplicacion.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){
		
		$messages = [
				'required' => 'El campo :attribute es requerido.',				
		];
		
		$rules = array(
				'app'    => 'required',
				'description' => 'required',
				'preferences' => 'required',
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			
			$app = new Aplications();
			
			$app->app = $request->input()['app'];
			$app->description = $request->input()['description'];
			$app->preferences = $request->input()['preferences'];
						
			if($request->input()['edit']){
				//se pretende actualizar el rol				
				try {
					$appAffectedRows = Aplications::where('id', $request->input()['app_id'])->update(array('app' => $app->app,'description' => $app->description,'preferences' => $app->preferences));
					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'La aplicación no se logro editar';
					return Redirect::to('aplicacion/agregar')->with('error', $message)->withInput();
				}
				
				Session::flash('_old_input.app', $app[0]['app']);
				Session::flash('_old_input.description', $app[0]['description']);
				Session::flash('_old_input.preferences', $app[0]['preferences']);
				Session::flash('_old_input.app_id', $request->input()['app_id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');
				
				return Redirect::to('aplicacion/agregar')->withInput()->with('message', 'Aplicación editada exitosamente');
				
			}else{
				try {					
					$app->save();
					return Redirect::to('aplicacion/agregar')->withInput()->with('message', 'Aplicción agregada exitosamente');
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'La aplicación no se logro agregar';					
					return Redirect::to('aplicacion/agregar')->with('error', $e->getMessage())->withInput();
				}				
			}		
		}
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
				
		$app =
		Aplications::
		where('seg_app.id', $id)
		->get()
		->toArray();
		
		Session::flash('_old_input.app', $app[0]['app']);
		Session::flash('_old_input.description', $app[0]['description']);		
		Session::flash('_old_input.preferences', $app[0]['preferences']);
		Session::flash('_old_input.app_id', $id);
		Session::flash('_old_input.edit', true);
		Session::flash('titulo', 'Editar');
		
		return Redirect::to('aplicacion/agregar');
	}
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Aplicación','Descripción','Icono','JS'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-7];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('seguridad.aplicacion.listar');		
	}
	
	public function postVer(Request $request){
		//consultamos los datos
		$app =
		Aplications::
		where('seg_app.id', $request->input()['id'])
		->join('seg_module','seg_app.id','=','seg_module.app_id')		
		->get()
		->toArray();
		
		if(count($app)){
			return response()->json(['respuesta'=>true,'data'=>$app]);
		}
		return response()->json(['respuesta'=>false,'data'=>null]);
		/*
		$app =
		Aplications::
		with('appModules')
		->where('seg_app.id', $request->input()['id'])
		->get()->all()[0]->toArray();
		*/		
	}
	
}
