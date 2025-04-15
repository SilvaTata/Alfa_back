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

        // 1. Verificar se o usuário tem uma solicitação agendada ativa
        //    A lógica exata de "agendada" pode variar (hoje? status específico?)
        //    Exemplo: Busca solicitação agendada para HOJE que ainda não começou
        $solicitacaoAgendada = Solicitar::where('user_id', $user->id)
            ->where('status', 'aprovada') 
            // ->whereDate('data_inicio', '<=', now()) // Verifica se a data de início é hoje ou antes
            // ->whereDate('data_fim', '>=', now())   // Verifica se a data de fim é hoje ou depois
            // ->whereNull('hora_inicio_real') // Garante que ainda não começou
            ->orderBy('data_inicio', 'asc') 
            ->first();

        if ($solicitacaoAgendada) {
             Log::info("QR Scan: User {$user->id} has scheduled request {$solicitacaoAgendada->id}");
            // Se tem agendada, verificar se o veículo é o correto
            if ($solicitacaoAgendada->veiculo_id === $veiculo->id) {
                // ✅ Veículo correto! Indicar que pode iniciar o uso.
                // O frontend pode então chamar a rota /solicitar/{id}/iniciar
                 Log::info("QR Scan: Correct vehicle for request {$solicitacaoAgendada->id}. Ready to start.");
                return response()->json([
                    'action' => 'allow_start', // Ação que o frontend deve entender
                    'message' => 'Este é o seu veículo agendado. Você pode iniciar a viagem.',
                    'solicitacao_id' => $solicitacaoAgendada->id,
                    'veiculo' => $veiculo->load(['marca', 'modelo']) // Envia dados do veículo se útil
                ]);
            } else {
                 Log::warning("QR Scan: Incorrect vehicle scanned. User {$user->id} expected vehicle {$solicitacaoAgendada->veiculo_id} for request {$solicitacaoAgendada->id}.");
                return response()->json([
                    'action' => 'error',
                    'message' => 'Este não é o veículo que você agendou. Verifique sua solicitação.'
                ], 409); // 409 Conflict é uma boa opção
            }
        } else {
            // 2. Se não tem solicitação agendada, verificar status do veículo
             Log::info("QR Scan: User {$user->id} has no active scheduled request. Checking vehicle {$veiculo->id} status: {$veiculo->status_veiculo}");
            if ($veiculo->status_veiculo === 'disponível') {
                // ✅ Veículo disponível! Indicar que pode fazer solicitação urgente.
                // O frontend pode direcionar para a tela de nova solicitação, pré-preenchendo o veículo
                 Log::info("QR Scan: Vehicle {$veiculo->id} is available. Prompting for urgent request.");
                return response()->json([
                    'action' => 'prompt_urgent_request', // Ação para o frontend
                    'message' => 'Veículo disponível. Gostaria de fazer uma solicitação de uso imediato?',
                    'veiculo_id' => $veiculo->id,
                    'veiculo' => $veiculo->load(['marca', 'modelo'])
                ]);
            } else {
                // ❌ Veículo indisponível (em uso, manutenção, etc.)
                 Log::warning("QR Scan: Vehicle {$veiculo->id} is not available (status: {$veiculo->status_veiculo}).");
                return response()->json([
                    'action' => 'error',
                    'message' => 'Veículo indisponível no momento (' . $veiculo->status_veiculo . ').'
                ], 409); // 409 Conflict
            }
        }
    }


   
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
