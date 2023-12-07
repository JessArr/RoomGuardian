<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoomsController extends Controller
{
    public function roomsUser(Request $request)
    {
        $token = JWTAuth::parseToken();
        $user = $token->authenticate();
        $rooms = Room::select('id', 'name')->where('user_id', $user->id)->get();
        if ($rooms->isEmpty()) {
            return response()->json([
                'process' => 'failed',
                'error' => "Habitaciones no encontradas"
            ], 404);
        }
        return response()->json(["Habitaciones"=>$rooms]);
    }

    public function roomdetail(Request $request, $id)
    {
        $token = JWTAuth::parseToken();
        $user = $token->authenticate();
        $room = Room::where('user_id', $user->id)->find($id);
        if (!$room) {
            return response()->json([
                'process' => 'failed',
                'error' => "Habitación no encontrada"
            ], 404);
        }
        $response=Http::withHeaders( [
            'X-AIO-Key' => 'aio_YZpp12PIHUF4JYBmAgoqUKzhZTtP',
        ])->get('https://io.adafruit.com/api/v2/Biozone090/feeds/habitacion1-infrarojo/data/last');
        $room->sensor_movimiento = $response->json()['value'];
        $response=Http::withHeaders( [
            'X-AIO-Key' => 'aio_YZpp12PIHUF4JYBmAgoqUKzhZTtP',
        ])->get('https://io.adafruit.com/api/v2/Biozone090/feeds/habitacion1-magnetico/data/last');
        $room->sensor_magnetico = $response->json()['value'];
        $response=Http::withHeaders( [
            'X-AIO-Key' => 'aio_YZpp12PIHUF4JYBmAgoqUKzhZTtP',
        ])->get('https://io.adafruit.com/api/v2/Biozone090/feeds/habitacion1-voltaje/data/last');
        $room->sensor_voltaje = $response->json()['value'];
        $response=Http::withHeaders( [
            'X-AIO-Key' => 'aio_YZpp12PIHUF4JYBmAgoqUKzhZTtP',
        ])->get('https://io.adafruit.com/api/v2/Biozone090/feeds/habitacion1-luz/data/last');
        $room->sensor_luz = $response->json()['value'];
        $response=Http::withHeaders( [
            'X-AIO-Key' => 'aio_YZpp12PIHUF4JYBmAgoqUKzhZTtP',
        ])->get('https://io.adafruit.com/api/v2/Biozone090/feeds/habitacion1-temperatura/data/last');
        $room->sensor_temperatura = $response->json()['value'];
        $response=Http::withHeaders( [
            'X-AIO-Key' => 'aio_YZpp12PIHUF4JYBmAgoqUKzhZTtP',
        ])->get('https://io.adafruit.com/api/v2/Biozone090/feeds/habitacion1-humedad/data/last');
        $room->sensor_humedad = $response->json()['value'];
        return response()->json(["Habitación"=>$room]);
    }

    public function store(Request $request)
    {
        $validacion = Validator::make($request->all(), [
            "name" => ["required", "min:6", "max:50"],
        ]);
        if ($validacion->fails()) {

            return response()->json([
                "process"=>"failed",
                "menssage"=>$validacion->errors()
            ]);
        }
        $token = JWTAuth::parseToken();
        $user = $token->authenticate();
        $room = new Room();
        $room->name = $request->name;
        $room->user_id = $user->id;
        $room->save();
        return response()->json([
            "process" => "success",
            "message" => "Habitación creada correctamente",
            "room" => $room
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $token = JWTAuth::parseToken();
        $user = $token->authenticate();
        $room = Room::where('user_id', $user->id)->find($id);
        if (!$room) {
            return response()->json([
                'process' => 'failed',
                'error' => "Habitación no encontrada"
            ], 404);
        }
        $room->Sensor_Magnetico = $request->input('Sensor_Magnetico');
        $room->Sensor_Movimiento = $request->input('Sensor_Movimiento');
        $room->Sensor_Temperatura = $request->input('Sensor_Temperatura');
        $room->Sensor_Humedad = $request->input('Sensor_Humedad');
        $room->Sensor_Luz = $request->input('Sensor_Luz');
        $room->Sensor_Voltaje = $request->input('Sensor_Voltaje');
        $room->save();
        return response()->json([
            "process" => "success",
            "message" => "Ha actualizado la habitación correctamente",
            "room" => $room
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $token = JWTAuth::parseToken();
        $user = $token->authenticate();
        $room = Room::where('user_id', $user->id)->find($id);
        if ($room->isEmpty()){
            return response()->json([
                'process' => 'failed',
                'error' => "Habitación no encontrada"
            ], 404);
        }
        $room->delete();
        return response()->json([
            "process" => "success",
            "message" => "Habitación eliminada correctamente",
            "room" => $room
        ], 204);
    }

}
