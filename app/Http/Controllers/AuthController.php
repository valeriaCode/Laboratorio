<?php
namespace App\Http\Controllers;
use App\Http\Requests\RegisterAuthRequest;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class AuthController extends Controller {

    public $loginAfterSignUp = true;

    public function register(Request $request) { //recibe una request
        $user = new User(); //creamos nuevo usuario
        $lab = Laboratory::findOrFail($request->laboratory_id); //en la request viene la llave de laboratorio
        $user->birthday = $request->birthday;
        $user->phone = $request->phone;
        $user->username = User::getUniqueUsername($request->name, $lab->name, $user->id); 
        $user->ci = $request->ci;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->laboratory_id = $request->laboratory_id;
        $user->save();
        if ($this->loginAfterSignUp) {
        return $this->login($request);
        }
        return response()->json([
        'status' => 'ok',
        'data' => $user
        ], 200);
    }

    public function login(Request $request) {
        $input = $request->only('username', 'password');
        $jwt_token = null;
        if (!$jwt_token = JWTAuth::attempt($input)) {
        return response()->json([
        'status' => 'invalid_credentials',
        'message' => 'Correo o contraseña no válidos.',
        ], 401);
        }
        return response()->json([
        'status' => 'ok',
        'token' => $jwt_token,
        ]);
    }

    public function logout(Request $request) {
        $this->validate($request, [
        'token' => 'required'
        ]);
        try {
        JWTAuth::invalidate($request->token); // invalidar el token
        return response()->json([
        'status' => 'ok',
        'message' => 'Cierre de sesión exitoso.'
        ]);
        } catch (JWTException $exception) {
        return response()->json([ 'status' => 'unknown_error',
        'message' => 'Al usuario no se le pudo cerrar la sesión.'
        ], 500);
        }
    }

    public function getAuthUser(Request $request) {
        $this->validate($request, [
        'token' => 'required'
        ]);
        $user = JWTAuth::authenticate($request->token);
        return response()->json(['username' => $user]);
    }

    protected function jsonResponse($data, $code = 200){
        return response()->json($data, $code,
        ['Content-Type' => 'application/json;charset=UTF8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}