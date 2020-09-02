<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Categoria;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    public function __construct()
    {
      // PLEASE ADD the jwt.auth middleware to protect your resouce
        //$this->middleware('jwt.auth');
    }

    public function index()
    {
        //return response()->json(['status'=>'ok','data'=>Product::orderBy('idpro', 'desc')->get()], 200);
        return response()->json(['status'=>'ok','data'=>Categoria::orderBy('nombrecat', 'asc')->get()], 200);
    }

    public function store(Request $request)
    {
        // $validatedData = Validator::make($request->all(), [
        //     'nombrecat' => 'required | unique:categorias',
        //     'users_id' => 'required'
        // ]);

        // if ($validatedData->fails()) {
        //     $response = [
        //         'success' => false,
        //         'data' => 'Categoria Validation Error.',
        //         'message' => $validatedData->errors()
        //     ];
        //     return response()->json($response, 422);
        // }

        $input = $request->all();

        if(!isset($input['categoria_estado'])){
            $input['categoria_estado'] = 'active';
        }

        $categoria = Categoria::create($input);
        //$data = $categoria->salida;

        $response = [
            'success' => true,
            'data' => $categoria->id,
            'message' => 'Categoria registrada con exito.'
        ];

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria){
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra la categoria'])],404);
        }

        return response()->json(['status'=>'ok','data'=>$categoria], 200);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria){
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra la categoria con ese código.'])],404);
        }

        $input = $request->all();
        
        $categoria->nombrecat = $input['nombrecat'];
        $categoria->categoria_estado = $input['categoria_estado'];
        $categoria->icon = $input['icon'];
        $categoria->color = $input['color'];
        $categoria->touch();
        $categoria->save();

        $response = [
            'success' => true,            
            'message' => 'categoria actualizada con exito.'
        ];

        return response()->json($response, 200);
    }

    public function existeNombreCategoria(Request $request){
/*         $validatedData = Validator::make($request->all(), [
            'nombrecat' => 'unique:categorias'
        ]);

        if ($validatedData->fails()) {

            return response()->json([
                'success' => false,
                'data' => 'Validation Error.',
                'message' => $validatedData->errors()
            ], 422);
        } */

        $input = $request->all();
        $result = DB::table('categorias')
        ->select('id')
        ->where('users_id', $input['idu'])
        ->where('nombrecat', $input['nombrecat'])
        ->count();

        if ($result>0) {
            return response()->json([
                'success' => false,
                'data' => 'Categoria Validation Error.',
                'message' => 'Ya se registro:'.$input['nombrecat']
            ], 422);
        }

        $response = [
            'success' => true,
            'data'=>'Validation Passed',
            'message' => 'Nombre Categoria es valido.'
        ];

        return response()->json($response, 200);
    }

    public function existeNombreCategoria2(Request $request, $id){

        $validatedData = Validator::make($request->all(), [
            'nombrecat' => 'unique:categorias,'.$id
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        }

        $response = [
            'success' => true,
            'data'=>'Validation Passed',
            'message' => 'Nombre Categoria es valido.'
        ];

        return response()->json($response, 200);
    }
    
    public function existeNombreCategoria21(Request $request){

/*         $validatedData = Validator::make($request->all(), [
            'nombrecat' => 'required|unique:categorias,nombrecat,'.$cate->id.',id'
        ]);

        if ($validatedData->fails()) {
            $response = [
                'success' => false,
                'data' => 'Validation Error.',
                'message' => $validatedData->errors()
            ];
            return response()->json($response, 422);
        } */
        $input = $request->all();
        $result = DB::table('categorias')
        ->select('id')
        ->where('users_id', $input['idu'])
        ->where('id','!=', $input['id'])
        ->where('nombrecat', $input['nombrecat'])
        ->count();

        if ($result>0) {
            return response()->json([
                'success' => false,
                'data' => 'Categoria Validation Error.',
                'message' => 'Ya se registro:'.$input['nombrecat']
            ], 422);
        }

        $response = [
            'success' => true,
            'data'=>'Validation Passed',
            'message' => 'Nombre Categoria es valido.'
        ];

        return response()->json($response, 200);
    }

    public function getMisCategorias($idu)
    {
        $categorias = DB::table('categorias')->select('*')->where('users_id', $idu)->get();
        return response()->json(['status'=>'ok','data'=>$categorias], 200);
    }

    public function getMisCategoriasActivos($idu, $state){
        $categorias = DB::table('categorias')
        ->select('*')
        ->where('categoria_estado',$state)
        ->where('users_id', $idu)->get();
        return response()->json(['status'=>'ok','data'=>$categorias], 200);
    }

    public function getCategorias(Request $request){

/*         state: 'active', 
        idu: this.user.idu,
        negocio: this.user.nid,
        role: this.user.rl */

        $input = $request->all();
        $idu = $input['idu'];

        if($input['role']==2){
            $result = DB::table('users')
            ->join('negocios', 'negocios.id', '=', 'users.negocios_id')
            ->select('idu')
            ->where('users.negocios_id', $input['negocio'])
            ->where('users.rol', 'admin')
            ->first();
            $idu = $result->idu;
        }

        $categorias = DB::table('categorias')
        ->select('*')
        ->where('categoria_estado',$input['state'])
        ->where('users_id', $idu)->get();
        return response()->json(['status'=>'ok','data'=>$categorias], 200);
    }


}
