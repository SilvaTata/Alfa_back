<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>QR Code com JSON</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 50px;
        }
        .qr-container {
            margin: 20px auto;
        }
        .json {
            margin-top: 30px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            text-align: left;
            font-family: monospace;
            white-space: pre-wrap;
            word-break: break-word;
        }
    </style>
</head>
<body>
    <h1>QR Code com JSON</h1>

    <div class="qr-container">
        {!! $qrCode !!}
    </div>

    <div class="json">
        {{ $json }}
    </div>
</body>
</html>
