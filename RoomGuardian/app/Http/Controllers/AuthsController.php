<?php

namespace App\Http\Controllers;

use App\Mail\VerificarMail;
use App\Models\User;
use App\Validations\Data\AuthsValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class AuthsController extends Controller
{
    public function register(Request $request)
    {
        $validacion = Validator::make($request->all(), [
            "name" => ["required", "min:10", "max:50"],
            "email" => ["required", "email", "max:150"],
            "password" => ["required", "min:6", "max:25"],
            'password_confirmation' =>["required", "min:6", "max:25","same:password"],
        ]);
        if ($validacion->fails()) {
            return $this->Errror(422,$validacion->errors());
        } else {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                return $this->Errror(409,"El usuario ya existe");
            }

            $link = URL::temporarySignedRoute('verificaremail', now()->addMinutes(20), [
                'email' => $request->email,
                'name' => $request->name,
                'password' => Hash::make($request->password),
            ]);

            $data = ['name' => $request->name, 'verificationUrl' => $link];
            Mail::to($request->email)->send(new VerificarMail($data));

            return response()->json([
                "process" => "successful",
                'message' => 'Correo de verificación enviado correctamente'
            ], 201);
        }
    }


    public function login(Request $request)
    {
        $validacion = Validator::make($request->all(), [
            "email" =>  ["required", "email", "max:150"],
            "password" => ["required", "min:6", "max:25"]
        ]);
        if ($validacion->fails()) {

            return $this->Errror(422,$validacion->errors());
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->Errror(404,"Usuario no encontrado");
        }
        if (!Hash::check($request->password, $user->password)) {
            return $this->Errror(401,"Contraseña incorrecta");
        } else {
            $token = JWTAuth::fromUser($user);
            return response()->json([
                "process" => "successful",
                'message' => 'Inicio de sesión exitoso',
                'token' => $token
            ], 200);
        }
    }


    public function logout(Request $request)
    {
        $token = JWTAuth::parseToken();
        try {
            $token->invalidate();
            $token->blacklist();
        } catch (TokenInvalidException $e) {
        }
        return response()->json([
            "process" => "successful",
            'message' => 'Cierre de sesión exitoso'
        ], 200);
    }

    public function refresh(Request $request)
    {
        $token = JWTAuth::getToken();
        try {
            $newToken = JWTAuth::refresh($token);
        } catch (TokenExpiredException $e) {
        }
        return response()->json([
            "process" => "successful",
            "message" => "Token actualizado",
            "new token" => $newToken
        ], 200);
    }

    public function verificarmail(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->email_verified_at = now();
        $user->save();
        return view('registro_confirmado', compact('user'));
    }
    public function ValidarToken(Request $request)
    {
        $token = JWTAuth::getToken();
        try {
            $token->check();
            return response()->json([
                "process" => "successful",
                "message" => true
            ], 200);
        } catch (TokenExpiredException $e) {

            return $this->Errror(401,"false");
        }
    }
    public function tokenvalidate(Request $request)
    {
        $isValidToken = false;

        try {
            JWTAuth::parseToken()->authenticate();
            $isValidToken = true;
        } catch (\Exception $e) {
            $isValidToken = false;
        }

        return response()->json([
            "process" => "successful",
            "message" => $isValidToken
        ], $isValidToken ? 200 : 401);
    }
    public function Errror($Status,$Message)
    {
        return response()->json([
            "process" => "failed",
            "message" => $Message
        ], $Status);
    }

}
