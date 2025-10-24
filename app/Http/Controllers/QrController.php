<?php

namespace App\Http\Controllers;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Http\Request;

class QrController extends Controller
{
    public function index(Request $request)
    {
        $mesa = $request->input('mesa', 'default');
        $url = url("/?mesa={$mesa}");

        // Crear el código QR
        $builder = new Builder(
            writer: new PngWriter(),
            data: $url,
            encoding: new Encoding('UTF-8'),

            size: 250,
            margin: 8
        );

        $result = $builder->build();

         $qrBase64 = base64_encode($result->getString());

        return view('QrGenerator.indexQr', [
            'qr' => $qrBase64,
            'mesa' => $mesa,
            'url' => $url,
        ]);
    }
}
