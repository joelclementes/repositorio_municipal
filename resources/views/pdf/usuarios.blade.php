<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header .subtitle {
            color: #666;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .filters {
            margin-bottom: 15px;
            font-size: 11px;
            color: #666;
        }
        .filters span {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Listado de Usuarios</h1>
        <div class="subtitle">
            Generado el: {{ $generated_at }} | Total: {{ $total }} usuarios
        </div>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <strong>Filtros aplicados:</strong>
        @foreach($filters as $key => $value)
            <span>{{ $key }}: {{ $value }}</span>
            @if(!$loop->last), @endif
        @endforeach
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Ente</th>
                {{-- <th>Email</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>
                    @foreach($user->roles as $role)
                        {{ $role->name }}
                    @endforeach
                </td>
                <td class="{{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                </td>
                <td>{{ $user->ente ? $user->ente->nombre : 'Sin asignar' }}</td>
                {{-- <td>{{ $user->email }}</td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestión de Usuarios - PDF generado automáticamente
    </div>
</body>
</html>