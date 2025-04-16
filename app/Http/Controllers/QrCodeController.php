<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function index()
    {
        $data = [
            [
                "id" => 1,
                "modelo" => "texeirao",
                "placa" => "ABC...",
                "ano" => 2025,
                "capacidade" => 32,
                "dt_prox_manu" => null,
                "dt_ultim_manu" => null,
                "id_empresa" => 1,
                "status" => "ativo",
                "id_tipo_veiculo" => 1
            ]
        ];

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $qrCode = QrCode::size(300)->generate($json);

        return view('qr.index', compact('qrCode', 'json'));
    }

    public function json()
    {
        $data = [
            [
                "id" => 1,
                "modelo" => "texeirao",
                "placa" => "ABC...",
                "ano" => 2025,
                "capacidade" => 32,
                "dt_prox_manu" => null,
                "dt_ultim_manu" => null,
                "id_empresa" => 1,
                "status" => "ativo",
                "id_tipo_veiculo" => 1
            ]
        ];

        return response()->json($data);
    }

}
