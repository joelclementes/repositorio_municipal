<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Obligaciones Municipales - {{ $datos['ente']->nombre }}</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            color: #000;
            font-size: 7pt;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 10px;
        }

        .header table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .header td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        .header-text {
            padding-left: 10px;
        }

        .header-text p {
            margin: 1px 0;
        }

        .header-title {
            font-size: 10pt;
            font-weight: bold;
            color: #6c143a;
        }

        .header-subtitle {
            font-size: 8pt;
            font-weight: bold;
        }

        .header-info {
            font-size: 8pt;
        }

        .header-ente {
            font-size: 9pt;
            font-weight: bold;
            color: #6c143a;
        }

        .categoria-header {
            background-color: #2e7d32;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            padding: 4px;
            margin-top: 8px;
        }

        .subcategoria-header {
            background-color: #4caf50;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 7pt;
            padding: 3px;
        }

        table.reporte {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        table.reporte th,
        table.reporte td {
            border: 1px solid #999;
            padding: 2px 3px;
            text-align: center;
            font-size: 6.5pt;
            vertical-align: middle;
        }

        table.reporte th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 6.5pt;
        }

        table.reporte td.doc-nombre {
            text-align: left;
            min-width: 120px;
        }

        table.reporte td.obs {
            text-align: left;
            font-size: 6pt;
            min-width: 120px;
            color: #333;
        }

        .estado-p {
            color: #2e7d32;
            font-weight: bold;
        }

        .estado-np {
            color: #d32f2f;
            font-weight: bold;
        }

        .no-aplica {
            background-color: #d9d9d9;
        }

        .obs-texto {
            color: #c62828;
            font-size: 6pt;
            margin: 1px 0;
        }

        .footer {
            margin-top: 10px;
            font-size: 7pt;
            font-style: italic;
            color: #666;
        }

        .page-break {
            page-break-before: always;
        }

        .leyenda {
            font-size: 7pt;
            margin: 5px 0 10px 0;
            color: #333;
        }

        .leyenda span {
            margin-right: 15px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <table>
            <tr>
                <td style="width: 90px;">
                    <img src="{{ public_path('assets/images/LOGO_LEGISLATURA.jpg') }}" alt="Logo" class="logo">
                </td>
                <td class="header-text">
                    <p class="header-title">Secretaría de Fiscalización</p>
                    <p class="header-subtitle">Departamento de Capacitación, Asesoría, Revisión y Supervisión a Municipios</p>
                    <p class="header-subtitle">Reporte de Obligaciones Municipales</p>
                    <p class="header-info">Ayuntamiento: <span class="header-ente">{{ $datos['ente']->nombre }}</span></p>
                    <p class="header-info">Periodo: enero a diciembre {{ $datos['axo'] }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="leyenda">
        <span><strong>P</strong> = Presentado (≥80% aprobado)</span>
        <span><strong>NP</strong> = No Presentado</span>
        <span style="background-color: #d9d9d9; padding: 1px 6px;">  </span> = No aplica
    </div>

    @foreach ($datos['categorias'] as $catIndex => $categoria)
        @if ($catIndex > 0)
            <div style="margin-top: 10px;"></div>
        @endif

        <div class="categoria-header">{{ $categoria['nombre'] }}</div>

        @foreach ($categoria['subcategorias'] as $subcategoria)
            <div class="subcategoria-header">{{ $subcategoria['nombre'] }}</div>

            <table class="reporte">
                <thead>
                    <tr>
                        <th style="width: 20px;">#</th>
                        <th class="doc-nombre">Documento</th>

                        @if ($subcategoria['tipo_periodo'] === 'trimestral')
                            <th style="width: 35px;">1er. Trim.</th>
                            <th style="width: 35px;">2do. Trim.</th>
                            <th style="width: 35px;">3er. Trim.</th>
                            <th style="width: 35px;">4to. Trim.</th>
                        @else
                            <th style="width: 22px;">ene</th>
                            <th style="width: 22px;">feb</th>
                            <th style="width: 22px;">mar</th>
                            <th style="width: 22px;">abr</th>
                            <th style="width: 22px;">may</th>
                            <th style="width: 22px;">jun</th>
                            <th style="width: 22px;">jul</th>
                            <th style="width: 22px;">ago</th>
                            <th style="width: 22px;">sep</th>
                            <th style="width: 22px;">oct</th>
                            <th style="width: 22px;">nov</th>
                            <th style="width: 22px;">dic</th>
                        @endif

                        <th class="obs">Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subcategoria['documentos'] as $index => $documento)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="doc-nombre">{{ $documento['nombre'] }}</td>

                            @foreach ($documento['meses'] as $estadoData)
                                <td class="{{ $estadoData['clase'] === 'no-aplica' ? 'no-aplica' : '' }}">
                                    @if ($estadoData['estado'] === 'P')
                                        <span class="estado-p">P</span>
                                    @elseif ($estadoData['estado'] === 'NP')
                                        <span class="estado-np">NP</span>
                                    @endif
                                </td>
                            @endforeach

                            <td class="obs">
                                @foreach ($documento['observaciones'] as $obs)
                                    <p class="obs-texto">{{ $obs['texto'] }}</p>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endforeach

    <div class="footer">
        <p><strong>OBSERVACIÓN GENERAL:</strong></p>
        <p>Reporte generado automáticamente el {{ $fechaGeneracion }} hrs.
        Criterio: Un documento se considera "Presentado" (P) si al menos el 80% de los archivos asociados cuentan con estado "Aprobado".</p>
    </div>
</body>
</html>
