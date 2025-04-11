<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Veiculo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\User;
use App\Models\Cargo;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

class VeiculoController extends Controller
{
    public function index()
    {
        return response()->json(Veiculo::all(), 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }
    
        if ($user->cargo_id === null) {
            return response()->json(['error' => 'Cargo não encontrado.'], 404);
        }
    
        if ($user->cargo_id !== 1) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }
    
        $data = $request->validate([
            'placa' => 'required|string|unique:veiculos,placa',
            'chassi' => 'required|string|unique:veiculos,chassi',
            'status_veiculo' => 'required|string|in:disponível,em uso,manutenção',
            'ano' => 'required|integer',
            'cor' => 'required|string|max:30',
            'capacidade' => 'required|numeric',
            'obs_veiculo' => 'nullable|string',
            'km_revisao' => 'nullable|numeric',
            'marca' => 'required|string',
            'modelo' => 'required|string',
        ]);
    
        $marca = Marca::where('marca', $data['marca'])->first();
        $modelo = Modelo::where('modelo', $data['modelo'])->first();
    
        if (!$marca) {
            return response()->json(['error' => 'Marca inválida'], 400); 
        }
    
        if (!$modelo) {
            return response()->json(['error' => 'Modelo inválido'], 400);
        }
    
        DB::beginTransaction();
        try {
            $veiculo = Veiculo::create([
                'placa' => $data['placa'],
                'chassi' => $data['chassi'],
                'status_veiculo' => $data['status_veiculo'],
                'ano' => $data['ano'],
                'cor' => $data['cor'],
                'capacidade' => $data['capacidade'],
                'obs_veiculo' => $data['obs_veiculo'],
                'km_revisao' => $data['km_revisao'],
                'marca_id' => $marca->id,
                'modelo_id' => $modelo->id,
            ]);
    
            $qrcode = QrCode::generate($veiculo->id);
            $fileName = time() . '.svg';
            file_put_contents(public_path('qrcodes/' . $fileName), $qrcode);
    
            $veiculo->update(['qr_code' => $fileName]);
    
            DB::commit();
    
            return response()->json([
                'message' => 'Veículo criado com sucesso!',
                'veiculo' => $veiculo->load(['marca', 'modelo']),
                'qrcode' => $fileName,
            ], 201);
        } catch(\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'error' => 'Erro ao criar veículo.', 
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    public function show($id)
    {
        $veiculo = Veiculo::find($id);

        if (!$veiculo) {
            return response()->json(['error' => 'Veículo não encontrado.'], 404);
        }
        return response()->json($veiculo->load(['marca', 'modelo']), 200);
    }

    
    public function update(Request $request, $id)
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }
    
        if ($user->cargo_id === null) {
            return response()->json(['error' => 'Cargo não encontrado.'], 404);
        }
    
        if ($user->cargo_id !== 1) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }

        $veiculo = Veiculo::find($id);
        
        if (!$veiculo) {
            return response()->json(['error' => 'Veículo não encontrado.'], 404);
        }
        
        $data = $request->validate([
            'placa' => 'required|string|unique:veiculos,placa,' . $id,
            'chassi' => 'required|string|unique:veiculos,chassi,' . $id,
            'status_veiculo' => 'required|string|in:disponível,em uso,manutenção',
            'ano' => 'required|integer',
            'cor' => 'required|string|max:30',
            'capacidade' => 'required|numeric',
            'obs_veiculo' => 'nullable|string',
            'km_revisao' => 'nullable|numeric',
            'marca' => 'required|string',
            'modelo' => 'required|string',
        ]);
        
        $marca = Marca::where('marca', $data['marca'])->first();
        $modelo = Modelo::where('modelo', $data['modelo'])->first();
        
        if (!$marca) {
            return response()->json(['error' => 'Marca inválida'], 400);
        }
        
        if (!$modelo) {
            return response()->json(['error' => 'Modelo inválido'], 400);
        }
        
        DB::beginTransaction();
        try {
            $veiculo->update([
                'placa' => $data['placa'],
                'chassi' => $data['chassi'],
                'status_veiculo' => $data['status_veiculo'],
                'ano' => $data['ano'],
                'cor' => $data['cor'],
                'capacidade' => $data['capacidade'],
                'obs_veiculo' => $data['obs_veiculo'],
                'km_revisao' => $data['km_revisao'],
                'marca_id' => $marca->id,
                'modelo_id' => $modelo->id,
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Veículo atualizado com sucesso!',
                'veiculo' => $veiculo->load(['marca', 'modelo']),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'error' => 'Erro ao atualizar veículo.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }
    
        if ($user->cargo_id === null) {
            return response()->json(['error' => 'Cargo não encontrado.'], 404);
        }
    
        if ($user->cargo_id !== 1) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }

        $veiculo = Veiculo::find($id);
        
        if (!$veiculo) {
            return response()->json(['error' => 'Veículo não encontrado.'], 404);
        }
        
        $veiculo->delete();
        
        return response()->json(['message' => 'Veículo deletado com sucesso!'], 200);
    }
    
    public function disponivel(Request $request) {
        $veiculos = Veiculo::where('status_veiculo', 'disponível')
                           ->when($request->has('search'), function ($query) use ($request) {
                                $search = $request->input('search');
                                 $query->where('modelo', 'LIKE', "%{$search}%" )
                                       ->orWhere('marca', 'LIKE', "%{$search}%" );
                             })
                             ->get();

        if ($veiculos->isEmpty()){
            return response()->json(['error' => 'Nenhum veículo disponível encontrado.'], 404);
        }

        return response()->json([
            'message' => 'Veículos disponíveis', 
            'veiculos' => $veiculos->load(['marca', 'modelo']),
        ],200);
    }

    // public function manutencao() {
    //     $user = auth()->user();

    //     $veiculo = Veiculo::find($id);

    //     if ($veiculo->km_revisao <=0 ) {
    //         $veiculo->status_veiculo = "manutenção";
    //         $veiculo->save();
    //     }
    // }
}