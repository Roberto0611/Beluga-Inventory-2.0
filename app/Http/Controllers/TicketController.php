<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleDetail;
use FPDF;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function generateTicket(Request $request, $sellId){

        $direccion = 'Politécnico Nacional 110, El Conchalito, 23090, Baja California Sur, México ';
        $RFC = 'OITR890928170';
        $nombre = 'RODRIGO ONTIVEROS TLACHI';
        $facturacion = 'administracion@belugavet.com';  

        // get the sell info, products, catalogo indo and user info
        $sell = Sale::with(['details.catalogo' => function($query) { 
                    $query->withTrashed(); },'user_info','payments'])->findOrFail($sellId);

        // create pdf
        $pdf = new FPDF('P','mm',array(80,200));
        $pdf->AddPage();
        $pdf->SetMargins(5,5,5);
        $pdf->SetFont('Arial','B',9);

        $pdf->SetFont('Arial','',6);
        $pdf -> ln(9);
        $pdf -> cell(70,2,$nombre,0,1,"C");
        $pdf -> ln();
        $pdf -> cell(70,2,"RFC: " . $RFC,0,1,"C");
        $pdf -> ln();
        $pdf -> cell(70,2,mb_convert_encoding("Personas Físicas con Actividades Empresariales y Profesionales",'ISO-8859-1','UTF-8'),0,1,"C");
        $pdf->Ln();
        $pdf->MultiCell(70,5,mb_convert_encoding($direccion,'ISO-8859-1','UTF-8'),0,'C');

        $pdf->Ln(1);

        $pdf->SetFont('Arial','B',9);
        $pdf->cell(20,5,"ID de venta: ",0,0,"L");
        $pdf->SetFont('Arial','',9);
        $pdf->cell(15,5,$sell->id,0,1,"L");

        //Division
        $pdf->cell(70,2,'----------------------------------------------------------------',0,1,"L");

        // Header for products
        $pdf->cell(10,4,'cant',0,0,'L');
        $pdf->cell(30,4,'producto',0,0,'L');
        $pdf->cell(15,4,'total',0,0,'L');

        $pdf->Ln(3);
        $pdf->cell(70,2,'----------------------------------------------------------------',0,1,"L");

        $totalProductos = 0;
        $pdf->SetFont('Arial','',7);

        $numeroDeProductos = 0;

        foreach ($sell->details as $product) {
            $pdf->cell(10,4,$product->quantity,0,0,'L');

            $yInicio = $pdf->GetY();
            $pdf->MultiCell(30,4,mb_convert_encoding($product->catalogo->nombre,'ISO-8859-1','UTF-8'),0,'L');
            $yFin = $pdf->GetY();

            $pdf->SetXY(45,$yInicio);

            $pdf->cell(15,4,'$' . $product->price * $product->quantity,0,0,'L');
            $pdf->Ln(7);

            $pdf->SetY($yFin);

            $numeroDeProductos += $product->quantity;
        }

        $pdf -> ln();
        $pdf->cell(70,4,"numero de productos:" . " " . $numeroDeProductos,0,1,"L");

        $pdf->cell(70,5,"Subtotal: " . "$" . $sell['subtotal'],0,1,"R");
        $pdf->cell(70,5,"Descuento: " . $sell['discount'] . "%",0,1,"R");
        $pdf->SetFont('Arial','B',10);
        $pdf->cell(70,5,"Total: " . "$" . $sell['total'],0,1,"R");

        $pdf -> ln(2);

        $pdf->SetFont('Arial','',8);

        $pdf->cell(70,2,'********************************************************',0,1,"C");
        $pdf -> cell(70,2,'Fecha: ' . $sell['created_at'],0,1,"C");
        $pdf -> ln();
        $pdf -> cell(70,2,'Cajero: ' . $sell->user_info->name,0,1,"C");
        $pdf -> ln();

        $pdf->SetFont('Arial','B',8);
        $pdf -> cell(70,2,'metodos de pago: ',0,1,"C");
        $pdf->SetFont('Arial','',8);

        $pdf -> ln();

        foreach ($sell->payments as $payment) {
            $pdf -> cell(70,2,'' . $payment->method . " --- $" . $payment->amount,0,1,"C");
            $pdf -> ln();
        }

        $pdf -> MultiCell(70,3,mb_convert_encoding("Para solicitar una factura, favor de enviar su información durante el mes fiscal de su compra a ",'ISO-8859-1','UTF-8') . $facturacion,0,"C");
        $pdf ->Ln();
        $pdf -> cell(70,2,'IVA Incluido',0,1,"C");

        $pdf -> ln(4);    
        $pdf->SetFont('Arial','',8);
        $pdf -> cell(70,2,'GRACIAS POR SU PREFERENCIA',0,1,"C");

        // Add image to pdf
        $pdf -> Image(asset('images/logoTicket.jpg'),21,2,40);



    return response($pdf->Output('S'), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="ticket.pdf"');
    }

    public function generateCorte(Request $request){
        try {
            // 1. Obtener totales por método de pago
            $methods = ['Efectivo', 'Transferencia', 'Tarjeta'];
            
            $salesByMethod = Payment::whereToday('created_at')
                ->where('deleted', 0)
                ->selectRaw('method, SUM(amount) as total')
                ->groupBy('method')
                ->get()
                ->keyBy('method');

            $totals = collect($methods)->mapWithKeys(function ($method) use ($salesByMethod) {
                return [$method => $salesByMethod->has($method) ? number_format($salesByMethod[$method]->total, 2) : '0.00'];
            });

            // 2. Calcular total general
            $totalGeneral = array_sum($salesByMethod->pluck('total')->toArray());
            
            // 3. Obtener número de ventas
            $numberOfSells = Sale::whereToday('created_at')
                ->where('deleted', 0)
                ->count();

            // 4. Generar PDF
            $pdf = new FPDF('P', 'mm', array(80, 200));
            $pdf->AddPage();
            $pdf->SetMargins(5, 5, 5);
            
            // Encabezado
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Ln(9);
            $pdf->Cell(70, 2, "Corte de caja", 0, 1, "C");
            $pdf->Ln();
            
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(70, 2, "Fecha: " . date("d-m-Y"), 0, 1, "C");
            $pdf->Ln(2);
            $pdf->Cell(70, 2, "Hora: " . date("H:i:s"), 0, 1, "C");
            $pdf->Ln(3);
            
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(70, 2, '----------------------------------------------------------------', 0, 1, "L");
            $pdf->Ln(2);
            
            // Resumen
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(70, 2, 'Resumen del dia:', 0, 1, "C");
            $pdf->Ln(3);
            
            $pdf->SetFont('Arial', '', 9);
            $this->addPaymentRow($pdf, 'Transferencia:', $totals['Transferencia']);
            $this->addPaymentRow($pdf, 'Tarjeta:', $totals['Tarjeta']);
            $this->addPaymentRow($pdf, 'Efectivo:', $totals['Efectivo']);
            
            // Total general
            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(70, 2, 'Total: $' . number_format($totalGeneral, 2), 0, 1, "R");
            
            // Número de ventas
            $pdf->Ln(2);
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(70, 2, 'Numero de ventas hoy: ' . $numberOfSells, 0, 1, "R");
            

            // Add image to pdf
            $pdf -> Image(asset('images/logoTicket.jpg'),21,2,40);
            
            // Devolver PDF
            return response($pdf->Output('S'), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="corte_'.date('Ymd_His').'.pdf"');

        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al generar el corte de caja');
        }
    }

    private function addPaymentRow($pdf, $label, $amount)
    {
        $pdf->Cell(45, 2, $label, 0, 0, "L");
        $pdf->Cell(25, 2, '$' . $amount, 0, 1, "R");
        $pdf->Ln(2);
    }
}
