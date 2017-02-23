<?php namespace App\Http\Controllers\Homage;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Homage\HomReciver;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class HomReciverController extends Controller {
		/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	protected $auth;
	//La ruta de controlador es: estado
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
		
		//Consulta de emails
		//total
		
		try{
			$moduledata['total_emails']=\DB::table('hom_reciver')->select(\DB::raw('count(*) as total'))->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('directorio/general')->with('error', $message);
		}
		
		//total agrupado por estado
		try{
			$moduledata['emails']=\DB::table('hom_reciver')->select('topic',\DB::raw('count(*) as total'))->groupBy('topic')->get();
		}catch (ModelNotFoundException $e){
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('directorio/general')->with('error',$message);
		}
		
		return Redirect::to('directorio/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		
		//llamado a vista
		return view('homenaje.reciver.reciver_index');

	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error','Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('directorio/listar');

	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Email','Nombre','Asunto','Descripción'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];		
		
		Session::flash('modulo', $moduledata);
		return view('homenaje.reciver.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']= HomReciver::count();
		
		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search',$request->input('search')['value']);
		
			$moduledata['emails']= HomReciver::where('hom_reciver.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->where(function ($query){
				$query->where('hom_reciver.email','like','%'.Session::get('search').'%')
				->orwhere('hom_reciver.name','like','%'.Session::get('search').'%')
				->orwhere('hom_reciver.topic','like','%'.Session::get('search').'%')
				->orwhere('hom_reciver.description','like','%'.Session::get('search').'%');
			})
			->skip($request->input('start'))->take($request->input('length'))->get();
			$moduledata['filtro'] = count($moduledata['emails']);
		}else{
			$moduledata['emails']=\DB::table('hom_reciver')
			->where('hom_reciver.active', '=' , Session::get('opaplus.usuario.lugar.active'))
			->skip($request->input('start'))->take($request->input('length'))
			->get();
		
		
			$moduledata['filtro'] = $moduledata['total'];
		}
		
		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['emails']]);
		
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la URL
		if(is_null($id_mod)) return Redirect::to('/')->with ('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('directorio/agregar');
	}
	public function getAgregar(){	
		return view('homenaje.reciver.agregar');
	}
	//función para guardar direcciones
	public function postSave(Request $request){
		$messages = [
				'required' => 'El campo :attribute es requerido.',
				'numeric' => 'El :attribute  debe ser un número'
		];
		
		$rules = array(
				'email'    => 'required',
				'name'	=> 'required',
				'topic'		=> 'required',
				'description' => 'required',
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		
		if($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			$reciver = new HomReciver();				
				
			$reciver->email = $request->input()['email'];
			$reciver->name = $request->input()['name'];
			$reciver->topic = $request->input()['topic'];
			$reciver->description = $request->input()['description'];
			if($request->input()['edit']){
				//se pretende editar
				try{
					$reciverAffectedRows = HomReciver::where('id', $request->input()['id'])
					->update(array('email' =>$reciver->email,'name'=>$reciver->name,'topic'=>$reciver->topic,'description'=>$reciver->description));
				}catch (\Illuminate\Database\QueryException $ex){
					$message = 'el email no se logro editar';
					return Redirect::to('directorio/agregar')->with('error',$ex)->withInput();
				}
		
				Session::flash('_old_input.email',$reciver->email);
				Session::flash('_old_input.name',$reciver->name);
				Session::flash('_old_input.topic',$reciver->topic);
				Session::flash('_old_input.description',$reciver->description);
				Session::flash('_old_input.id', $request->input()['id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');
		
				return Redirect::to('directorio/agregar')->withInput()->with('message','Email editado exitosamente');
			}else{
		
				try{
					$reciver->save();
					return Redirect::to('directorio/agregar')->withInput()->with('message','Email agregado exitosamente');
				}catch(\Illuminate\Database\QueryException $ex){
					$message = 'El email no se logro agregar';
					return Redirect::to('directorio/agregar')->with('error',$ex->getMessage())->withInput();
				}
			}
		
		}
				
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');

		$reciver = HomReciver::where('hom_reciver.id',$id)->get()->toArray();
		
		Session::flash('_old_input.email',$reciver[0]['email']);
		Session::flash('_old_input.name',$reciver[0]['name']);
		Session::flash('_old_input.topic',$reciver[0]['topic']);
		Session::flash('_old_input.description',$reciver[0]['description']);
		Session::flash('_old_input.id',$id);
		Session::flash('_old_input.edit',true);
		Session::flash('titulo','Editar');

		return Redirect::to('directorio/agregar');				
		
	}
	
	public function postBuscar(Request $request){
		$url = explode("/",Session::get('_previous.url'));
		$moduledata['fillable'] = ['Email','Nombre','Asunto','Descripción'];
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
		
		Session::flash('modulo',$moduledata);
		Session::flash('filtro',$request->input()['names']);
		return view('homenaje.reciver.listar');

		
	}

	//reciclar
	public function postLugar(Request $request){
		$olinreciver = HomReciver::where('id',$request->input()['email_id'])->get()->toArray();

		if(count($olinreciver)){
			$olinreciverAffectedRows = HomReciver::where('id',$request->input()['email_id'])->update(array('active'=> $request->input()['activo']));

			if($olinreciverAffectedRows){
				if($request->input()['activo']) return response()->json(['respuesta'=>true,'data'=>'El email se ha restaurado adecuadamente, para administrar dirigirse hasta el escritorio','opp'=>$request->input()['activo']]);
				return response()->json(['respuesta'=>true,'data'=>'El email se reciclo adecuadamente, para recuperar debe dirigirse hasta la papelera','opp'=>$request->input()['activo']]);
			}
		}
		return response()->json(['respuesta'=>false,'data'=>'El email no se logro reciclar']); 
	}
	
}
