@extends('layouts.admin')

@section('title', 'Factures')
@section('content-header', 'List Des Factures')
@section('content-actions')
{{-- <a href="{{route('shop.cart.index', $shop->id)}}" class="btn btn-success">
    <i class="fas fa-cart-plus pr-1"></i>Facturation
</a> --}}
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-4">
                            <select @if(!$user->is_admin) disabled @endif name="shop" id="" class="form-control">
                                <option value="0" @if($shopId=='0' ) selected @endif>Tous Les Magasins</option>
                                @foreach ($shops as $shop)
                                <option value="{{ $shop->id }}" @if($shopId==$shop->id) selected @endif>{{ $shop->name
                                    }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" placeholder="Numero facture" name="search" class="form-control"
                                value="{{request('search')}}" />
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="start_date" class="form-control"
                                value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="row col-md-2">
                            <button class="btn btn-primary" type="submit">Filtrer</button>
                            <div class="col-md-2">
                                <a href="{{route('orders.list.pdf')}}?{{ request()->getQueryString() }}" target="_blank"><button class="btn btn-success" type="button">Imprimer List</button></a>
                            </div>
                            {{-- <button class="btn btn-secondary" type="submit">Telecharger PDF</button> --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Numero Facture</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Crée par</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                @if ($order->deleted_at)
                <tr class="text-red text-bold" style="text-decoration: line-through">
                @else
                <tr>
                @endif
                    <td>{{$order->order_number}}</td>
                    <td>{{$order->customer}}</td>
                    <td>{{ number_format($order->paid, 0, ',', '.') }} {{ config('settings.currency_symbol') }}</td>
                    <td>{{$order->user->first_name}}</td>
                    <td>{{$order->created_at}}</td>
                    <td>
                        <button class="ml-1 btn btn-primary btinformation" data-toggle="modal"
                            data-target="#billDetails" data-order="{{ $order }}" data-isadmin="{{ $user->is_admin }}" data-toggle="tooltip"
                            data-placement="bottom" title="Voir Facture">
                            <i class="fas fa-info"></i>
                        </button>

                        <a href="{{route('reprint.shop.order', ['order' => $order->id])}}">
                        <button class="ml-1 btn btn-primary btinformation" data-toggle="modal">
                            Imprimer
                        </button>
                        </a>

                        {{-- <button class="ml-2 btn btn-primary re-print" data-url="{{route('orders.destroy', $order)}}"
                            data-toggle="tooltip" data-placement="bottom" title="Imprimer"><i
                            class="fas fa-print"></i>Print
                        </button> --}}
                        @if ($user->is_admin && !$order->deleted_at)
                        <button class="ml-2 btn btn-danger btn-delete" data-url="{{route('orders.destroy', $order)}}"
                            data-toggle="tooltip" data-placement="bottom" title="Supprimer"><i
                            class="fas fa-trash"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th></th>
                    <th>{{ number_format($total, 0, ',', '.') }} {{ config('settings.currency_symbol') }} </th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        {{ $orders->render() }}
    </div>
    <div class="modal fade" id="billDetails" tabindex="-1" role="dialog" aria-labelledby="billDetailsLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="billDetailsLabel"><b class="">Facture No</b>: <span
                            id="orderNumber"><span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="d-flex flex-row justify-content-between">
                        <u>
                            <h6 class="modal-title"><b class="">Client:</b> <span id="orderCustomer"><span></h6>
                        </u>
                        <i>
                            <h6 class="modal-title"><b class="">Saisie Par:</b> <span id="orderUser"><span></h6>
                        </i>
                    </div>

                    <table class="table" id="data-table">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Article</td>
                                <td>Qté</td>
                                <td>P.U</td>
                                <td>P.T</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($order->items as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->product->sell_price }} FC</td>
                                <td>{{ $item->product->sell_price * $item->quantity }}</td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                    <table class="table">
                        <thead>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <h6><b>Total:</b></h6>
                                <td>
                                    <h6><b id="orderTotal"></b></h6>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <h6><b>Reduction:</b></h6>
                                <td>
                                    <h6><b id="orderDiscount"> FC</b></h6>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <h5><b>Montant payé:</b></h5>
                                <td>
                                    <h5><b id="orderPaid"></b> <i>FC</i></h5>
                                </td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer d-flex flex-row justify-content-center">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>
<iframe
    id="ifmcontentstoprint"
    title="Reçue"
    width="100%"
    height="500px"
></iframe>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.0.slim.min.js"
    integrity="sha256-tG5mcZUtJsZvyKAxYLVXrmjKBVLd6VpVccqz/r4ypFE=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {
        $(document).on('click', '.btn-delete', function () {
            $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Etes-vous sur?',
                text: "Voulez-vous vraiment supprimer cette facture?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, Supprimer!',
                cancelButtonText: 'No',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {_method: 'DELETE', _token: '{{csrf_token()}}'}, function (res) {
                        $this.closest('tr').fadeOut(500, function () {
                            $(this).remove();
                            location.reload();
                        })
                    })
                }
            })
        });

        $(document).on('click', '.btn-delete-single', function () {
            $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Supprimer Cet Article?',
                text: "Voulez-vous vraiment supprimer cet article de la facture?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'No',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    var itemId = $(this).data('itemid');
                    var orderId = $(this).data('orderid');
                    console.log("VIRTUAL ", itemId, orderId)
                    $.post(`/admin/orders/${orderId}/item/${itemId}/delete`, {_token: '{{csrf_token()}}'}, function (res) {
                        $this.closest('tr').fadeOut(500, function () {
                            $(this).remove();
                            location.reload();
                        })
                    })
                }
            })
        });

        $('#billDetails').on('show.bs.modal', function (event) {
            $this = $(this);
            var button = $(event.relatedTarget); // Button that triggered the modal
            var order = button.data('order');
            var isadmin = button.data('isadmin');
            var tableBody = $('#data-table tbody');
            tableBody.empty(); // Clear existing data if any

            $(this).find('#orderNumber').text(order.order_number ?? '');
            $(this).find('#orderCustomer').text(order.customer ?? '');
            $(this).find('#orderUser').text(order.user?.first_name ?? '');
            $(this).find('#orderTotal').text(order.total ?? '');
            $(this).find('#orderDiscount').text(order.discount ?? '');
            $(this).find('#orderPaid').text(order.paid ?? '');


            $.each(order?.items, function (index, row) {
                var newRow = '<tr>' +
                    '<td>' + (isadmin) + '</td>' +
                    '<td>' + row.product?.name + '</td>' +
                    '<td>' + row.quantity + '</td>' +
                    '<td>' + Math.floor(row.price) + '</td>' +
                    '<td>' + Math.floor(Number(row.price) * Number(row.quantity)) + '</td>';

                    // Check if admin for delete action
                    if (isadmin) {
                        newRow += '<td><button class="ml-2 btn btn-danger btn-delete-single" data-itemId="' + row.id + '" data-orderId="' + order.id +'">Delete</button></td>';
                    } else {
                        newRow += '<td></td>';
                    }

                    newRow += '</tr>';

                tableBody.append(newRow);
            });
        });

        function handlePrinting() {

            console.log('IMPRIMONSS')

            // Define your CSS styles as a string
        const styles = `
            /* Define A5 page size for printing */
            @page {
                size: A5;
                margin: 0;
            }

            /* Define the content area on the A5 page */
            @media print {
                html, body {
                    width: 110mm; /* A5 width in millimeters */
                    height: 210mm; /* A5 height in millimeters */
                    margin: 0;
                }
                body {
                    padding: 1mm; /* Add some padding to fit content within A5 */
                }
            }

            /* Styles for the <div> you want to print */
            .printable-content {
                width: 100mm; /* Adjust the width to fit content within A5 */
                height: 190mm; /* Adjust the height to fit content within A5 */
                background-color: white; /* Ensure a white background for printing */
                /* Add other styles as needed */
            }
        `;

        // Get the iframe element
        const iframe = document.getElementById('ifmcontentstoprint');

        // Get the iframe's document
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        // Create a <style> element and append the CSS styles
        const styleElement = iframeDoc.createElement('style');
        styleElement.innerHTML = styles;
        iframeDoc.head.appendChild(styleElement);

        // Trigger the print dialog for the iframe's content
        var content = document.getElementById("divcontents");
        var pri = iframe.contentWindow;
        pri.document.open();
        pri.document.write(content.innerHTML);
        pri.document.close();
        pri.focus();
        pri.print();
        // iframe.contentWindow.print();
        }

    });
</script>
@endsection
