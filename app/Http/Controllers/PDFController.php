<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
}
