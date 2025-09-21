<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Inventory;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(){
        $catalog = Catalogo::all()
        ->where('service','0');

        return view('inventory.catalog',compact('catalog'));
    }

    public function store(Request $request){
        $item = new Catalogo();
        
        $item->nombre = $request->name;
        $item->Precio = $request->precio;
        $item->imagenURL = $request->imgUrl;

        if ($request->idealAlmacen == null) {
            $request->idealAlmacen = 0;
        }
        if ($request->idealAuto == null) {
            $request->idealAuto = 0;
        }

        $item->IdealAlmacen = $request->idealAlmacen;
        $item->IdealAuto = $request->idealAuto; 
        $item->service = 0;

        $item->save();

        return redirect()->route('catalog');
    }

    public function destroy($id){
        $item = Catalogo::find($id);
        $item->delete();

        Inventory::where('catalogo_id', $id)->delete();

        return redirect()->route('catalog');
    }

    public function update(Request $request,$id){
        $item = Catalogo::find($id);

        $item->nombre = $request->name;
        $item->Precio = $request->precio;
        $item->imagenURL = $request->imgUrl;
        $item->IdealAlmacen = $request->idealAlmacen;
        $item->IdealAuto = $request->idealAuto;
        $item->service = 0;

        $item->save();

        return redirect()->route('catalog');
    }
}
