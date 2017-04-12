<?php namespace App\Http\Controllers\Auth;

use App\User;
use App\Core\Security\Permit;
use Validator;
use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Core\Security\Aplications;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class KeyController extends Controller {

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
	 * The Guard implementation.
	 *
	 * @var Guard
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
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function getLogin()
	{		
		if ($this->auth->guest()){
			return view('auth/login');
		}
		return Redirect::route('home');
	}
	
	public function postAcces(Request $request)
	{
		$messages = [
			'required' => 'El campo :attribute es requerido.',
			'size' => 'La :attribute deberia ser mayor a :size.',
			'min' => 'La :attribute deberia tener almenos :min. caracteres',
			'max' => 'La :attribute no debe tener mas de :max. caracteres',
			'alphaNum' => 'La :attribute solo debe tener letras y numeros',
		];

		$rules = array(
			'usuario'    => 'required|min:4|max:60', // make sure the username field is not empty
			'contrasenia' => 'required|alphaNum|min:4|max:12' // password can only be alphanumeric and has to be greater than 3 characters
		);
		
		$validator = Validator::make($request->input(), $rules, $messages);
		
		if ($validator->fails()) {
			//el redirect puede redirigir a route, to, back, url			
			return Redirect::to('auth/login')->withErrors($validator)->withInput();
		}else{			
			//preguntamos si el usuario no esta autenticado
			if (!$this->auth->check()){
				
				$userdata = array(
				    'name'  => Input::get('usuario'),
				    'password'  => Input::get('contrasenia'),
					'active'  => 1
				);
				
				if (!$this->auth->attempt($userdata)){
					//el redirect puede redirigir a route, to, back, url
					return Redirect::to('auth/login')->withErrors(['Datos invalidos, comunicate con el administrador'])->withInput();
				}					
				
				//consultamos el usuario
				$user = new User();
				$result = null;
				try {					
					//$result = User::with('profile')->with('userAplications')->where('seg_user.active',$userdata['active'])->where('name',$userdata['name'])->join('seg_rol','seg_user.rol_id','=','seg_rol.id')->join('seg_user_profile','seg_user.id','=','seg_user_profile.user_id')->get()->all()[0]->toArray();
					$result = User::where('seg_user.active',$userdata['active'])->where('name',$userdata['name'])->join('seg_rol','seg_user.rol_id','=','seg_rol.id')->join('seg_user_profile','seg_user.id','=','seg_user_profile.user_id')->get()->all()[0]->toArray();
				}catch (ModelNotFoundException $e) {
					$message = 'Problemas al hallar el perfil de usuario';
					return Redirect::back()->with('error', $message);
				}
				
				$array = Array();
				$array['usuario']['id'] = $user->id = $result['user_id'];
				$array['usuario']['name']=$result['name'];
				$array['usuario']['email']=$result['email'];
				$array['usuario']['password']=$result['password'];
				$array['usuario']['active']=$result['active'];
				$array['usuario']['login']=$result['login'];				
				$array['usuario']['ip'] = $user->ip = $request->server()['REMOTE_ADDR'];
				$array['usuario']['rol_id']=$result['rol_id'];
				$array['usuario']['rol']=$result['rol'];
				$array['usuario']['rol_description']=$result['description'];
				/*
				$array['usuario']['identificacion']=$result['profile']['identificacion'];
				$array['usuario']['names']=$result['profile']['names'];
				$array['usuario']['surnames']=$result['profile']['surnames'];
				$array['usuario']['birthdate']=$result['profile']['birthdate'];
				$array['usuario']['sex']=$result['profile']['sex'];
				$array['usuario']['adress']=$result['profile']['adress'];
				$array['usuario']['avatar']=$result['profile']['avatar'];
				$array['usuario']['perfil_description']=$result['profile']['description'];
				$array['usuario']['template']=$result['profile']['template'];
				$array['usuario']['movil_number']=$result['profile']['movil_number'];
				$array['usuario']['location']=$result['profile']['location'];				
				*/
				$array['usuario']['identificacion']=$result['identificacion'];
				$array['usuario']['names']=$result['names'];
				$array['usuario']['surnames']=$result['surnames'];
				$array['usuario']['birthdate']=$result['birthdate'];
				$array['usuario']['sex']=$result['sex'];
				$array['usuario']['adress']=$result['adress'];
				$array['usuario']['avatar']=$result['avatar'];
				$array['usuario']['perfil_description']=$result['description'];
				$array['usuario']['template']=$result['template'];
				$array['usuario']['movil_number']=$result['movil_number'];
				$array['usuario']['location']=$result['location'];
				$array['usuario']['ultimo_ingreso']=$result['updated_at'];
				
				//asignamos la vista en el escritorio
				$array['usuario']['lugar']['lugar']='escritorio';
				$array['usuario']['lugar']['active']=1;
				
				//consultamos las aplicaciones del usuario del usuario disponibles
				$apps = Array();
				$aplications=\DB::table('seg_app_x_user')->where('active',1)->where('user_id',$array['usuario']['id'])->get();
				//foreach ($result['user_aplications'] as $value){
				foreach ($aplications as $value){
					//if($value['active'])$apps[]=$value['app_id'];
					if($value->active)$apps[]=$value->app_id;
				}				
				$aplicaciones = null;
				try {
					$aplicaciones=Aplications::find($apps)->where('active',1)->toArray();
				}catch (ModelNotFoundException $e) {
					$message = 'Problemas al hallar las aplicaciones de usuario';
					return Redirect::back()->with('error', $message);
				}
								
				//consultamos los modulos de las aplicaciones permitidos para este usuario
				$permisos = array();//este vector almacena la forma definitiva				
				try {
					$permits = Permit::with('modules')->with('options')->where('rol_id',$result['rol_id'])->get()->toArray();
				}catch (ModelNotFoundException $e) {
					$message = 'Problemas al hallar los permisos de usuario';
					return Redirect::back()->with('error', $message);
				}
				
				//filtamos los modulos deacuerso a las plicaciones vigentes para el usuario
				$pmts = Array();
				foreach ($permits as $value){
					if(in_array($value['modules']['app_id'],$apps))$pmts[]=$value;
				}
				$permits = $pmts;			
										
				foreach ($permits as $value){					
					if(!(key_exists($value['modules']['app_id'], $permisos))){
						$permisos[$value['modules']['app_id']]['modulos'] = array();
						
						if(!(key_exists($value['modules']['app_id']['aplicacion'], $permisos))){
							foreach ($aplicaciones as $app){
								if($value['modules']['app_id'] == $app['id']){
									$permisos[$value['modules']['app_id']]['aplicacion'] = $app['app'];
									$permisos[$value['modules']['app_id']]['descripcion'] = $app['description'];
									$permisos[$value['modules']['app_id']]['preferencias'] = $app['preferences'];
								}									
							}							
						}
						
						
					}
					if(!(key_exists($value['module_id'], $permisos[$value['modules']['app_id']]['modulos']))){
						$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']] = array();
						$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['modulo'] = $value['modules']['module'];
						$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['preferencias'] = $value['modules']['preference'];
						$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['descripcion'] = $value['modules']['description'];
						$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['opciones'] = array();						
					}										
				}
				//este último ciclo agrega las opciones a los array previamente creados, no se logro hacer en el anterior ciclo.
				foreach ($permits as $value){
					$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['opciones'][$value['options']['id']][$value['options']['id']] = $value['options']['option'];
					$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['opciones'][$value['options']['id']]['lugar'] = json_decode($value['options']['preference'])->lugar;
					$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['opciones'][$value['options']['id']]['vista'] = json_decode($value['options']['preference'])->vista;
					$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['opciones'][$value['options']['id']]['icono'] = json_decode($value['options']['preference'])->icono;
					$permisos[$value['modules']['app_id']]['modulos'][json_decode($value['modules']['preference'])->categoria][$value['module_id']]['opciones'][$value['options']['id']]['accion'] = $value['options']['action'];
				}
				$array['usuario']['permisos']=$permisos;
				//asignamos al session, el array de permisos				
				Session::put('opaplus', $array);								
				//actualizamos la ip				
				try {
					$user->where('id',$user->id)->update(array('ip' => $user->ip, 'updated_at' => date("Y-m-d")));
				}catch (ModelNotFoundException $e) {
					$message = 'Problemas al actualizar los datos de usuario';
					return Redirect::back()->with('error', $message);
				}
				
				//registamos el log de acceso
				
				\DB::insert("INSERT INTO `oli_log` (
					`id`,
					`action`,
					`description`,
					`date`,
					`user_id`,					
					`created_at`,
					`updated_at`) VALUES (
					NULL,
					'Acceso',
					'Login usuario: ".Session::get('opaplus.usuario.names')."',
					'".date('Y-m-d')."',
					'".Session::get('opaplus.usuario.id')."',						
					NULL,
					NULL)");
				
				//retornamos al index que debe pintar con la nueva imformacion				
				return Redirect::route('home')->with('message-acces', 'Bienvenido: '.Session::get('opaplus.usuario.names').' '.Session::get('opaplus.usuario.surnames') );
			}
			else{				
				dd($validator);
				exit();
			}
		}		
		
	}
	//esta función es para enviar el correo de recuperar el password
	public function postRecoverpsw(Request $request)
	{
		//verificamos si el email corresponde a un usuario de la aplicacion
		$data = array(
			'name' => Session::get('copy'),
			'mail' => Session::get('mail'),
			'email' => $request->input()['email'],				
		);
		try {
			$model = User::where('email', '=', $data['email'])->firstOrFail();
		}catch (ModelNotFoundException $e) {
			$message = 'El email suministrado no es valido';
            return Redirect::back()->with('error', $message);
		}
		
		$data['password'] = $model->password;
		$data['user'] = $model->name;		
		$data['url'] = substr_replace($request->url(),"recuperar_contrasenia/".$model->name."/".base64_encode($model->password),strlen($request->url())-strlen('auth/recoverpsw'));
						
		Mail::send('email.recover',$data,function($message) use ($model) {
			$message->from(Session::get('mail'),Session::get('copy'));
			$message->to($model->email,$model->name)->subject('Recuperación de Contraseña.');
		});
		
		return Redirect::to('auth/login')->with('message', 'Revisa tu correo elecronico, '. Session::get('copy') .' acaba de enviarte un correo que te ayudara a recuperar tu contraseña');
	
	}
	//esta función es para recuperar el password que llega desde el amail
	public function getRecoverpsw($user=null, $psw=null, Request $request)
	{
		//verificamos las variables
		if(is_null($user) or is_null($psw)){
			//retornamos con mensaje de error
			return Redirect::to('auth/login')->with('error', 'Los datos no alcanzarón a llegar desde el correo electronico. ¡Intentalo de nuevo!');
		}
		//si intentan corromper los datos por html
		$userdata = array(
			'name'  => $user,
			'password'  => base64_decode($psw),
			'active'  => 1
		);
		
		try {
			//hay que validar de este modo ya que el modelo User encripta la contraseña y ya la tenemos encriptada
			$model = User::where('name', '=', $userdata['name'])->where('password', '=', $userdata['password'])->firstOrFail();
		}catch (ModelNotFoundException $e) {
			$message = 'Datos invalidos, tu contraseña o tu usuario actual no coincide.';
			return Redirect::to('auth/login')->with('error',$message);
		}
		$userdata['id']=$model->id;
		return Redirect::to('auth/login')->with('user',$userdata);
		
	}
	//esta función es para guardar el nuevo  password recuperado
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
			'user' => 'required',
			'contrasenia_uno' => 'required',
			'contrasenia_dos' => 'required|alphaNum|min:4|max:4',
			'contrasenia_tres' => 'required|alphaNum|min:4|max:4',				
				
		);
		$validator = Validator::make($request->input(), $rules, $messages);
		if ($validator->fails()) {			
			return Redirect::to('auth/login')->withErrors($validator)->withInput();
		}		
		try {			
			//hay que validar de este modo ya que el modelo User encripta la contraseña y ya la tenemos encriptada
			$model = User::where('name', '=', $request->input()['user'])->where('password', '=', $request->input()['contrasenia_uno'])->firstOrFail();
		}catch (ModelNotFoundException $e) {
			$message = 'Datos invalidos, tu contraseña o tu usuario actual no coincide.';
			return Redirect::to('auth/login')->with('error',$message);
		}
		//validamos contraseña nueva
		if ($request->input()['contrasenia_dos'] !== $request->input()['contrasenia_tres']){			
			return Redirect::back()->withErrors(['Datos invalidos, tu contraseña nueva no coincide'])->withInput();
		}
		
		$user = new User();
		
		$user = User::find($request->input()['id']);		
		$user->setPasswordAttribute($request->input()['contrasenia_dos']);				
		$user->save();
		
		return Redirect::back()->with('message', 'Contraseña actualizada correctamente');
		
	}
	//función para salir de al aplicación
	public function getLogout()
	{
		// Cerramos la sesión
		$this->auth->logout();
		$user = new User();
		try {
			$user->where('id',Session::get('opaplus.usuario.id'))->update(array('ip' => Session::get('opaplus.usuario.ip'), 'updated_at' => date("Y-m-d")));
		}catch (ModelNotFoundException $e) {
			$message = 'Problemas al actualizar los datos de usuario';
			return Redirect::back()->with('error', $message);
		}		
		return Redirect::to('auth/login')->with('message', 'Salida segura');
	}

}
