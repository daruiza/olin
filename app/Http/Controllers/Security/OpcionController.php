<?php namespace App\Http\Controllers\Security;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Security\Option;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class OpcionController extends Controller {
	
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
			
		try {
			$moduledata['opciones']=Option::all()->toArray();;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo.' de usuario';
			return Redirect::to('opcion/general')->with('error', $message);
		}
		$moduledata['total_opciones'] = count($moduledata['opciones']);
				
		$datos = Array('categoria'=>array(),'datos'=>array());
		foreach ($moduledata['opciones'] as $value){
			//creación de categorias
			if(!(in_array(json_decode($value['preference'])->lugar,$datos['categoria']))){
				$datos['categoria'][] = json_decode($value['preference'])->lugar;
			}			
			//datos
			if(!(key_exists(json_decode($value['preference'])->vista,$datos['datos']))){
				$datos['datos'][json_decode($value['preference'])->vista]=array();
			}
			if(!(key_exists(json_decode($value['preference'])->lugar,$datos['datos'][json_decode($value['preference'])->vista]))){
				$datos['datos'][json_decode($value['preference'])->vista][json_decode($value['preference'])->lugar]=1;
			}else $datos['datos'][json_decode($value['preference'])->vista][json_decode($value['preference'])->lugar]++;			
			
		}
		$aux_datos = array();
		foreach ($datos['datos'] as $key=>$value){
			$aux_datos[$key] = array();;
			foreach ($value as $val){
				$aux_datos[$key][] = $val;
			}
		}
		$datos['datos'] = $aux_datos;		
		$moduledata['datos'] = $datos;	
		
		return Redirect::to('opcion/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('seguridad.opcion.opcion_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('opcion/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Opción','Acción','Lugar','Vista'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		Session::flash('modulo', $moduledata);
		return view('seguridad.opcion.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=Option::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['opciones']=
			Option::			
			where(function ($query) {
				$query->where('seg_option.option', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_option.action', 'like', '%'.Session::get('search').'%')
				->orWhere('seg_option.preference', 'like', '%'.Session::get('search').'%');
			})
			->skip($request->input('start'))->take($request->input('length'))
			->get();		
			$moduledata['filtro'] = count($moduledata['opciones']);
		}else{
			$moduledata['opciones']=\DB::table('seg_option')->skip($request->input('start'))->take($request->input('length'))->get();
							
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['opciones']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
			
		return Redirect::to('opcion/agregar');
	}
	public function getAgregar(){
	
		return view('seguridad.opcion.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){
		
		$messages = [
				'required' => 'El campo :attribute es requerido.',				
		];
		
		$rules = array(
				'option'    => 'required',
				'action' => 'required',	
				'lugar' => 'required',
				'vista' => 'required',
				'icono' => 'required',
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			
			$option = new Option();
			
			$option->option = $request->input()['option'];
			$option->action = $request->input()['action'];
			
			$preferencias = array();
			$preferencias['lugar'] = $request->input()['lugar'];
			$preferencias['vista'] = $request->input()['vista'];			
			$preferencias['icono'] = $request->input()['icono'];
				
			$option->preference = json_encode($preferencias);
						
			if($request->input()['edit']){
				//se pretende actualizar la opcion		
				
				try {
					$optionAffectedRows = Option::where('id', $request->input()['option_id'])->update(array('option' => $option->option,'action' => $option->action,'preference' => $option->preference));
					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'la opción no se logro editar';
					return Redirect::to('opcion/agregar')->with('error', $message)->withInput();
				}
				
				Session::flash('_old_input.option', $option[0]['option']);
				Session::flash('_old_input.action', $option[0]['action']);
				Session::flash('_old_input.lugar', $request->input()['lugar']);
				Session::flash('_old_input.vista', $request->input()['vista']);
				Session::flash('_old_input.icono', $request->input()['icono']);
				Session::flash('_old_input.option_id', $request->input()['option_id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');			
				
				return Redirect::to('opcion/agregar')->withInput()->with('message', 'Opcion editada exitosamente')->withInput();;
				
			}else{
				try {					
					$option->save();
					return Redirect::to('opcion/agregar')->withInput()->with('message', 'Opción agregada exitosamente');
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'La opción no se logro agregar';					
					return Redirect::to('opcion/agregar')->with('error', $e->getMessage())->withInput();
				}				
			}		
		}
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
				
		$opcion =
		Option::
		where('seg_option.id', $id)
		->get()
		->toArray();
		
		$preferencias = json_decode($opcion[0]['preference']);	
		
		Session::flash('_old_input.option', $opcion[0]['option']);
		Session::flash('_old_input.action', $opcion[0]['action']);
		Session::flash('_old_input.lugar', $preferencias->lugar);
		Session::flash('_old_input.vista', $preferencias->vista);
		Session::flash('_old_input.icono', $preferencias->icono);
		Session::flash('_old_input.option_id', $id);
		Session::flash('_old_input.edit', true);
		Session::flash('titulo', 'Editar');
		
		return Redirect::to('opcion/agregar');
	}
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Opción','Acción','Lugar','Vista'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('seguridad.opcion.listar');
		
	}
	
}
