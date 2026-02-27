<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Documento;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentos = [
            [
                'documento_data' => [
                    'clave' => 'EF1',
                    'nombre' => 'Estado de Actividades',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF2',
                    'nombre' => 'Estado de Situación Financiera',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF3',
                    'nombre' => 'Estado de Variación de la Hacienda Municipal',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF4',
                    'nombre' => 'Estado de cambios en la Situación Financiera',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF5',
                    'nombre' => 'Estado de Flujos de Efectivo',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF6',
                    'nombre' => 'Informes sobre Pasivos Contingentes',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF7',
                    'nombre' => 'Notas a los Estados Financieros',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF8',
                    'nombre' => 'Conciliación entre Ingresos Presupuestales y Contables',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF9',
                    'nombre' => 'Conciliación entre Egresos Presupuestales y Contables',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF10',
                    'nombre' => 'Estado Analítico del Activo',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF11',
                    'nombre' => 'Estado Analítico de la Deuda y Otros Pasivos',
                    'subcategoria_id' => 1,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF12',
                    'nombre' => 'Estado Analítico de los Ingresos',
                    'subcategoria_id' => 2,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF13',
                    'nombre' => 'Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Administrativa',
                    'subcategoria_id' => 2,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF14',
                    'nombre' => 'Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Económica',
                    'subcategoria_id' => 2,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF15',
                    'nombre' => 'Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación por Objetivo del Gasto',
                    'subcategoria_id' => 2,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF16',
                    'nombre' => 'Estado Analítico del Ejercicio del Presupuesto de Egresos Clasificación Funcional',
                    'subcategoria_id' => 2,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF17',
                    'nombre' => 'Oficio de Remisión de los Estados Financieros',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF18',
                    'nombre' => 'Acta de Sesión de Cabildo',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF19',
                    'nombre' => 'Balanza de Comprobación (al último nivel de desagregación)',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF20',
                    'nombre' => 'Informe de Altas y Bajas de Personal',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF21',
                    'nombre' => 'Informe de Altas de Bienes Muebles',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF22',
                    'nombre' => 'Informe de Bajas de Bienes Muebles',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF23',
                    'nombre' => 'Informe de Altas de Bienes Inmuebles',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF24',
                    'nombre' => 'Informe de Bajas de Bienes Inmuebles',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF25',
                    'nombre' => 'Estado de Deuda Pública',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF26',
                    'nombre' => 'Informe del Órgano Interno de Control',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF27',
                    'nombre' => 'Relación de Donaciones Recibidas',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF28',
                    'nombre' => 'Notificación de Depósitos de Participaciones y Aportaciones',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF29',
                    'nombre' => 'Listado de Adjudicaciones y Licitaciones (Incluye las Obras Públicas)',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF30',
                    'nombre' => 'Formato con Programas con Recursos Concurrentes por Orden de Gobierno',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF31',
                    'nombre' => 'Formato de Montos Pagados por Ayudas y Subsidios',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF32',
                    'nombre' => 'Formato de Aplicación de Recursos del FORTAMUN-DF',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF33',
                    'nombre' => 'Formato de aplicación de Recursos del FISM-DF',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF34',
                    'nombre' => 'Conciliaciones Bancarias',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF35',
                    'nombre' => 'Estados de Cuentas Bancarias',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF36',
                    'nombre' => 'Auxiliares Bancarios',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF37',
                    'nombre' => 'Relación de Cheques en Tránsito',
                    'subcategoria_id' => 3,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'EF38',
                    'nombre' => 'Depósitos Pendientes de Registrar por el Banco',
                    'subcategoria_id' => 3,
                ],
            ],

            [
                'documento_data' => [
                    'clave' => 'OP1',
                    'nombre' => 'Oficio de Remisión',
                    'subcategoria_id' => 4,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP2',
                    'nombre' => 'Acta de Sesión de Cabildo (OP)',
                    'subcategoria_id' => 4,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP3',
                    'nombre' => 'Relación de Obras y Acciones',
                    'subcategoria_id' => 4,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP4',
                    'nombre' => 'Reporte Fotográfico de Avance Mensual de obra (Formato 9)',
                    'subcategoria_id' => 4,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP5',
                    'nombre' => 'Estado Mensual de Obras por Contrato (Formato 11)',
                    'subcategoria_id' => 4,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP6',
                    'nombre' => 'Estado mensual de Obras por Administración Directa (Formato 12)',
                    'subcategoria_id' => 4,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP7',
                    'nombre' => 'Primer Informe Trimestral de Obras y Acciones',
                    'subcategoria_id' => 5,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP8',
                    'nombre' => 'Segundo Informe Trimestral de Obras y Acciones',
                    'subcategoria_id' => 5,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP9',
                    'nombre' => 'Tercer Informe Trimestral de Obras y Acciones',
                    'subcategoria_id' => 5,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP10',
                    'nombre' => 'Cuarto Informe Trimestral de Obras y Acciones',
                    'subcategoria_id' => 5,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP11',
                    'nombre' => 'Propuesta General de Inversión (Formato 01)',
                    'subcategoria_id' => 6,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP12',
                    'nombre' => 'Modificación a la Propuesta de Inversión (Formato 02)',
                    'subcategoria_id' => 6,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP13',
                    'nombre' => 'Cierre de Obras y Acciones (Formato 04)',
                    'subcategoria_id' => 7,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP14',
                    'nombre' => 'Acta de Comité de Contraloría Social, para las Modalidades de Contrato y/o Administración Directa',
                    'subcategoria_id' => 7,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OP15',
                    'nombre' => 'Acta de Entrega-Recepción del Ayuntamiento a la Comunidad al finalizar la Obra para las Modalidades de Contrato y/o Administración Directa',
                    'subcategoria_id' => 7,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OBM1',
                    'nombre' => 'Corte de Caja',
                    'subcategoria_id' => 8,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OBM2',
                    'nombre' => 'Reporte de Recaudación de Impuesto Predial',
                    'subcategoria_id' => 8,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OBM3',
                    'nombre' => 'Reporte de Recaudación de Traslado de Dominio',
                    'subcategoria_id' => 8,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OBM4',
                    'nombre' => 'Reporte de Recaudación de Derchos de Agua',
                    'subcategoria_id' => 8,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV1',
                    'nombre' => 'Cuenta Pública',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV2',
                    'nombre' => 'Proyecto de Ley de Ingresos',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV3',
                    'nombre' => 'Presupuesto de Egresos',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV4',
                    'nombre' => 'Plantilla de Personal',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV5',
                    'nombre' => 'Inventario y Avalúo de Bienes Muebles e Inmuebles',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV6',
                    'nombre' => 'Acta de Cabildo donde se Prorroga el Descuento del Impuesto Predial',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV7',
                    'nombre' => 'Padrón de Predial',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV8',
                    'nombre' => 'Padrón de Agua',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV9',
                    'nombre' => 'Padrón de Limpia Pública',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV10',
                    'nombre' => 'Padrón de Comercio',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV11',
                    'nombre' => 'Padrón de Mercados',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV12',
                    'nombre' => 'Padrón de Bares y Cantinas',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV13',
                    'nombre' => 'Fianzas de Responsabilidades',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV14',
                    'nombre' => 'Programa Anual de Adquisiciones',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV15',
                    'nombre' => 'Programas Presupuestarios (Antes POA)',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV16',
                    'nombre' => 'Informe Trimestral de Deuda Pública',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV17',
                    'nombre' => 'Propuesta General de Inversión',
                    'subcategoria_id' => 9,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OLV18',
                    'nombre' => 'Acta Bimestral de Sesión de Cabildo Abierto',
                    'subcategoria_id' => 9,
                ],
            ],
            
            [
                'documento_data' => [
                    'clave' => 'ODCAM1',
                    'nombre' => 'Acta Circunstanciada de Entrega-Recepción',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM2',
                    'nombre' => 'Acta de Integración de las Comisiones Especiales de Entrega-Recepción',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM3',
                    'nombre' => 'Dictamen',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM4',
                    'nombre' => 'Notificaciones',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM5',
                    'nombre' => 'Acta de Acuerdo en Vía de Opinión',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM6',
                    'nombre' => 'Acta de Instalación del Ayuntamiento',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM7',
                    'nombre' => 'Designación de Comisiones Municipales',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM8',
                    'nombre' => 'Nombramiento del Secretario del Ayuntamiento',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM9',
                    'nombre' => 'Nombramiento del Tesorero',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM10',
                    'nombre' => 'Nombramiento del Titular del Órgano de Control Interno',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM11',
                    'nombre' => 'Nombramiento del Comandante de Polcía',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM12',
                    'nombre' => 'Nombramiento del Director de Obras Públicas',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM13',
                    'nombre' => 'Nombramiento del Director de Fomento Agropecuario',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODCAM14',
                    'nombre' => 'Plan Municipal de Desarrollo',
                    'subcategoria_id' => 10,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA1',
                    'nombre' => 'Modificaciones Presupuestales',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA2',
                    'nombre' => 'Modificaciones a la Plantilla del Personal',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA3',
                    'nombre' => 'Nombramiento del Actual Secretario del Ayuntamiento',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA4',
                    'nombre' => 'Nombramiento del Actual Tesorero',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA5',
                    'nombre' => 'Nombramiento del Actual Titula del Órgano Interno de Control',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA6',
                    'nombre' => 'Nombramiento del Actual Comandante de Policía',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA7',
                    'nombre' => 'Nombramiento del Actual Director de Obras Públicas',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA8',
                    'nombre' => 'Nombramiento del Actual Director de Fomento Agropecuario',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA9',
                    'nombre' => 'Actualizaciones al Plan Municipal de Desarrollo',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA10',
                    'nombre' => 'Modificación de Comisiones Municipales',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA11',
                    'nombre' => 'Nombramiento del Funcionario con Perfil Financiero',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA12',
                    'nombre' => 'Nombramiento del Funcionario con Perfil de Obra',
                    'subcategoria_id' => 11,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'ODAA13',
                    'nombre' => 'Nombramiento del Funcionario con Perfil de Contralor',
                    'subcategoria_id' => 11,
                ],
            ],

            [
                'documento_data' => [
                    'clave' => 'OTLDF1',
                    'nombre' => 'Formato 1 Estado de Situación financiera detallado',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OTLDF2',
                    'nombre' => 'Formato 2 Informe Analítico de la Deuda Pública y Otros Pasivos',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OTLDF3',
                    'nombre' => 'Formato 3 Informe Analítico de Obligaciones Diferentes de Financiamiento',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OTLDF4',
                    'nombre' => 'Formato 4 Balance Presupuestario',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OTLDF5',
                    'nombre' => 'Formato 5 Estado Analítico de Ingresos Detallado',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OTLDF6',
                    'nombre' => 'Formato 6a Estado Analítico del Ejercicio del Presupuesto de Egresos Detallado - Clasificación por Objeto del Gasto',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OTLDF7',
                    'nombre' => 'Formato 6b Estado Analítico del Ejercicio del Presupuesto de Egresos Detallado - Clasificación Admimnistrativa',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OTLDF8',
                    'nombre' => 'Formato 6c Estado Analítico del Ejercicio del Presupuesto de Egresos Detallado - Clasificación Funcional',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'OTLDF9',
                    'nombre' => 'Formato 6d Estado Analítico del Ejercicio del Presupuesto de Egresos Detallado - Clasificación Servicios Personales Por Categoría',
                    'subcategoria_id' => 12,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIPA01',
                    'nombre' => 'Plan anual de Auditorías del Contralor Interno',
                    'subcategoria_id' => 13,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIPA02',
                    'nombre' => 'Primer informe trimestral sobre las Auditorías del Contralor Interno (Con corte al 31 de marzo)',
                    'subcategoria_id' => 13,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIPA03',
                    'nombre' => 'Segundo informe trimestral sobre las Auditorías del Contralor Interno (Con corte al 30 de junio)',
                    'subcategoria_id' => 13,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIPA04',
                    'nombre' => 'Tercer informe trimestral sobre las Auditorías del Contralor Interno (Con corte al 30 de septiembre)',
                    'subcategoria_id' => 13,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIPA05',
                    'nombre' => 'Cuarto informe trimestral sobre las Auditorías del Contralor Interno (Con corte al 31 de diciembre)',
                    'subcategoria_id' => 13,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIIS01',
                    'nombre' => 'Reporte de Estatus Inicial del Ejercicio sobre las Observaciones y/o Recomendaciones',
                    'subcategoria_id' => 14,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIIS02',
                    'nombre' => 'Primer informe trimestral sobre las Observaciones y/o Recomendaciones (Con corte al 31 de marzo)',
                    'subcategoria_id' => 14,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIIS03',
                    'nombre' => 'Segundo informe trimestral sobre las Observaciones y/o Recomendaciones (Con corte al 30 de junio)',
                    'subcategoria_id' => 14,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIIS04',
                    'nombre' => 'Tercer informe trimestral sobre las Observaciones y/o Recomendaciones (Con corte al 30 de septiembre)',
                    'subcategoria_id' => 14,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIIS05',
                    'nombre' => 'Cuarto informe trimestral sobre las Observaciones y/o Recomendaciones (Con corte al 31 de diciembre)',
                    'subcategoria_id' => 14,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIER01',
                    'nombre' => 'Dictamen de Entrega Recepción',
                    'subcategoria_id' => 15,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIER02',
                    'nombre' => 'Primer Informe Trimestral sobre el Seguimiento de las Observaciones de Entrega Recepción (Corte al 30 de Junio)',
                    'subcategoria_id' => 15,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIER03',
                    'nombre' => 'Segundo Informe Trimestral sobre el Seguimiento de las Observaciones de Entrega Recepción (Corte al 30 de Septiembre)',
                    'subcategoria_id' => 15,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIER04',
                    'nombre' => 'Tercer Informe Trimestral sobre el Seguimiento de las Observaciones de Entrega Recepción (Corte al 30 de Diciembre)',
                    'subcategoria_id' => 15,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIPMD01',
                    'nombre' => 'CIPMD01 Plan Municipal de Desarrollo',
                    'subcategoria_id' => 16,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIPMD02',
                    'nombre' => 'CIPMD02 Seguimiento al Plan Municipal de Desarrollo',
                    'subcategoria_id' => 16,
                ],
            ],
            [
                'documento_data' => [
                    'clave' => 'CIPMD03',
                    'nombre' => 'CIPMD03 Actualizaciones al Plan Municipal de Desarrollo',
                    'subcategoria_id' => 16,
                ],
            ],
        ];

        foreach ($documentos as $documento) {
            Documento::create($documento['documento_data']);
        }
    }
}
