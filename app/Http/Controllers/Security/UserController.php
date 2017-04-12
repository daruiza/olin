<?php namespace App\Http\Controllers\Security;

use App\User;
use App\Core\Security\UserProfile;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

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

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		return view('welcome');
	}
	
	//función para mostrar la vista perfil
	public function getPerfil(){
		return view('user/perfil');
	}
	
	//función para mostrar la vista perfil
	public function getBuzon(){
		//controlador de metodo buzon
		return view('user/buzon');
	}
	
	//Esta función es para guardar el usuario de la vista perfil, el usuario personal
	public function postSave(Request $request)
	{
		//calculo de fechas para mayores de 18 años
		$hoy = date('Y-m-j');
		$fecha = strtotime('-18 year',strtotime($hoy));
		$fecha = date('Y-m-j',$fecha);
		
		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener maximo :max. caracteres',
			'numeric' => 'El :attribute  debe ser un número',
			'before' => "El :attribute  debe menor a: $fecha",
		];
		
		$rules = array(
			'name' => 'required|min:4',
			'identificacion' => 'numeric',
			'names'    => 'required|min:3|max:60', // make sure the username field is not empty
			'surnames' => 'required|min:3|max:60',			
			'sex' => 'required',
			'adress' => 'required',
			'birthdate' => "before:$fecha",
		);		
		
		$validator = Validator::make($request->input(), $rules, $messages);		
		if ($validator->fails()) {			
			return Redirect::back()->withErrors($validator);
		}else{			
			$user = new User();
			$userprofile = new UserProfile();
							
			$userprofile = UserProfile::find(Session::get('opaplus.usuario.id'));
			$user = User::find(Session::get('opaplus.usuario.id'));
			
			$user->ip = $request->server()['REMOTE_ADDR'];
			$user->name = $request->input()['name'];
			$user->email = $request->input()['email'];
			
			$userprofile ->identificacion =  $request->input()['identificacion'];			
			$userprofile ->names =  $request->input()['names'];			
			$userprofile ->surnames =  $request->input()['surnames'];			
			$userprofile ->birthdate =  $request->input()['birthdate'];			
			$userprofile ->sex =  $request->input()['sex'];			
			$userprofile ->adress =  $request->input()['adress'];			
			$userprofile ->description =  $request->input()['perfil_description'];						
			$userprofile ->movil_number =  $request->input()['movil_number'];			
			
			try {
				$userAffectedRows = User::where('id', Session::get('opaplus.usuario.id'))->update(array('ip' => $user->ip,'name' => $user->name,'email' => $user->email));
				$userProfileAffectedRows = UserProfile::where('user_id', Session::get('opaplus.usuario.id'))->update(array('identificacion' => $userprofile->identificacion,'names' => $userprofile->names,'surnames' => $userprofile->surnames,'birthdate' => $userprofile->birthdate,'sex' => $userprofile->sex,'adress' => $userprofile->adress,'description' => $userprofile->description,'movil_number' => $userprofile->movil_number));
			}catch (\Illuminate\Database\QueryException $e) {
				$message = 'La Identificación,el usuario o el email ya existen';
				return Redirect::to('perfil_usuario')->with('error', $message);
			}		
			
			Session::put('opaplus.usuario.name', $request->input()['name']);
			Session::put('opaplus.usuario.email', $request->input()['email']);
			Session::put('opaplus.usuario.identificacion', $request->input()['identificacion']);
			Session::put('opaplus.usuario.names', $request->input()['names']);
			Session::put('opaplus.usuario.surnames', $request->input()['surnames']);
			Session::put('opaplus.usuario.birthdate', $request->input()['birthdate']);
			Session::put('opaplus.usuario.sex', $request->input()['sex']);
			Session::put('opaplus.usuario.adress', $request->input()['adress']);
			Session::put('opaplus.usuario.perfil_description', $request->input()['perfil_description']);
			Session::put('opaplus.usuario.movil_number', $request->input()['movil_number']);
			
			return Redirect::to('perfil_usuario')->with('message', 'Correcta actualización de perfil');			
		}
	}
	//Esta función es para guardar el cambio de imagen
	public function postSaveimg(Request $request){
		$file = array('image' => Input::file('image'));
		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener maximo :max. caracteres',
			'mimes' => 'La :attribute debe ser de tipo jpeg, png o bmp',
		];
		$rules = array(
			'image' => 'required|mimes:jpeg,bmp,png',				
		);
		$validator = Validator::make($file, $rules,$messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}
		if (Input::file('image')->isValid()) {
			$destinationPath = 'images/user'; 
			$extension = Input::file('image')->getClientOriginalExtension(); // getting image extension
			$fileName = rand(11111,99999).'.'.$extension; // renameing image
			Input::file('image')->move($destinationPath, $fileName); // uploading file to given path
						
			$userprofile = new UserProfile();
				
			$userprofile = UserProfile::find(Session::get('opaplus.usuario.id'));
						
			$userprofile->avatar =  $fileName;
			Session::put('opaplus.usuario.avatar', $fileName);
			
			$userProfileAffectedRows = UserProfile::where('id', Session::get('opaplus.usuario.id'))->update(array('avatar' => $userprofile->avatar));
						
			return Redirect::back()->with('message', 'Correcta actualización de imagen de perfil');
		}else{
			$message = 'Archivo Imagen Invalido';
			return Redirect::back()->with('error', $message);
		}
		
	}
	//Esta función es para guardar el cambio de contraseña
	public function postSavepsw(Request $request){
		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener maximo :max. caracteres',
			'mimes' => 'La :attribute debe ser de tipo jpeg, png o bmp',
			'alphaNum' => 'La :attribute solo debe tener letras y numeros',
		];
		$rules = array(
			'contrasenia_uno' => 'required|alphaNum|min:4|max:12',
			'contrasenia_dos' => 'required|alphaNum|min:4|max:12',
			'contrasenia_tres' => 'required|alphaNum|min:4|max:12',				
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}
		
		$userdata = array(
			'name'  => Session::get('opaplus.usuario.name'),
			'password'  => Input::get('contrasenia_uno'),
			'active'  => 1
		);
		//validamos contraseña actual
		if (!$this->auth->attempt($userdata)){
			return Redirect::back()->withErrors(['Datos invalidos, tu contraseña actual no coincide'])->withInput();
		}
		//validamos contraseña nueva
		if (Input::get('contrasenia_dos') !== Input::get('contrasenia_tres')){
			return Redirect::back()->withErrors(['Datos invalidos, tu contraseña nueva no coincide'])->withInput();
		}
		
		$user = new User();		
		$user = User::find(Session::get('opaplus.usuario.id'));		
		$user->setPasswordAttribute(Input::get('contrasenia_dos'));
		Session::put('opaplus.usuario.password', $user->password);
			
		$user->save();	
		
		return Redirect::back()->with('message', 'Contraseña actualizada correctamente');
		
	}
	
	//Función para cambiar de lugar 
	public function postLugar(Request $request){		
		$array = Array();
		if($request->input('lugar')){
			//estamos en la papelera
			$array['usuario']['lugar']['lugar']='escritorio';
			$array['usuario']['lugar']['active']=1;
			Session::put('opaplus.usuario.lugar.lugar', 'escritorio');
			Session::put('opaplus.usuario.lugar.active', 1);
		}else{
			//estamos en el escritorio
			$array['usuario']['lugar']['lugar']='papelera';
			$array['usuario']['lugar']['active']=0;
			Session::put('opaplus.usuario.lugar.lugar', 'papelera');
			Session::put('opaplus.usuario.lugar.active', 0);			
		}
		return response()->json(['respuesta'=>true,'data'=>$array]);
		
	}
	
	
}

