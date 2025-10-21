<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f9fafb;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9fafb; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 30px 20px; text-align: center; border-bottom: 3px solid #16a34a;">
                            <h1 style="margin: 0; color: #16a34a; font-size: 32px;">☕ Menú Coffee</h1>
                            <p style="margin: 10px 0 0; color: #6b7280; font-size: 14px;">Tu café de confianza</p>
                        </td>
                    </tr>

                    <!-- Saludo -->
                    <tr>
                        <td style="padding: 30px 30px 20px;">
                            <h2 style="margin: 0 0 15px; color: #333; font-size: 24px;">¡Hola {{ $cliente['nombre'] }}! 👋</h2>
                            <p style="margin: 0; color: #555; line-height: 1.6; font-size: 16px;">
                                Gracias por tu compra. Hemos recibido tu pedido correctamente y está siendo preparado.
                            </p>
                        </td>
                    </tr>

                    <!-- Número de Pedido -->
                    @if(isset($numeroPedido))
                    <tr>
                        <td style="padding: 0 30px 20px;">
                            <div style="background-color: #dcfce7; border-left: 4px solid #16a34a; padding: 15px; border-radius: 6px;">
                                <p style="margin: 0; color: #166534; font-size: 14px;">
                                    <strong>Número de Pedido:</strong> <span style="font-size: 18px;">#{{ $numeroPedido }}</span>
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    <!-- Resumen del Pedido -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px;">
                                <h3 style="margin: 0 0 15px; color: #333; font-size: 18px;">📋 Resumen del Pedido</h3>
                                
                                @foreach($pedido as $item)
                                <div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                        <div style="flex: 1;">
                                            <strong style="color: #333; font-size: 15px;">{{ $item['nombre'] }}</strong>
                                            <span style="color: #6b7280;"> x {{ $item['quantity'] }}</span>
                                            
                                            @if(isset($item['tamano']) && $item['tamano'])
                                                <br><small style="color: #6b7280; font-size: 13px;">• Tamaño: {{ ucfirst($item['tamano']) }}</small>
                                            @endif
                                            @if(isset($item['leche']) && $item['leche'])
                                                <br><small style="color: #6b7280; font-size: 13px;">• Leche: {{ $item['leche'] }}</small>
                                            @endif
                                            @if(isset($item['extras']) && is_array($item['extras']) && count($item['extras']) > 0)
                                                <br><small style="color: #6b7280; font-size: 13px;">• Extras: {{ implode(', ', $item['extras']) }}</small>
                                            @endif
                                        </div>
                                        <div>
                                            <strong style="color: #16a34a; font-size: 16px;">${{ number_format($item['subtotal'], 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Total -->
                                <div style="padding-top: 15px; text-align: right;">
                                    <span style="color: #6b7280; font-size: 16px;">Total: </span>
                                    <strong style="color: #16a34a; font-size: 24px;">${{ number_format(array_sum(array_column($pedido, 'subtotal')), 2) }}</strong>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Adjunto PDF -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 6px;">
                                <p style="margin: 0; color: #1e40af; font-size: 14px;">
                                    📎 <strong>Adjunto:</strong> Encontrarás el recibo completo en formato PDF adjunto a este correo.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; text-align: center; border-top: 2px solid #e5e7eb;">
                            <p style="margin: 0 0 10px; color: #16a34a; font-size: 18px; font-weight: bold;">
                                ¡Gracias por tu preferencia!
                            </p>
                            <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                Menú Coffee - Tu café de confianza
                            </p>
                            <p style="margin: 15px 0 0; color: #9ca3af; font-size: 12px;">
                                Si tienes alguna pregunta, no dudes en contactarnos.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>