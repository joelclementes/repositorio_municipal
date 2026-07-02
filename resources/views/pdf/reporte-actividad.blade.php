<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora de Actividad del Sistema</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 7.5pt;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #6c143a;
            padding-bottom: 8px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            border: none;
            vertical-align: middle;
        }

        .header-title {
            font-size: 11pt;
            font-weight: bold;
            color: #6c143a;
            margin: 0;
        }

        .header-subtitle {
            font-size: 9pt;
            font-weight: bold;
            color: #333;
            margin: 2px 0 0 0;
        }

        .header-meta {
            font-size: 8pt;
            text-align: right;
            color: #666;
        }

        table.datos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.datos th {
            background-color: #6c143a;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 6px 5px;
            font-size: 8pt;
            border: 1px solid #6c143a;
        }

        table.datos td {
            border: 1px solid #ddd;
            padding: 5px 5px;
            vertical-align: top;
        }

        table.datos tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .bg-login { background-color: #dbeafe; color: #1e40af; }
        .bg-create { background-color: #dcfce7; color: #166534; }
        .bg-update { background-color: #fef3c7; color: #92400e; }
        .bg-delete { background-color: #fee2e2; color: #991b1b; }
        .bg-gray { background-color: #f3f4f6; color: #374151; }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 7pt;
            text-align: center;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="header-title">Secretaría de Fiscalización - SIFOM</h1>
                    <h2 class="header-subtitle">Bitácora de Actividades (Spatie Activitylog)</h2>
                </td>
                <td class="header-meta">
                    Fecha de emisión: {{ $fechaGeneracion }} hrs<br>
                    Registros emitidos: {{ count($actividades) }}
                </td>
            </tr>
        </table>
    </div>

    <table class="datos">
        <thead>
            <tr>
                <th style="width: 12%">Fecha / Hora</th>
                <th style="width: 18%">Usuario</th>
                <th style="width: 18%">Origen / Rol</th>
                <th style="width: 15%">Acción</th>
                <th style="width: 27%">Descripción</th>
                <th style="width: 10%">Dirección IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($actividades as $actividad)
                <tr>
                    <td>{{ $actividad->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <strong>{{ $actividad->causer?->name ?? 'Sistema' }}</strong><br>
                        <span style="color: #666; font-size: 7pt;">{{ $actividad->causer?->email }}</span>
                    </td>
                    <td>
                        @if($actividad->causer)
                            @if($actividad->causer->hasAnyRole(['SuperUsuario', 'Administrador', 'Revisor']))
                                {{ $actividad->causer->roles->first()?->name ?? 'Congreso' }} (Congreso)
                            @else
                                {{ $actividad->causer->ente?->nombre ?? 'N/A' }} (Municipio)
                            @endif
                        @else
                            Sistema / Proceso
                        @endif
                    </td>
                    <td>
                        @php
                            $action = $actividad->log_name;
                            $class = 'bg-gray';
                            if (str_contains($action, 'Inicio de sesión')) {
                                $class = 'bg-login';
                            } elseif (str_contains($action, 'Aprobación') || str_contains($action, 'Creación')) {
                                $class = 'bg-create';
                            } elseif (str_contains($action, 'Rechazo') || str_contains($action, 'Eliminación')) {
                                $class = 'bg-delete';
                            } elseif (str_contains($action, 'Actualización') || str_contains($action, 'Carga')) {
                                $class = 'bg-update';
                            }
                        @endphp
                        <span class="badge {{ $class }}">{{ $action }}</span>
                    </td>
                    <td>{{ $actividad->description }}</td>
                    <td><code>{{ $actividad->getExtraProperty('ip') ?? 'N/A' }}</code></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        No se encontraron actividades registradas con los criterios seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Reporte de Auditoría de Sistemas de SIFOM - Congreso del Estado de Veracruz
    </div>

</body>
</html>
