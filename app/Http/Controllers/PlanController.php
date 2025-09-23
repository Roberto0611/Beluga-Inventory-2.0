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

    public function index(Request $request, $ubicacion){
        // Obtener datos del catálogo
        $catalog = Catalogo::where('service', 0)->get();
        
        // Calcular stock y porcentaje faltante usando foreach (necesitamos $ubicacion en el scope)
        foreach ($catalog as $product) {
            $product->current_stock = $this->getStock($product->ID);
            // Faltantes (no negativos)
            $product->missing_stock_almacen = max(0, $product->IdealAlmacen - $product->current_stock);
            $product->missing_stock_auto    = max(0, $product->IdealAuto - $product->current_stock);
        
            if ($ubicacion === 'almacen') {

                $product->ideal_stock = $product->IdealAlmacen;
                $product->missing_stock = $product->missing_stock_almacen;

                $product->missing_percentage = $product->ideal_stock > 0
                    ? ($product->missing_stock / $product->ideal_stock)
                    : 0;
            } elseif ($ubicacion === 'auto') {
                $product->ideal_stock = $product->IdealAuto;
                $product->missing_stock = $product->missing_stock_auto;

                $product->missing_percentage = $product->ideal_stock > 0
                    ? ($product->missing_stock / $product->ideal_stock)
                    : 0;
            } else {
                // Ubicación desconocida: usar almacen como fallback
                $product->missing_percentage = $product->IdealAlmacen > 0
                    ? ($product->missing_stock_almacen / $product->IdealAlmacen)
                    : 0;
            }
        }

    // Ordenar por porcentaje faltante (descendente) y reindexar
    $catalog = $catalog->sortByDesc('missing_percentage')->values();

    return view('inventory.plan', compact('catalog', 'ubicacion'));
    }
}