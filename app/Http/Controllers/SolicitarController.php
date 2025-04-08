<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Models\Veiculo;
use App\Models\Solicitar;
use App\Models\HistSolicitar;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Cargo;

class SolicitarController extends Controller
{
    public function index()
    {
       $user = Auth::user();
    
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }
    
        if ($user->cargo_id === null) {
            return response()->json(['error' => 'Cargo não encontrado.'], 404);
        }

        if ($user->cargo_id === 1) {
            $veiculos = Veiculo::where('status_veiculo', 'em uso')->get();
            if ($veiculos->isEmpty()) {
                return response()->json(['error' => 'Nenhum veículo em uso encontrado.'], 404);
            }

            return response()->json($veiculos, 200);
        } else {
            $solicitadosDoUsuario = Veiculo::where('status_veiculo', 'em uso')
                                            ->whereHas('solicitars', function ($query) use ($user) {
                                                $query->where('user_id', $user->id)
                                                      ->where('situacao', 'aceita');
                                            })
                                            ->get();
            if ($solicitadosDoUsuario->isEmpty()) {
                return response()->json(['error' => 'Você não possui veículos em uso no momento.'], 404);
            }

            return response()->json($solicitadosDoUsuario, 200);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'veiculo_id' => 'required|exists:veiculos,id',
            'prev_hora_inicio' => 'required|date_format:H:i',
            'prev_data_inicio' => 'required|date_format:Y-m-d',
            'prev_hora_final' => 'required|date_format:H:i',
            'prev_data_final' => 'required|date_format:Y-m-d',
            'motivo' => 'required|string|max:255',
        ]);

        $data['user_id'] = Auth::id();

        DB::beginTransaction();
        try {
            $veiculo = Veiculo::findOrFail($data['veiculo_id']);

            if ($veiculo->status_veiculo !== 'disponível') {
                return response()->json(['error' => 'Veículo não disponível.'], 400);
            }

            $veiculo->status_veiculo = 'em uso';
            $veiculo->save();

            $solicitar = new Solicitar();
            $solicitar->user_id = $data['user_id'];
            $solicitar->veiculo_id = $data['veiculo_id'];
            $solicitar->prev_hora_inicio = $data['prev_hora_inicio'];
            $solicitar->prev_data_inicio = $data['prev_data_inicio'];
            $solicitar->prev_hora_final = $data['prev_hora_final'];
            $solicitar->prev_data_final = $data['prev_data_final'];
            $solicitar->motivo = $data['motivo'];
            $solicitar->save();

            DB::commit();
 
            return response()->json([
                'message' => 'Solicitação criada com sucesso.',
                'solicitacao' => $solicitar,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao atualizar o veículo.'], 500);
        }  
    }

    public function show($id)
    {
        $solicitar = Solicitar::find($id);

        if (!$solicitar) {
            return response()->json(['error' => 'Solicitação não encontrada.'], 404);
        }

        return response()->json($solicitar, 200);
    }

    public function aceitar(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->cargo_id !== 1) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }

        $solicitar = Solicitar::find($id);

        if (!$solicitar) {
            return response()->json(['error' => 'Solicitação não encontrada.'], 404);
        }

        $solicitar->situacao = 'aceita';
        $solicitar->save();

        $histSolicitar = new HistSolicitar();
        $histSolicitar->solicitacao_id = $solicitar->id;
        $histSolicitar->hora_aceito = now()->format('H:i');
        $histSolicitar->data_aceito = now()->format('Y-m-d');
        $histSolicitar->adm_id = $user->id;
        $histSolicitar->save();

        return response()->json(['message' => 'Solicitação aceita com sucesso.'], 200);
    }

    public function recusar(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->cargo_id !== 1) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }

        $solicitar = Solicitar::find($id);

        if (!$solicitar) {
            return response()->json(['error' => 'Solicitação não encontrada.'], 404);
        }

        $solicitar->situacao = 'aceita';
        $solicitar->save();

        $histSolicitar = new HistSolicitar();
        $histSolicitar->solicitacao_id = $solicitar->id;
        $histSolicitar->hora_aceito = now()->format('H:i');
        $histSolicitar->data_aceito = now()->format('Y-m-d');
        $histSolicitar->adm_id = $user->id;
        $histSolicitar->save();

        return response()->json(['message' => 'Solicitação aceita com sucesso.'], 200);
    }

    public function destroy($id)
    {
        //
    }
}
