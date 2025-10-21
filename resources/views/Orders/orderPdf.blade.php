<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido - {{ $cliente['nombre'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            padding: 30px;
            font-size: 14px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #16a34a;
            font-size: 32px;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 16px;
        }
        .info-box {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #16a34a;
        }
        .info-box strong {
            color: #16a34a;
            display: inline-block;
            width: 120px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        thead {
            background-color: #16a34a;
            color: white;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        tbody tr:hover {
            background-color: #f9fafb;
        }
        .producto-nombre {
            font-weight: bold;
            color: #1f2937;
        }
        .item-detalles {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
            line-height: 1.4;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
        }
        .total-label {
            font-size: 16px;
            color: #6b7280;
            margin-right: 10px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #16a34a;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            color: #6b7280;
        }
        .footer p {
            margin-bottom: 5px;
        }
        .footer .thank-you {
            font-size: 18px;
            color: #16a34a;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>☕ Menú Coffee</h1>
        <p>Recibo de Compra</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <strong>Pedido #:</strong> {{ $numeroPedido ?? 'N/A' }}
        </div>
        <div class="info-row">
            <strong>Cliente:</strong> {{ $cliente['nombre'] }}
        </div>
        <div class="info-row">
            <strong>Email:</strong> {{ $cliente['email'] }}
        </div>
        <div class="info-row">
            <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Producto</th>
                <th style="width: 15%; text-align: center;">Cantidad</th>
                <th style="width: 15%; text-align: right;">Precio Unit.</th>
                <th style="width: 20%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido as $item)
            <tr>
                <td>
                    <div class="producto-nombre">{{ $item['nombre'] }}</div>
                    @if(isset($item['tamano']) && $item['tamano'])
                        <div class="item-detalles">• Tamaño: {{ ucfirst($item['tamano']) }}</div>
                    @endif
                    @if(isset($item['leche']) && $item['leche'])
                        <div class="item-detalles">• Leche: {{ $item['leche'] }}</div>
                    @endif
                    @if(isset($item['extras']) && is_array($item['extras']) && count($item['extras']) > 0)
                        <div class="item-detalles">• Extras: {{ implode(', ', $item['extras']) }}</div>
                    @endif
                </td>
                <td style="text-align: center;">{{ $item['quantity'] }}</td>
                <td style="text-align: right;">${{ number_format($item['precio'], 2) }}</td>
                <td style="text-align: right;">${{ number_format($item['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <span class="total-label">TOTAL:</span>
        <span class="total-amount">${{ number_format(array_sum(array_column($pedido, 'subtotal')), 2) }}</span>
    </div>

    <div class="footer">
        <p class="thank-you">¡Gracias por tu preferencia!</p>
        <p>Menú Coffee - Tu café de confianza</p>
        <p style="font-size: 11px; margin-top: 15px;">Este documento es un comprobante de tu pedido</p>
    </div>
</body>
</html>