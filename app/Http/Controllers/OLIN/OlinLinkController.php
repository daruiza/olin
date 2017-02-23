<?php namespace App\Http\Controllers\OLIN;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\OLIN\OlinLink;
use Illuminate\Support\Facades\Input;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class OlinLinkController extends Controller {
	
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
				
		//consultas de vinculos
		//total
		try {
			$moduledata['total_vinculos']=\DB::table('oli_link')
			->select(\DB::raw('count(*) as total'))	
			->where('oli_link.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('vinculos/general')->with('error', $message);
		}
		
		//operaciones de analisis
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
		
		
		return Redirect::to('vinculos/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('olin.link.link_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('vinculos/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Vinculo','Descripción'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		Session::flash('modulo', $moduledata);
		return view('olin.link.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=OlinLink::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['vinculos']=
			OlinLink::	
			where('oli_link.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->where(function ($query) {
				$query->where('oli_link.link', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_link.description', 'like', '%'.Session::get('search').'%');				
			})
			->skip($request->input('start'))->take($request->input('length'))
			->get();		
			$moduledata['filtro'] = count($moduledata['vinculos']);
		}else{			
			$moduledata['vinculos']=\DB::table('oli_link')
			->where('oli_link.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->skip($request->input('start'))->take($request->input('length'))->get();
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['vinculos']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
			
		return Redirect::to('vinculos/agregar');
	}
	public function getAgregar(){
	
		return view('olin.link.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){	
		$messages = [
				'required' => 'El campo :attribute es requerido.',
		];
		
		$rules = array(
				'link'    => 'required',				
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
				
			$olinlink = new OlinLink();
				
			$olinlink->link = $request->input()['link'];
			$olinlink->description = $request->input()['description'];
		
			if($request->input()['edit']){
				//se pretende actualizar el link
				try {
					$linkAffectedRows = OlinLink::where('id', $request->input()['link_id'])->update(array('link' => $olinlink->link,'description' => $olinlink->description));
						
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'El Vinculo no se logro editar';
					return Redirect::to('vinculos/agregar')->with('error', $message)->withInput();
				}
		
				Session::flash('_old_input.link', $olinlink[0]['link']);
				Session::flash('_old_input.description', $olinlink[0]['description']);
				Session::flash('_old_input.link_id', $request->input()['link_id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');
		
				return Redirect::to('vinculos/agregar')->withInput()->with('message', 'Vinculo editado exitosamente');
		
			}else{
				try {
					$olinlink->save();
					
					$url = explode("/", Session::get('_previous.url'));
					$moduledata['fillable'] = ['Sede','Telefono','Descripción'];
					//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
					$moduledata['modulo'] = $url[count($url)-5];
					$moduledata['id_app'] = $url[count($url)-3];
					$moduledata['categoria'] = $url[count($url)-2];
					$moduledata['id_mod'] = $url[count($url)-1];
						
					Session::flash('modulo', $moduledata);
					Session::flash('filtro', $request->input()['link']);					
					Session::flash('message', 'El Vinculo se ha agregado exitosamente');
					
					return view('olin.link.listar');					
					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'El vinculo no se logro agregar';
					return Redirect::to('vinculos/agregar')->with('error', $e->getMessage())->withInput();
				}
			}
		}
		
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
				
		$olinlink =
		OlinLink::
		where('oli_link.id', $id)
		->get()
		->toArray();
		
		Session::flash('_old_input.link', $olinlink[0]['link']);
		Session::flash('_old_input.description', $olinlink[0]['description']);		
		Session::flash('_old_input.link_id', $id);
		Session::flash('_old_input.edit', true);
		Session::flash('titulo', 'Editar');
		
		return Redirect::to('vinculos/agregar');
	}
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Vinculo','Descripción'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('olin.link.listar');
		
	}
	
	public function postLugar(Request $request){
		/*Consultamos la sede
		 * */
		
		$olinlink =
		OlinLink::
		where('oli_link.id', $request->input()['link_id'])
		->get()
		->toArray();
		
		if(count($olinlink)){
				
			$olinlinkAffectedRows = OlinLink::where('id', $request->input()['link_id'])->update(array('active' => $request->input()['activo']));
				
			if($olinlinkAffectedRows){
				if($request->input()['activo']) return response()->json(['respuesta'=>true,'data'=>'El vinculo se restaurado adecuadamente, para administarla habra que ir hasta el escritorio','opp'=>$request->input()['activo']]);
				return response()->json(['respuesta'=>true,'data'=>'El vinculo se reciclo adecuadamente, para recuperarla habra que ir hasta la papelera','opp'=>$request->input()['activo']]);
			}
	
		}
		return response()->json(['respuesta'=>false,'data'=>'La sede no se logro reciclar']);
	}
	
}
