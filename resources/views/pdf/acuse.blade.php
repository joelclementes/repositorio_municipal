<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acuse de Documentos Validados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            margin: 10px 5px 30px 5px;
        }

        .top-header {
            width: 100%;
            margin-bottom: 10px;
        }

        .top-header table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .top-header td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 120px;
            text-align: left;
        }

        .logo {
            width: 100px;
            height: auto;
            display: block;
        }

        .titulo-sistema {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            white-space: nowrap;
            padding-left: 5px;
        }

        .folio {
            text-align: right;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .ente {
            font-size: 16px;
            margin-bottom: 25px;
            line-height: 1.4;
        }

        .titulo-documento {
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .titulo-documento .titulo1 {
            font-size: 16px;
            font-weight: bold;
        }

        .titulo-documento .titulo2 {
            font-size: 16px;
            font-weight: bold;
        }

        .titulo-documento .detalle {
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .mensaje-vacio {
            text-align: center;
            color: #999;
            margin-top: 50px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <div class="top-header">
        <table>
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('assets/images/LOGO_LEGISLATURA.jpg') }}" alt="Logo" class="logo">
                </td>
                <td class="titulo-sistema">
                    SISTEMA DE INFORMACIÓN FINANCIERA Y OBRA PÚBLICA
                </td>
            </tr>
        </table>
    </div>

    <div class="folio">
        NÚM. RECIBO: {{ $numero_recibo ?? 'XXXX' }}
    </div>

    <div class="ente">
        {{ $nombre_ente ?? 'XXXX' }}<br>
        PRESENTE
    </div>

    <div class="titulo-documento">
        <div class="titulo1">ACUSE DE ACEPTACIÓN DE DOCUMENTOS</div>
        <div class="titulo2">INTEGRACIÓN DEL ESTADO FINANCIERO MENSUAL</div>
        <div class="detalle"><strong>PERIODO:</strong> {{ $periodo ?? 'XXXX' }}</div>
        <div class="detalle"><strong>FECHA DE RECEPCIÓN:</strong> {{ $fecha_recepcion ?? 'XXXX' }}</div>
    </div>

    @if(count($data) > 0 && !empty($data[0]['documento_validado']))
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Documento validado</th>
                    <th rowspan="2">Tipo de archivo</th>
                    <th colspan="2">ACEPTACIÓN</th>
                </tr>
                <tr>
                    <th>FECHA</th>
                    <th>HORA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    <td>{{ $row['documento_validado'] }}</td>
                    <td>{{ $row['tipo_archivo'] }}</td>
                    <td>{{ $row['fecha'] }}</td>
                    <td>{{ $row['hora'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="mensaje-vacio">No se encontraron archivos para el período seleccionado.</p>
    @endif

</body>
</html>