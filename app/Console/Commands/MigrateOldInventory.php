<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-old-inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar datos del inventario viejo al nuevo esquema con locations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de inventario viejo...');

        $oldInventories = DB::table('inventario')->get(); 

        foreach ($oldInventories as $old) {
            try {
                // Verificar si existe en catálogo
                $exists = DB::table('catalogo')
                    ->where('id', $old->FKCatalog)
                    ->exists();

                if (!$exists) {
                    $this->warn("Saltando inventario ID {$old->ID}: catálogo {$old->FKCatalog} no existe");
                    continue; // salta este registro
                }

                // Normalizar fecha ( para evitar error de que la fecha no es compatible cuando no tiene ninguna asignada)
                $expirationDate = ($old->caducidad === '0000-00-00' || empty($old->caducidad))
                    ? null
                    : $old->caducidad;

                DB::table('inventories')->insert([
                    'catalogo_id'     => $old->FKCatalog,
                    'location_id'     => $this->mapLocation($old->ubicacion),
                    'quantity'        => $old->cantidad ?? 0,
                    'expiration_date' => $expirationDate,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            } catch (\Exception $e) {
                $this->error("Error migrando ID {$old->ID}: " . $e->getMessage());
            }
        }

        $this->info('Migración completada con éxito.');
    }

    private function mapLocation($ubicacion)
    {
        return match (strtolower(trim($ubicacion))) {
            'almacen' => 1,
            'auto' => 2,
            default => null, // para que sepas si algo no coincide
        };
    }
}