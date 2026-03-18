<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acuse de Documentos Validados</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .mensaje-vacio { text-align: center; color: #999; margin-top: 50px; }
    </style>
</head>
<body>
    <h2>Acuse de Documentos Validados</h2>
    @if(count($data) > 0 && !empty($data[0]['documento_validado']))
        <table>
            <thead>
                <tr>
                    <th>Documento validado</th>
                    <th>Tipo de archivo</th>
                    <th>Fecha</th>
                    <th>Hora</th>
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