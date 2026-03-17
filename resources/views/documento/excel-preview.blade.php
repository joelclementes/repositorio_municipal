{{-- resources/views/excel-preview.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista previa - {{ $archivo->nombre }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f5f5f5;
        }
        
        .header {
            background-color: #1D6F42;
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 500;
        }
        
        .header a {
            background-color: white;
            color: #1D6F42;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .header a:hover {
            background-color: #e0e0e0;
        }
        
        .excel-container {
            background-color: white;
            border-radius: 0 0 8px 8px;
            padding: 20px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Estilos para la tabla Excel */
        .excel-container table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
        }
        
        .excel-container td, 
        .excel-container th {
            border: 1px solid #e0e0e0;
            padding: 8px 12px;
            min-width: 80px;
        }
        
        .excel-container th {
            background-color: #f2f2f2;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .excel-container tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .excel-container tr:hover {
            background-color: #f0f7ff;
        }
        
        .info-bar {
            background-color: #e8f5e9;
            border-left: 4px solid #1D6F42;
            padding: 12px 20px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-bar svg {
            color: #1D6F42;
            width: 24px;
            height: 24px;
        }
        
        .info-bar p {
            margin: 0;
            color: #2e7d32;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2>📊 {{ $archivo->ente->nombre }} - {{ $archivo->documentoRecibido->documento->nombre }}</h2>
            <p style="margin: 5px 0 0; opacity: 0.9;">
                Período: {{ $archivo->documentoRecibido->periodo->descripcion }}
            </p>
        </div>
        {{-- <a href="{{ url()->previous() }}">← Volver</a> --}}
    </div>
    
    <div class="info-bar">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p>
            Vista previa del archivo Excel. Para una mejor experiencia, 
            <a href="{{ $archivo->url }}" target="_blank" style="color: #1D6F42; font-weight: bold;">
                descarga el archivo original
            </a>.
        </p>
    </div>
    
    <div class="excel-container">
        {!! $html !!}
    </div>
</body>
</html>