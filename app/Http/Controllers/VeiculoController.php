<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationExcepion;
use App\Models\Veiculo;
use App\Models\Marca;
use App\Models\Modelo;


class VeiculoController extends Controller
{
    public function index()
    {
        return response()->json(Veiculo::all(), 200);
    }

    public function store(Request $request)
    {
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
        if(!$marca) {
           return response()->json(['error' => 'Marca inválida'], 400); 
        }

        if(!$modelo) 
        {
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
            $filleName = time() . '.svg';
            file_put_contents(public_path('qrcodes/' . $filleName), $qrcode);

            $veiculo->update(['qr_code' => $filleName]);

            DB::commit();

            return response()->json(['message' => 'Veículo criado com sucesso!'], 201);
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
        return response()->json($veiculo, 200);
    }

    public function status($status) {
        $statusValidos = ['disponível', 'em uso', 'manutenção'];

        if (!in_array($status, $statusValidos)) {
            return response()->json(['error' => 'Status inválido'], 400);
        }

        $veiculos = Veiculo::where('status_veiculo', $status)->get();

        if ($veiculos->isEmpty()) {
            return response()->json(['message' => 'Nenhum veículo encontrado com esse status.'], 404);
        }

        return response()->json($veiculos, 200);
    }

    public function update(Request $request, $id)
    {
        $veiculo = Veiculo::find($id);

        if (!$veiculo) {
            return response()->json(['error' => 'Veículo não encontrado.'], 404);
        }

        $request->validate([
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
    }

    public function destroy($id)
    {
        //
    }
}
