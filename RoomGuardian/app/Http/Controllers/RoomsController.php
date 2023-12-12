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
        $response=$this->getAdafruitSensorData('habitacion1-infrarojo');
        $room->sensor_movimiento = $response;
        $response=$this->getAdafruitSensorData('habitacion1-magnetico');
        $room->sensor_magnetico = $response;
        $response=$this->getAdafruitSensorData('habitacion1-voltaje');
        $room->sensor_voltaje = $response;
        $response=$this->getAdafruitSensorData('habitacion1-luz');
        $room->sensor_luz = $response;
        $response=$this->getAdafruitSensorData('habitacion1-temperatura');
        $room->sensor_temperatura = $response;
        $response=$this->getAdafruitSensorData('habitacion1-humedad');
        $room->sensor_humedad = $response;
        return response()->json(["Habitación"=>$room]);
    }

    public function getAdafruitSensorData($feedName){
        $response=Http::withHeaders( [
            'X-AIO-Key' => 'aio_CmMw96Bex9QV52LbAfzWVjQl5kuK',
        ])->get('https://io.adafruit.com/api/v2/Biozone090/feeds/'.$feedName.'/data/last');
        if (!$response->ok()) {
            return response()->json([
                'process' => 'failed',
                'error' => "Falló la conexión con el servidor"
            ], 404);
        }
        return $response->json()['value'];
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

    public function destroy(Request $request, $id)
    {
        $token = JWTAuth::parseToken();
        $user = $token->authenticate();
        $room = Room::where('user_id', $user->id)->find($id);
        if (!$room){
            return response()->json([
                'process' => 'failed',
                'error' => "Habitación no encontrada"
            ], 404);
        }
        $room->delete();
        return response()->json([
            "process" => "success"
        ], 200);
    }
    public function limtemperatura(Request $request){
    $response=Http::withHeaders( [
        'X-AIO-Key' => 'aio_CmMw96Bex9QV52LbAfzWVjQl5kuK',
    ])->post('https://io.adafruit.com/api/v2/Biozone090/feeds/habitacion1-temperaturamax/data', [
        'value' => $request->input('value'),
    ]);
    if (!$response->ok()) {
        return response()->json([
            'process' => 'failed',
            'error' => "Falló la conexión con el servidor"
        ], 404);
    }
    return response()->json([
        "process" => "success",
        "message" => "Se ha actualizado el limite de  temperatura correctamente",
        "room" => $response->json()
    ], 200);
    }
}
