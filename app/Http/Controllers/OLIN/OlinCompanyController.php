<?php namespace App\Http\Controllers\OLIN;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\OLIN\OlinLink;
use App\Core\OLIN\OlinCompany;
use App\Core\OLIN\OlinSeat;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class OlinCompanyController extends Controller {
	
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
		
		//consultas de empresas
		//total
		try {
			$moduledata['total_empresas']=\DB::table('oli_company')
			->select(\DB::raw('count(*) as total'))
			->join('oli_seat', 'seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->where('oli_company.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('empresas/general')->with('error', $message);
		}
		//operaciones de analisis
		//total agrupado por EMPRESAS y afiliados
		try {
			$moduledata['empresas']=\DB::table('oli_user')
			->select('company', \DB::raw('count(*) as total'))
			->join('oli_company', 'company_id', '=', 'oli_company.id')
			->join('oli_seat', 'oli_company.seat_id', '=', 'oli_seat.id')
			->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->groupBy('company_id')
			->get();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo.' de usuario';
			return Redirect::to('afiliados/general')->with('error', $message);
		}	
		
		
		
		return Redirect::to('empresas/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return view('olin.company.company_index');
		
	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('empresas/listar');
	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Empresa','Sede','Descripción'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		Session::flash('modulo', $moduledata);
		return view('olin.company.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']=OlinCompany::
		join('oli_seat', 'seat_id', '=', 'oli_seat.id')
		->where('oli_company.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
		->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
		->count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search', $request->input('search')['value']);			
			
			$moduledata['empresas']=
			OlinCompany::
			select('oli_company.id','company','oli_company.description','seat_id','seat','phone')
			->join('oli_seat', 'seat_id', '=', 'oli_seat.id')
			->where('oli_company.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			//->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->where(function ($query) {
				$query->where('oli_company.company', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_company.description', 'like', '%'.Session::get('search').'%')
				->orWhere('oli_seat.seat', 'like', '%'.Session::get('search').'%');
			})
			->skip($request->input('start'))->take($request->input('length'))
			->get();		
			$moduledata['filtro'] = count($moduledata['empresas']);
		}else{			
			$moduledata['empresas']=\DB::table('oli_company')
			->select('oli_company.id','company','oli_company.description','seat_id','seat','phone')
			->join('oli_seat', 'seat_id', '=', 'oli_seat.id')
			->where('oli_company.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			//->where('oli_seat.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->skip($request->input('start'))->take($request->input('length'))
			->get();
				
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['empresas']]);
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la url
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		$moduledata['id']=$id_mod;
		$moduledata['id_app']=$id_app;
		
		//sedes
		$seats = OlinSeat::
		where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
		->get()
		->toArray();		
		foreach ($seats as $seat){
			$seatss[$seat['id']] = $seat['seat'];
		}
		$moduledata['seats']=$seatss;
		
		//vinculos	
		$moduledata['links'] = OlinLink::
		where('oli_link.active', '=' , Session::get('opaplus.usuario.lugar.active'))
		->get()
		->toArray();
		
		return Redirect::to('empresas/agregar')->with('modulo',$moduledata);
	}
	public function getAgregar(){
	
		return view('olin.company.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){	
		
		//sedes
		$seats = OlinSeat::
		where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
		->get()
		->toArray();
		foreach ($seats as $seat){
			$seatss[$seat['id']] = $seat['seat'];
		}
		$moduledata['seats']=$seatss;
		
		//vinculos
		$moduledata['links'] = OlinLink::
		where('oli_link.active', '=' , Session::get('opaplus.usuario.lugar.active'))
		->get()
		->toArray();		
		
		$checkbox = Array();
		foreach($request->input() as $key=>$value){
			if(strpos($key,$request->input()['mod_id']) !== false) $checkbox[$key] = $value;
		}	
		
		$messages = [
				'required' => 'El campo :attribute es requerido.',				
		];
		
		$rules = array(
				'company'    => 'required',
				'seat_id' => 'required',				
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput()->with('modulo',$moduledata);
		}else{
			
			$olincompany = new OlinCompany();
			
			$olincompany->company = $request->input()['company'];			
			$olincompany->description = $request->input()['description'];
			$olincompany->seat_id = $request->input()['seat_id'];
						
			if($request->input()['edit']){
				//se pretende actualizar la empresa				
				try {
					$companyAffectedRows = OlinCompany::where('id', $request->input()['company_id'])->update(array('company' => $olincompany->company,'description' => $olincompany->description,'seat_id' => $olincompany->seat_id));
					
					//borramos todas las entradas de la empresa
					\DB::table('oli_link_x_company')->where('company_id', (int)$request->input()['company_id'])->delete();
					//relacion los vinculo
					foreach($checkbox as $value){
						\DB::table('oli_link_x_company')->insert(array(
							'link_id'=>(int)$value,
							'company_id'=>(int)$request->input()['company_id']
							)
						);
					}
					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'La Empresa o no se logro editar';
					return Redirect::to('empresas/agregar')->with('error', $message)->withInput()->with('modulo',$moduledata);
				}			
				
				Session::flash('_old_input.company', $olincompany[0]['company']);
				Session::flash('_old_input.description', $olincompany[0]['description']);
				Session::flash('_old_input.seat_id', $olincompany[0]['seat_id']);
				Session::flash('_old_input.company_id', $request->input()['company_id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');				
				
				$moduledata['com_links']=$checkbox;
				
				return Redirect::to('empresas/agregar')->withInput()->with('message', 'Empresa editada exitosamente')->with('modulo',$moduledata);
				
			}else{
				try {
					//guardado de usuario
					$olincompany->save();
					//relacion los vinculos
					foreach($checkbox as $value){						
						\DB::table('oli_link_x_company')->insert(array(
							'link_id'=>(int)$value,
							'company_id'=>$olincompany->id					
							)
						);					
					}					
				}catch (\Illuminate\Database\QueryException $e) {
					$message = 'El vinculo ya se encuentra relacionado - agregar';
					return Redirect::to('empresas/agregar')->with('error', $e->getMessage())->withInput()->with('modulo',$moduledata);
				}					
			}
			return Redirect::back()->with('modulo',$moduledata)->with('message', 'Vinculo creado adecuadamente');
		}
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		
		$moduledata['id']=$id_mod;
		$moduledata['id_app']=$id_app;
		
		//sedes
		$seats = OlinSeat::
		where('oli_seat.active', '=' , Session::get('opaplus.usuario.lugar.active'))
		->get()
		->toArray();
		foreach ($seats as $seat){
			$seatss[$seat['id']] = $seat['seat'];
		}
		$moduledata['seats']=$seatss;
		
		//vinculos
		$moduledata['links'] = OlinLink::
		where('oli_link.active', '=' , Session::get('opaplus.usuario.lugar.active'))
		->get()
		->toArray();
		
		$olincompany =
		OlinCompany::
		where('oli_company.id', $id)
		->get()
		->toArray();
		
		//consulta de vinculos que posee la empresa		
		$array =
		OlinCompany::
		select('oli_link.id')
		->join('oli_link_x_company','oli_company.id','=','oli_link_x_company.company_id')
		->join('oli_link', 'oli_link_x_company.link_id', '=', 'oli_link.id')
		->where('oli_company.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
		->where('oli_link.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
		->where('oli_company.id', $id)
		->get()
		->toArray();
		
		$com_links = Array();
		foreach($array as $value)$com_links[]=$value['id'];
		$moduledata['com_links']=$com_links;
		
		Session::flash('_old_input.company', $olincompany[0]['company']);
		Session::flash('_old_input.description', $olincompany[0]['description']);
		Session::flash('_old_input.seat_id', $olincompany[0]['seat_id']);
		Session::flash('_old_input.company_id', $id);
		Session::flash('_old_input.edit', true);
		Session::flash('titulo', 'Editar');
		
		return Redirect::to('empresas/agregar')->with('modulo',$moduledata);	
	}
	
	public function postBuscar(Request $request){
	
		$url = explode("/", Session::get('_previous.url'));
		$moduledata['fillable'] = ['Empresa','Sede','Descripción'];
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			
		Session::flash('modulo', $moduledata);
		Session::flash('filtro', $request->input()['names']);
		return view('olin.company.listar');
		
	}
	
	public function postVer(Request $request){
		
		try {
			$olincompany =
			OlinCompany::
			select('oli_company.company', 'oli_link.link', 'oli_link.description')				
			->join('oli_link_x_company','oli_company.id','=','oli_link_x_company.company_id')
			->join('oli_link', 'oli_link_x_company.link_id', '=', 'oli_link.id')
			->where('oli_company.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))	
			->where('oli_link.active', '=' ,  Session::get('opaplus.usuario.lugar.active'))
			->where('oli_company.id', $request->input()['id'])	
			->get()
			->toArray();
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo.' de usuario';			
			return response()->json(['respuesta'=>false,'data'=>$e->getMessage()]);			
		}		
	
		if(count($olincompany)){
			return response()->json(['respuesta'=>true,'data'=>$olincompany]);
		}
		
		return response()->json(['respuesta'=>false,'data'=>null]);
	}
	
	public function postLugar(Request $request){
		/*Consultamos la empresa*/
		$olincompany =
		OlinCompany::
		where('oli_company.id', $request->input()['company_id'])
		->get()
		->toArray();
	
		if(count($olincompany)){
				
			$olincompanyAffectedRows = OlinCompany::where('id', $request->input()['company_id'])->update(array('active' => $request->input()['activo']));
				
			if($olincompanyAffectedRows){
				if($request->input()['activo']) return response()->json(['respuesta'=>true,'data'=>'La empresa se ha restaurado adecuadamente, para administarla habra que ir hasta el escritorio','opp'=>$request->input()['activo']]);
				return response()->json(['respuesta'=>true,'data'=>'La empresa se reciclo adecuadamente, para recuperarla habra que ir hasta la papelera','opp'=>$request->input()['activo']]);
			}	
		}	
		return response()->json(['respuesta'=>false,'data'=>'La empress no se logro reciclar']);
	}
	
}
