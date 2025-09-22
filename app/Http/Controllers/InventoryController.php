<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Inventory;
use App\Models\Location;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function getStock($productId){
        return Inventory::where('catalogo_id', $productId)->sum('quantity');
    }

    public function stock(){
        $catalog = Catalogo::all()
        ->where('service','0');

        $locations = Location::all();

        $stock = Inventory::with('catalogo','location')
        ->selectRaw('*, SUM(quantity) OVER (PARTITION BY catalogo_id) as total_quantity')
        ->orderBy('expiration_date')
        ->get()
        ->groupBy('catalogo_id');

        return view('inventory.stock',compact('catalog','stock','locations'));
    }

    public function store(Request $request){

        $validated = $request->validate([
        'product_id' => 'required|exists:catalogo,id',
        'cantidad' => 'required|integer|min:1',
        'location_id' => 'required|exists:locations,id'
        ]);
        
        $stock = new Inventory();
        $stock->catalogo_id = $request->product_id;
        $stock->quantity = $request->cantidad;
        $stock->expiration_date = $request->caducidad;
        $stock->location_id = $request->location_id;

        $stock->save();

        return redirect()->route('inventory'); # redirigir 
    }

    public function reduce(Request $request)
    {
        $stock = Inventory::find($request->inventory_id);

        if (!$stock) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }

        $new_quantity = $request->current_quantity - $request->reduction_quantity;

        if ($new_quantity > 0) {
            $stock->quantity = $new_quantity;
            $stock->save();
            return response()->json(['success' => true, 'new_quantity' => $new_quantity, 'deleted' => false]);
        } else {
            $stock->delete();
            return response()->json(['success' => true, 'deleted' => true]);
        }
    }

    public function getInventario($productoID) {
    try {
        $totalQuantity = Inventory::where('catalogo_id', $productoID)->sum('quantity');
        
        return response()->json([
            'cantidad' => $totalQuantity, // Suma de todas las cantidades
            'error' => false
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => 'Error al consultar inventario: ' . $e->getMessage()
        ], 500);
    }
}
}
