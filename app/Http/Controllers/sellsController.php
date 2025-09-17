<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class sellsController extends Controller
{
    public function index(){

        $lastSells = Sale::latest()->take(10)->get();

        return view('sells.index',compact('lastSells'));
    }

    public function newsell(){

        $catalog = Catalogo::all();

        return view('sells.newsell',compact('catalog'));
    }

    public function destroy($sellId){
        $sell = Sale::find($sellId);
        $sell->deleted = 1;
        $sell->save();

        $payments = Payment::where('sale_id',$sellId)->get();
        
        foreach ($payments as $payment) {
            $payment->deleted = 1;
            $payment->save();
        }
        
        return redirect()->route('sells')->with('success', 'Venta #'.$sellId.' eliminada correctamente');
    }

    public function show(Request $request, $sellId)
    {
        try {
            $sell = Sale::with(['details.catalogo' => function($query) { 
                    $query->withTrashed(); }, 'user_info', 'payments'])
                    ->findOrFail($sellId);
                    
            return view('sells.show', compact('sell'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Redirigir con mensaje de error
            return redirect()->route('sells')
                ->with('error', 'La venta #'.$sellId.' no fue encontrada');
        }
    }

    public function store(Request $request){
        // insert sale
        $sale = new Sale;

        $sale->user_id = Auth::user()->id;
        $sale->subtotal = $request->subtotal;
        $sale->discount = $request->descuento;
        $sale->total = $request->total;
        $sale->notes = $request->notas;

        $sale->save(); 

        // process $cartItems
        $cartItems = json_decode($request->cartItems, true);

        // insert sale details
        foreach ($cartItems as $item) {
            $saleDetail = new SaleDetail;

            $saleDetail->sale_id = $sale->id;
            $saleDetail->catalogo_id = $item['id'];
            $saleDetail->price = $item['unitPrice'];
            $saleDetail->quantity = $item['quantity'];

            $saleDetail->save();

            // reduce from inventory
            $deleteQuantity = $item['quantity'];
            $stocks = Inventory::where('catalogo_id',$item['id'])
            ->orderBy('expiration_date', 'asc')
            ->get();

            // iterar hasta reducir la cantidad necesaria
            foreach ($stocks as $stock) {
                $currentQuantity = $stock->quantity;

                // if the quantity to delete it's higher or equal to the deleteQuantity
                // we delete the row and reduce the deleteQuantity 

                if ($currentQuantity <= $deleteQuantity) {
                    $stock->delete();
                    $deleteQuantity -= $currentQuantity;
                }else{ // else reduce the quantity on the db
                    $stock->quantity = $currentQuantity - $deleteQuantity;
                    $stock->save();
                    $deleteQuantity = 0;
                    break;
                }
                
                if ($deleteQuantity == 0) {
                    break;
                    }
            }
        }
        // insert payment 1
        $payment = new Payment;

        $payment->sale_id = $sale->id;
        $payment->amount = $request->pagoMonto1;
        $payment->method = $request->pagoMetodo1;

        $payment->save();
        
        // in case of existence of payment 2 insert it
        if ($request->pagoMonto2 > 0 and $request->pagoMetodo2 != "Ninguno") {
            $payment = new Payment;

            $payment->sale_id = $sale->id;
            $payment->amount = $request->pagoMonto2;
            $payment->method = $request->pagoMetodo2;

            $payment->save();
        }

        return redirect()->route('sells',['ticketId' => $sale->id]);
    }
}
