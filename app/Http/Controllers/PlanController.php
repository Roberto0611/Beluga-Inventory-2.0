<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Inventory;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function getStock($productId){
        return Inventory::where('catalogo_id', $productId)->sum('quantity');
    }

    public function index(){
        // Obtener datos del catálogo
        $catalog = Catalogo::where('service', 0)->get();
        
        // Calcular stock y porcentaje faltante
        $catalog->each(function ($product) {
            $product->current_stock = $this->getStock($product->ID);
            $product->missing_stock = $product->IdealPetShed - $product->current_stock;
            
            // Calcular porcentaje faltante (protección contra división por cero)
            $product->missing_percentage = $product->IdealPetShed > 0 
                ? ($product->missing_stock / $product->IdealPetShed) 
                : 0;

            if ($product->missing_stock < 0) {
                $product->missing_stock = 0;
            }
        });

        // Ordenar por porcentaje faltante (descendente)
        $catalog = $catalog->sortByDesc('missing_percentage');

        return view('inventory.plan', compact('catalog'));
    }
}