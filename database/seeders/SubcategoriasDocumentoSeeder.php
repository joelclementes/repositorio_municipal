<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubcategoriasDocumento;

class SubcategoriasDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategorias = [
            [
                'subcategoria_Data' => [
                    'clave' => 'IF',
                    'nombre' => 'Información Financiera',
                    'categoria_id' => 1,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'IP',
                    'nombre' => 'Información Presupuestal',
                    'categoria_id' => 1,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'IA',
                    'nombre' => 'Información Adicional',
                    'categoria_id' => 1,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'OP',
                    'nombre' => 'Informe mensual de Obra Pública',
                    'categoria_id' => 2,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'ITOP',
                    'nombre' => 'Información trimestral de Obra Pública',
                    'categoria_id' => 2,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'IPOP',
                    'nombre' => 'Información Presupuestal de Obra Pública',
                    'categoria_id' => 2,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'COPAC',
                    'nombre' => 'Cierre de Obra Pública y Acciones',
                    'categoria_id' => 2,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'OMENS',
                    'nombre' => 'Obligaciones Mensuales',
                    'categoria_id' => 3,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'OLV',
                    'nombre' => 'Obligaciones a presentarse en los períodos señalados por la legislación vigante',
                    'categoria_id' => 3,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'ODCAM',
                    'nombre' => 'Obligaciones derivadas de Cambio de Administración Municipal',
                    'categoria_id' => 3,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'ODAA',
                    'nombre' => 'Obligaciones derivadas de Actualizaciones Administrativas',
                    'categoria_id' => 3,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'OTLDF',
                    'nombre' => 'Obligaciones trimestrales derivadas de la ley de Disciplina Financiera',
                    'categoria_id' => 3,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'CIPA',
                    'nombre' => 'Plan anual de auditoría',
                    'categoria_id' => 4,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'CIIS',
                    'nombre' => 'Informes de seguimiento de Observaciones y/o recomendaciones de años anteriores de las cuentas públicas auditadas por el ORFIS',
                    'categoria_id' => 4,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'CIER',
                    'nombre' => 'Informes de Seguimiento de Observaciones del Dictamen de Entrega y Recepción',
                    'categoria_id' => 4,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'CIPMD',
                    'nombre' => 'Plan Municipal de Desarrollo',
                    'categoria_id' => 4,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'IFT',
                    'nombre' => 'Información Financiera Trimestral',
                    'categoria_id' => 5,
                ],
            ],
            [
                'subcategoria_Data' => [
                    'clave' => 'OOB',
                    'nombre' => 'Otras Obligaciones',
                    'categoria_id' => 5,
                ],
            ],
        ];

        foreach ($subcategorias as $categoria) {
            SubcategoriasDocumento::create($categoria['subcategoria_Data']);
        }
    }
}
