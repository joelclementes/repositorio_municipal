<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\CategoriasDocumento;
use App\Models\SubcategoriasDocumento;
use App\Models\Periodo;

class UploadTacajetePdfsTest extends DuskTestCase
{
    /**
     * A Dusk test to upload PDFs for tacajete automatically.
     */
    public function testUploadPdfsTacajete(): void
    {
        $this->browse(function (Browser $browser) {
            // 1. Iniciar sesión
            $browser->visit('/login')
                    ->type('email', 'tacajete')
                    ->type('password', '123456789')
                    ->click('button[type="submit"]')
                    ->waitForLocation('/dashboard', 10);

            // 2. Ir a la página de registro
            $browser->visit('/documentos/registrar')
                    ->pause(2000); // Esperar a que cargue Livewire

            // Obtener todos los periodos activos
            $periodos = Periodo::where('is_active', 1)->orderBy('id', 'desc')->get();
            if ($periodos->isEmpty()) {
                $this->fail('No hay periodos activos disponibles');
            }

            foreach ($periodos as $periodo) {
                // Seleccionar periodo
                $browser->select('@periodosSeleccionados', (string)$periodo->id)
                        ->pause(1500); // Esperar request de Livewire

                // Obtener las categorías permitidas para el rol "Tesorero"
                $categorias = CategoriasDocumento::whereRaw("FIND_IN_SET('Tesorero', roles_permitidos)")->get();

                foreach ($categorias as $cat) {
                    $browser->select('@categoriaSeleccionada', (string)$cat->id)
                            ->pause(1500);

                    $subcategorias = SubcategoriasDocumento::where('categoria_id', $cat->id)->get();
                    
                    foreach ($subcategorias as $sub) {
                        $browser->select('@subcategoriaSeleccionada', (string)$sub->id)
                                ->pause(1500);

                        // Buscar botones de "Subir PDF" o "Subir Excel" habilitados
                        $selector = 'button:not([disabled]).bg-vino-900, button:not([disabled]).bg-green-700';

                        while (true) {
                            $botones = $browser->driver->findElements(\Facebook\WebDriver\WebDriverBy::cssSelector($selector));
                            
                            if (empty($botones)) {
                                break; // No hay más botones habilitados en esta subcategoría
                            }

                            // Siempre hacemos clic en el primer botón disponible
                            $botones[0]->click();
                            
                            // Esperar a que se abra el modal
                            $browser->waitFor('.fixed.inset-0.z-50', 5);
                            $browser->pause(1000);
                            
                            // Adjuntar archivo dummy
                            $path = '/var/www/html/storage/app/public/dummy.pdf';
                            $browser->attach('@archivoInput', $path)
                                    ->pause(1000);
                            
                            // Clic en Subir archivo (el botón es color esmeralda)
                            $botonSubir = $browser->driver->findElement(\Facebook\WebDriver\WebDriverBy::cssSelector('button.bg-emerald-900'));
                            $botonSubir->click();
                            
                            // Esperar a que el modal se cierre
                            $browser->waitUntilMissing('.fixed.inset-0.z-50', 10);
                            
                            // Pausa breve antes de subir el siguiente
                            $browser->pause(1500);
                        }
                    }
                }
            }
        });
    }
}
