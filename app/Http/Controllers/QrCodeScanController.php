<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Solicitar;
use App\Http\Controllers\SolicitarController;
use App\Http\Controllers\VeiculoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QrCodeScanController extends Controller
{
    public function handleScan(Request $request, Veiculo $veiculo)
    {
        $user = Auth::user();

        Log::info("QR Scan: User {$user->id} scanned Veiculo {$veiculo->id}");

        $solicitacaoAgendada = Solicitar::where('user_id', $user->id)
            ->where('status', 'aprovada') 
            ->whereDate('prev_data_inicio', '<=', now()) 
            ->whereDate('prev_data_fim', '>=', now())   
            ->whereNull('hora_inicio') 
            ->orderBy('data_inicio', 'asc') 
            ->first();

        if ($solicitacaoAgendada) {
             Log::info("QR Scan: User {$user->id} has scheduled request {$solicitacaoAgendada->id}");
           
            if ($solicitacaoAgendada->veiculo_id === $veiculo->id) {
                 Log::info("QR Scan: Correct vehicle for request {$solicitacaoAgendada->id}. Ready to start.");
                return response()->json([
                    'action' => 'allow_start', 
                    'message' => 'Este é o seu veículo agendado. Você pode iniciar a viagem.',
                    'solicitacao_id' => $solicitacaoAgendada->id,
                    'veiculo' => $veiculo->load(['marca', 'modelo']) 
                ]);
            } else {
                 Log::warning("QR Scan: Incorrect vehicle scanned. User {$user->id} expected vehicle {$solicitacaoAgendada->veiculo_id} for request {$solicitacaoAgendada->id}.");
                return response()->json([
                    'action' => 'error',
                    'message' => 'Este não é o veículo que você agendou. Verifique sua solicitação.'
                ], 409); 
            }
        } else {
             Log::info("QR Scan: User {$user->id} has no active scheduled request. Checking vehicle {$veiculo->id} status: {$veiculo->status_veiculo}");
            if ($veiculo->status_veiculo === 'disponível') {
                 Log::info("QR Scan: Vehicle {$veiculo->id} is available. Prompting for urgent request.");
                return response()->json([
                    'action' => 'prompt_urgent_request', 
                    'message' => 'Veículo disponível. Gostaria de fazer uma solicitação de uso imediato?',
                    'veiculo_id' => $veiculo->id,
                    'veiculo' => $veiculo->load(['marca', 'modelo'])
                ]);
            } else {
                 Log::warning("QR Scan: Vehicle {$veiculo->id} is not available (status: {$veiculo->status_veiculo}).");
                return response()->json([
                    'action' => 'error',
                    'message' => 'Veículo indisponível no momento (' . $veiculo->status_veiculo . ').'
                ], 409); 
            }
        }
    }
}
