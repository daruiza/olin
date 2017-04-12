<?php namespace App\Http\Controllers\Homage;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Core\Homage\HomState;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class HomStateController extends Controller {
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

		//Consulta de estado
		//total
		
		try{
			$moduledata['total_estados']=\DB::table('hom_state')->select(\DB::raw('count(*) as total'))->get()[0]->total;
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('estado/general')->with('error', $message);
		}

		//total agrupado por estado
		try{
			$moduledata['estados']=\DB::table('hom_state')->select('state',\DB::raw('count(*) as total'))->groupBy('state')->get();
		}catch (ModelNotFoundException $e){
			$message = 'Problemas al hallar datos de '.$modulo;
			return Redirect::to('estado/general')->with('error',$message);
		}
		
		return Redirect::to('estado/general')->with('modulo',$moduledata);
	}
	public function getGeneral(){
		if(is_null(Session::get('modulo.id'))) return Redirect::to('/')->with('error', 'Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		
		//llamado a vista
		return view('homenaje.state.state_index');

	}
	
	public function getEnumerar($id_app=null,$categoria=null,$id_mod=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error','Este modulo no se debe alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('estado/listar');

	}
	public function getListar(){
		//las siguientes dos lineas solo son utiles cuando se refresca la pagina, ya que al refrescar no se pasa por el controlador
		$moduledata['fillable'] = ['Estado','Alerta','Orden','Descripción'];
		//recuperamos las variables del controlador anterior ante el echo de una actualización de pagina
		$url = explode("/", Session::get('_previous.url'));

	
		
		//estas opciones se usaran para pintar las opciones adecuadamente con respecto al modulo
		$moduledata['modulo'] = $url[count($url)-5];
		$moduledata['id_app'] = $url[count($url)-3];
		$moduledata['categoria'] = $url[count($url)-2];
		$moduledata['id_mod'] = $url[count($url)-1];
		
		
		Session::flash('modulo', $moduledata);
		return view('homenaje.state.listar');
	}
	public function getListarajax(Request $request){
		//otros parametros
		$moduledata['total']= HomState::count();

		//realizamos la consulta
		if(!empty($request->input('search')['value'])){
			Session::flash('search',$request->input('search')['value']);

			$moduledata['estados']= HomState::where('hom_state.active', '=' , Session::get('opaplus.usuario.lugar.active'))->where(function ($query){
				$query->where('hom_state.state','like','%'.Session::get('search').'%')
				->orwhere('hom_state.alert','like','%'.Session::get('search').'%')
				->orwhere('hom_state.order','like','%'.Session::get('search').'%')
				->orwhere('hom_state.description','like','%'.Session::get('search').'%');
			})
			->skip($request->input('start'))->take($request->input('length'))->get();
			$moduledata['filtro'] = count($moduledata['estados']);
		}else{
			$moduledata['estados']=\DB::table('hom_state')->where('hom_state.active', '=' , Session::get('opaplus.usuario.lugar.active'))->skip($request->input('start'))->take($request->input('length'))->get();


			$moduledata['filtro'] = $moduledata['total'];
		}

		return response()->json(['draw'=>$request->input('draw')+1,'recordsTotal'=>$moduledata['total'],'recordsFiltered'=>$moduledata['filtro'],'data'=>$moduledata['estados']]);
		
		
	}
	
	//Función para la opción: agregar
	public function getCrear($id_app=null,$categoria=null,$id_mod=null){	
		//Modo de evitar que otros roles ingresen por la URL
		if(is_null($id_mod)) return Redirect::to('/')->with ('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');
		return Redirect::to('estado/agregar');
	}
	public function getAgregar(){	
		return view('homenaje.state.agregar');
	}
	//función para guardar usuarios con su perfil
	public function postSave(Request $request){
		$messages = [
			'required' => 'El campo :attribute es requerido.',		
			'numeric' => 'El :attribute  debe ser un número'	
		];

		$rules = array(
				'state'    => 'required',
				'alert'	=> 'required',
				'order'		=> 'required|numeric',
				'description' => 'required',				
		);

		$validator = Validator::make($request->input(), $rules, $messages);

		if($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}else{
			$estado = new HomState();	
			
			
			$estado->state = $request->input()['state'];
			$estado->alert = $request->input()['alert'];
			$estado->order = $request->input()['order'];
			$estado->description = $request->input()['description'];
			if($request->input()['edit']){
				//se pretende editar
				try{
					$estadoAffectedRows = HomState::where('id', $request->input()['id'])->update(array('state' =>$estado->state,'alert'=>$estado->alert,'order'=>$estado->order,'description'=>$estado->description));
				}catch (\Illuminate\Database\QueryException $ex){
					$message = 'el estado no se logro editar';
					return Redirect::to('estado/agregar')->with('error',$ex)->withInput();
				}

				Session::flash('_old_input.state',$estado->state);
				Session::flash('_old_input.alert',$estado->alert);
				Session::flash('_old_input.order',$estado->order);
				Session::flash('_old_input.description',$estado->description);
				Session::flash('_old_input.id', $request->input()['id']);
				Session::flash('_old_input.edit', true);
				Session::flash('titulo', 'Editar');

				return Redirect::to('estado/agregar')->withInput()->with('message','Estado editado exitosamente');
			}else{

				try{
					$estado->save();
					return Redirect::to('estado/agregar')->withInput()->with('message','Estado agregado exitosamente');
				}catch(\Illuminate\Database\QueryException $ex){
					$message = 'El estado no se logro agregar';
					return Redirect::to('estado/agregar')->with('error',$ex->getMessage())->withInput();
				}
			}

		}
		
		
	}
	public function getActualizar($id_app=null,$categoria=null,$id_mod=null,$id=null){
		if(is_null($id_mod)) return Redirect::to('/')->with('error', 'Este modulo no se puede alcanzar por url, solo es valido desde las opciones del menú');

		$estado = HomState::where('hom_state.id',$id)->get()->toArray();

		Session::flash('_old_input.state',$estado[0]['state']);
		Session::flash('_old_input.alert',$estado[0]['alert']);
		Session::flash('_old_input.order',$estado[0]['order']);
		Session::flash('_old_input.description',$estado[0]['description']);
		Session::flash('_old_input.id',$id);
		Session::flash('_old_input.edit',true);
		Session::flash('titulo','Editar');

		return Redirect::to('estado/agregar');
				
		
	}
	
	public function postBuscar(Request $request){
		$url = explode("/",Session::get('_previous.url'));
		$moduledata['fillable'] = ['Estado','Alerta','Orden','Descripción'];
		$moduledata['modulo'] = $url[count($url)-4];
		$moduledata['id_app'] = $url[count($url)-2];
		$moduledata['categoria'] = $url[count($url)-1];
		$moduledata['id_mod'] = $url[count($url)-5];
			


		Session::flash('modulo',$moduledata);
		Session::flash('filtro',$request->input()['names']);
		return view('homenaje.state.listar');

		
	}




	//reciclar
	public function postLugar(Request $request){
		$olinestado = HomState::where('id',$request->input()['estado_id'])->get()->toArray();

		if(count($olinestado)){
			$olinestadoAffectedRows = HomState::where('id',$request->input()['estado_id'])->update(array('active'=> $request->input()['activo']));

			if($olinestadoAffectedRows){
				if($request->input()['activo']) return response()->json(['respuesta'=>true,'data'=>'El estado se ha restaurado adecuadamente, para administrar dirigirse hasta el escritorio','opp'=>$request->input()['activo']]);
				return response()->json(['respuesta'=>true,'data'=>'El estado se reciclo adecuadamente, para recuperar debe dirigirse hasta la papelera','opp'=>$request->input()['activo']]);
			}
		}
		return response()->json(['respuesta'=>false,'data'=>'El estado no se logro reciclar']);
	}	
	
}
