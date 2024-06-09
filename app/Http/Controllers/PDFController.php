<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use TCPDF;

class PDFController extends Controller
{
    public function generatePDF()
    {
        $date = date('d/m/Y');
        $products = Product::all();
        $totalCount = count($products) ?? '';
        $title = "List Globale des Articles (Total: $totalCount)";

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('3emeAdam');
        $pdf->SetAuthor('3emeAdam');
        $pdf->SetTitle('3emeAdam - pos');

        // Add a page
        $pdf->AddPage();

        // HTML content
        $html = '
        <html>
        <head>
            <title>3emeAdam - pos</title>
            <style>
                .custom-table {
                    width: 100%;
                    margin-bottom: 1rem;
                    background-color: white;
                    border-collapse: collapse;
                }

                .custom-table th, .custom-table td {
                    padding: 0.75rem;
                    vertical-align: top;
                    border-top: 1px solid black;
                    border-left: 1px solid black;
                    border-right: 1px solid black;
                }

                .custom-table thead th {
                    vertical-align: bottom;
                    border-bottom: 2px solid black;
                }

                .custom-table tbody + tbody {
                    border-top: 2px solid black;
                }

                .custom-table-sm th, .custom-table-sm td {
                    padding: 0.3rem;
                }

            </style>
        </head>
        <body>
            <h1>' . $title . '</h1>
            <p>' . $date . '</p>
            <table class="custom-table custom-table-bordered">
                <tr>
                    <th>Code</th>
                    <th colspan="2">Nome</th>
                    <th>Quantité</th>
                </tr>';

        // Loop through products and add rows to the table
        foreach ($products as $product) {
            $html .= '<tr>
                        <td>' . $product->code . '</td>
                        <td colspan="2">' . $product->name . '</td>
                        <td></td>
                    </tr>';
        }

        $html .= '</table>
        </body>
        </html>';

        // Output the HTML content as a PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output the PDF to the browser or save it to a file
        $pdf->Output('example.pdf', 'I');
    }

    public function generateShopPDF(Shop $shop)
    {
        $date = date('d/m/Y');
        $products = ShopProduct::with('product')->where('shop_id', $shop->id)->get();
        $totalCount = count($products) ?? '';
        $title = "$shop->name: Stock des Articles (Total: $totalCount)";

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('3emeAdam');
        $pdf->SetAuthor('3emeAdam');
        $pdf->SetTitle('3emeAdam - pos');

        // Add a page
        $pdf->AddPage();

        // HTML content
        $html = '
        <html>
        <head>
            <title>3emeAdam - pos</title>
            <style>
                .custom-table {
                    width: 100%;
                    margin-bottom: 1rem;
                    background-color: white;
                    border-collapse: collapse;
                }

                .custom-table th, .custom-table td {
                    padding: 0.75rem;
                    vertical-align: top;
                    border-top: 1px solid black;
                    border-left: 1px solid black;
                    border-right: 1px solid black;
                }

                .custom-table thead th {
                    vertical-align: bottom;
                    border-bottom: 2px solid black;
                }

                .custom-table tbody + tbody {
                    border-top: 2px solid black;
                }

                .custom-table-sm th, .custom-table-sm td {
                    padding: 0.3rem;
                }

            </style>
        </head>
        <body>
            <h1>' . $title . '</h1>
            <p>' . $date . '</p>
            <table class="custom-table custom-table-bordered">
                <tr>
                    <th>Code</th>
                    <th colspan="2">Nome</th>
                    <th>Quantité</th>
                </tr>';

        // Loop through products and add rows to the table
        foreach ($products as $shopProduct) {
            $code = $shopProduct->product->code ?? '';
            $name = $shopProduct->product->name ?? '';
            $quantity = $shopProduct->product->quantity ?? '';

            $html .= '<tr>
                        <td>' . $code . '</td>
                        <td colspan="2">' . $name . '</td>
                        <td>' . $quantity . '</td>
                    </tr>';
        }

        $html .= '</table>
        </body>
        </html>';

        // Output the HTML content as a PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output the PDF to the browser or save it to a file
        $pdf->Output('shopArticles.pdf', 'I');
    }

    public function generateBillsPDF(Request $request)
    {
        $shop = Shop::where('id', $request->shop)->first();

        if (!$shop) {
            return;
        }


        // Generate PDF
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $displayStartDate = Carbon::parse($startDate)->toDateString();
        $displayEndDate = Carbon::parse($endDate)->toDateString();

        // Create an array to hold the dates
        $dateArray = [];
        $totalSales = 0;

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $currentDate = Carbon::parse($date)->toDateString();
            $dayOrders = Order::where('shop_id', $shop->id)->whereDate('created_at', $currentDate);

            $dateArray[$currentDate]["billed"] = $dayOrders->sum('total');
            $dateArray[$currentDate]["descounted"] = $dayOrders->sum('discount');
            $dateArray[$currentDate]["received"] = $dayOrders->sum('paid');

            $totalSales += $dateArray[$currentDate]["billed"];
        }
        // dd($dateArray);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('3emeAdam');
        $pdf->SetAuthor('3emeAdam');
        $pdf->SetTitle('3emeAdam - pos');

        // Add a page
        $pdf->AddPage();

        // HTML content
        $html = '
        <html>
        <head>
            <title>3emeAdam - pos</title>
            <style>
                .custom-table {
                    width: 100%;
                    margin-bottom: 1rem;
                    background-color: white;
                    border-collapse: collapse;
                }

                .custom-table th, .custom-table td {
                    padding: 0.75rem;
                    vertical-align: top;
                    border-top: 1px solid black;
                    border-left: 1px solid black;
                    border-right: 1px solid black;
                }

                .custom-table thead th {
                    vertical-align: bottom;
                    border-bottom: 2px solid black;
                }

                .custom-table tbody + tbody {
                    border-top: 2px solid black;
                }

                .custom-table-sm th, .custom-table-sm td {
                    padding: 0.3rem;
                }

            </style>
        </head>
        <body>
            </br>

            <h2>List des ventes journalieres: ' . $displayStartDate . ' - ' . $displayEndDate . '</h2>
            </br>
            <h3>TOTAL VENDU: <b>' . number_format($totalSales, 2, ',', '.') . 'FC</b></h3>
            <p>Magazin: 3emeAdam ' . $shop->name . '</p>
            <table class="custom-table custom-table-bordered">
                <tr>
                    <th>Dates de vente</th>
                    <th>Total Facturé</th>
                    <th>Total Reduction</th>
                    <th>Total Encaissé</th>
                </tr>';

                foreach ($dateArray as $key => $daySale) {
                    $billed = $daySale["billed"] ?? '';
                    $descounted = $daySale["descounted"] ?? '';
                    $received = $daySale["received"] ?? '';

                    $html .= '<tr>
                                <td> '. $key .'</td>
                                <td> '. $billed .' FC</td>
                                <td> '. $descounted .' FC</td>
                                <td> '. $received .' FC</td>
                            </tr>';
                }

        $html .= '</table>
        </body>
        </html>';

        // Output the HTML content as a PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output the PDF to the browser or save it to a file
        $pdf->Output('ventes journalieres.pdf', 'I');
    }
}
