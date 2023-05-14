<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ejercicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\role;
use App\Models\rutina_users;
use App\Models\tarifa;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function addUser(Request $req)
    {
        try {
            $user = new User();
            $user->name = $req->name;
            $user->email = $req->email;
            $user->password = Hash::make($req->password);
            $user->fecha_nacimiento = $req->fecha_nacimiento;
            $user->save();

            $role_default = role::where('nombre_role', 'default_user')->get();
            $arrayRoles = $req->checked;
            $user->roles()->sync($arrayRoles);

            // return $user->roles;


            $success = true;
            $message = "Usuario registrado correctamente";
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }


        $response = [
            'success' => $success,
            'message' => $message,
        ];

        return response()->json($response);
    }

    public function allUsers(Request $req)
    {
        $users = User::all()->toArray();
        return $users;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        DB::delete('delete from rutina_users where user_id ? '[$id]);
        $user->delete();
        return response()->json(['success' => 'User deleted successfully']);
    }

    public function updateUsers($id, $idroles, Request $request)
    {
        $user = User::find($id);
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'fecha_nacimiento' => 'required',
        ]);

        $idroles->validate([
            'user_id' => 'required',
            'role_id' => 'required'
        ]);
        $roles = $idroles->all();
        $rol = User::roles();
        $input = $request->all();
        $input["password"] = Hash::make($request->password);
        $user->update($input);


        return response()->json(['success' => 'User update successfully']);
    }

    public function addTarifas(Request $req)
    {
        try {
            $tarifa = new tarifa();
            $tarifa->tipo_tarifa = $req->tipo_tarifa;
            $tarifa->precio = $req->precio;
            $tarifa->descripcion_tarifa = $req->descripcion_tarifa;
            $tarifa->save();


            $success = true;
            $message = "Tarifa añadida correctamente";
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }


        $response = [
            'success' => $success,
            'message' => $message,
        ];

        return response()->json($response);
    }

    public function deleteTarifas($id)
    {
        DB::delete('delete from tarifas where id = ?', [$id]);
        DB::delete('delete from facturas where tarifa_id ? '[$id]);
        return response()->json(['success' => 'Tarifa deleted successfully']);
    }


    public function showUser($id)
    {
        $users = User::find($id);
        return $users;
    }

    public function newTarifas(Request $req)
    {
        try {
            $tarifa = new tarifa();
            $tarifa->tipo_tarifa = $req->tipo_tarifa;
            $tarifa->precio = $req->precio;
            $tarifa->descripcion_tarifa = $req->descripcion_tarifa;
            $tarifa->save();

            $success = true;
            $message = "Tarifa añadida correctamente";
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }


        $response = [
            'success' => $success,
            'message' => $message,
        ];

        return response()->json($response);
    }

    public function showTarifa($idtarifa)
    {
        $tarifas = tarifa::findOrFail($idtarifa);
        return $tarifas;
    }

    public function updatetarifa(Request $req, $idtarifa)
    {
        $tarifa = tarifa::find($idtarifa);
        $req->validate([
            'tipo_tarifa' => 'required',
            'precio' => 'required',
            'descripcion_tarifa' => 'required'
        ]);

        $input = $req->all();
        $tarifa->update($input);
        return response()->json(['success' => 'Tarifa update successfully']);
    }

    public function editUser($iduser, Request $req)
    {
        $user = User::find($iduser);
        $req->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'fecha_nacimiento' => 'required',
        ]);

        $input = $req->all();
        $input["password"] = Hash::make($req->password);
        $user->update($input);
        return response()->json(['success' => 'User update successfully']);
    }

    public function ejericicios()
    {
        $ejercicios = ejercicio::all()->toArray();
        return $ejercicios;
    }

    public function deleteEjercicios($idejercicio)
    {
        $ejercicio = ejercicio::find($idejercicio);
        $ejercicio->delete();
        return response()->json(['success' => 'Ejercicio deleted successfully']);
    }

    public function showEjericioUpdate($id)
    {
        $ejercicio = ejercicio::find($id);
        return $ejercicio;
    }

    public function updateEjercicio($idejercicio, Request $req)
    {
        $ejercicio = ejercicio::find($idejercicio);
        $req->validate([
            'nombre_ejercicio' => 'required',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        $input = $req->all();
        $imageName = NULL;
        if ($image = $req->file('file')) {
            $destinationPath = 'img/';
            $imageName = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);
            $input['image'] = $imageName;
            unlink('img/' . $ejercicio->image);
        }
        $ejercicio->update($input);
        return response()->json(['success' => 'Ejercicio update successfully']);
    }

    public function createEjercicio(Request $req)
    {
        $req->validate([
            'nombre_ejercicio'=> 'required',
            'imagen_ejercicio' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'categoria_id' => 'required'
        ]);

        $input = $req->all();
        $imageName = NULL;

        if($image = $req->file('imagen_ejercicio')){
            $destinationPath = 'img/';
            $imageName = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);
            $input['image'] = $imageName;
        }
        ejercicio::create($input);

        return response()->json(['success' => 'Ejercicio creado correctamente.']);
    }

    public function updateRutina(Request $req, $idrutina){
        // $rutina_user = rutina_users::where('user_id', $iduser)->get();
        $rutina_user = rutina_users::find($idrutina);
        $req->validate([
            'nombre_rutina' => 'required',
            'dia_semana' => 'required',
            'ejercicios' => 'required',
            'user_id' => 'required'
        ]);

        $input = $req->all();
        $rutina_user->update($input);
        return response()->json(['success' => 'Rutina update successfully']);
    }

    public function deleteRutina($idrutina){
        $rutina = rutina_users::find($idrutina);
        $rutina->delete();
        return response()->json(['success' => 'Rutina deleted successfully']);
    }
}
