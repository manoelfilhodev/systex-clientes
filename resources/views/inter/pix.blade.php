<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento PIX</title>
    <style>
        :root {
            --verde: #27ae60;
            --cinza: #f4f6f8;
            --escuro: #1e1e1e;
            --texto: #333;
        }

        body {
            background-color: var(--cinza);
            font-family: "Segoe UI", sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: var(--texto);
        }
        .card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
            text-align: center;
            width: 380px;
            transition: 0.3s;
        }
        h2 { color: #111; margin-bottom: 10px; }
        .valor {
            font-size: 28px;
            font-weight: bold;
            color: var(--verde);
            margin-bottom: 10px;
        }
        .descricao {
            margin-bottom: 20px;
            color: #555;
        }
        img {
            margin: 10px 0;
            border: 4px solid var(--verde);
            border-radius: 10px;
        }
        .pix-code {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            font-size: 12px;
            color: #555;
            word-break: break-all;
            margin-bottom: 10px;
        }
        button {
            background-color: var(--verde);
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover { background-color: #219150; }
        .copiado {
            display: none;
            margin-top: 10px;
            color: var(--verde);
            font-weight: bold;
            font-size: 13px;
        }
        .erro {
            color: red;
            font-weight: bold;
            background: #fee;
            padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="card">
        @if(isset($erro))
            <div class="erro">⚠️ {{ $erro }}</div>
        @else
            <h2>Pagamento PIX</h2>
            <div class="valor">R$ {{ $valor }}</div>
            <p class="descricao">{{ $descricao }}</p>

            @if($imagem)
                <img src="{{ $imagem }}" width="200" height="200" alt="QR Code PIX">
            @endif

            <div class="pix-code" id="pixCode">
                {{ $qrcode ?: ($raw_resposta['loc']['location'] ?? 'QR Code não retornado.') }}
            </div>

            <button onclick="copiarPix()">📋 Copiar Código PIX</button>
            <div id="msgCopiado" class="copiado">✅ Código copiado!</div>

            <p style="margin-top:10px;font-size:12px;color:#888;">
                TXID: {{ $txid }}
            </p>
        @endif
    </div>

    <script>
        function copiarPix() {
            const codigo = document.getElementById('pixCode').innerText.trim();
            navigator.clipboard.writeText(codigo).then(() => {
                const msg = document.getElementById('msgCopiado');
                msg.style.display = 'block';
                setTimeout(() => msg.style.display = 'none', 2000);
            });
        }
    </script>
</body>
</html>
