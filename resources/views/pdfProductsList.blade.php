<html>
<head>
    <title>Laravel 10 Generate PDF Example - ItSolutionStuff.com</title>
    <style>
        .custom-table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
        }

        .custom-table th, .custom-table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .custom-table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .custom-table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }

        .custom-table-sm th, .custom-table-sm td {
            padding: 0.3rem;
        }

    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $date }}</p>

    <table class="table table-bordered table-sm">
        <tr>
            <th>Code</th>
            <th>Nome</th>
            <th>Quantité</th>
        </tr>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->code }}</td>
            <td>{{ $product->name }}</td>
            <td></td>
        </tr>
        @endforeach
    </table>

</body>
</html>
