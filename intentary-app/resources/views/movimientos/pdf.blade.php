<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Movimientos de Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .entrada {
            color: #28a745;
            font-weight: bold;
        }
        .salida {
            color: #dc3545;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Movimientos de Inventario</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Total de registros: {{ $movimientos->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Proveedor/Proyecto</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $movimiento)
                <tr>
                    <td class="text-center">
                        {{ $movimiento->fecha_hora->format('d/m/Y H:i') }}
                    </td>
                    <td class="text-center">
                        <span class="{{ $movimiento->tipo == 'entrada' ? 'entrada' : 'salida' }}">
                            {{ ucfirst($movimiento->tipo) }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $movimiento->producto->codigo ?? 'N/A' }}</strong><br>
                        {{ $movimiento->producto->nombre ?? 'Producto no encontrado' }}
                    </td>
                    <td class="text-center {{ $movimiento->tipo == 'entrada' ? 'entrada' : 'salida' }}">
                        {{ $movimiento->tipo == 'entrada' ? '+' : '-' }}{{ number_format($movimiento->cantidad, 2) }}
                        {{ $movimiento->producto->unidad ?? '' }}
                    </td>
                    <td>
                        @if($movimiento->proveedor)
                            {{ $movimiento->proveedor->nombre }}
                        @elseif($movimiento->proyecto)
                            {{ $movimiento->proyecto->nombre }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $movimiento->usuario->nombre ?? 'Usuario no encontrado' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión de Inventario - {{ config('app.name') }}</p>
    </div>
</body>
</html>