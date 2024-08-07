import React, { Component } from "react";
import ReactDOM from "react-dom";
import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";
// import Slip from "./Slip";
// import { renderToString } from 'react-dom/server';

class Cart extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            products: [],
            customers: [],
            code: "",
            search: "",
            customer_id: "",
            customer: "",
            discount: null,
            discountPercent: null,
            shopId: null,
            currentShop: null,
            orderToPrint: null,
        };

        this.setShop = this.setShop.bind(this);
        this.loadCart = this.loadCart.bind(this);
        this.loadDiscount = this.loadDiscount.bind(this);
        this.getCurrentShop = this.getCurrentShop.bind(this);
        this.handleOnChangeCode = this.handleOnChangeCode.bind(this);
        this.handleScanCode = this.handleScanCode.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.handleEmptyCart = this.handleEmptyCart.bind(this);

        this.loadProducts = this.loadProducts.bind(this);
        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSeach = this.handleSeach.bind(this);
        this.setCustomerId = this.setCustomerId.bind(this);
        this.setCustomer = this.setCustomer.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this);
    }

    componentDidMount() {
        //get shop id
        this.setShop();

        // load user cart
        this.loadCart();
        this.loadProducts();
        this.loadCustomers();
        this.loadDiscount();
        this.getCurrentShop();
    }

    loadCustomers() {
        axios.get(`/admin/customers`).then((res) => {
            const customers = res.data;
            this.setState({ customers });
        });
    }

    setShop() {
        const url = window.location.href;
        const parts = url.split('/');
        const shopId = parts[parts.length - 2];
        this.setState({ shopId: shopId });
    }

    loadDiscount() {
        axios.get(`/admin/settings/get-discount`).then((res) => {
            if (res && res.data) {
                this.setState({ discount: res.data.discount });
                this.setState({ discountPercent: res.data.discount_percent });
            }
        });
    }

    getCurrentShop() {
        const url = window.location.href;
        const parts = url.split('/');
        const shopId = parts[parts.length - 2];

        console.log('Will search: ', shopId);
        axios.get(`/admin/settings/get-shop/${shopId}`).then((res) => {
            console.log('Will search RES: ', res);

            if (res && res.data) {
                this.setState({ currentShop: res.data.shop });
                console.log('FINAL ', this.state.currentShop);
            }
        });
    }

    loadProducts(search = "") {
        const url = window.location.href;
        const parts = url.split('/');
        const shopId = parts[parts.length - 2];

        const query = !!search ? `?search=${search}` : "";
        axios.get(`/admin/shop-items/${shopId}/products${query}`).then((res) => {
            const products = res.data.products.data?.filter(prod => prod.product);
            this.setState({ products });
        });
    }

    handleOnChangeCode(event) {
        const code = event.target.value;
        this.setState({ code });
    }

    loadCart() {
        // axios.get("/admin/cart").then((res) => {
        //     const cart = res.data;
        //     this.setState({ cart });
        // });
    }

    handleScanCode(event) {
        event.preventDefault();
        const { code } = this.state;
        if (!!code) {
            axios
                .post("/admin/cart", { code })
                .then((res) => {
                    this.loadCart();
                    this.setState({ code: "" });
                })
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }
    handleChangeQty(product_id, qty) {
        // if (!qty) return;
        const cart = this.state.cart.map((c) => {
            if (c.product_id === product_id) {
                const maxValue = c.unit === 'pce' ? Number(qty) : Number(qty) * c.product.items_in_box

                if (Number(c.max_qty) >= maxValue) {
                    c.sell_quantity = Number(qty);
                    c.final_quantity = maxValue;
                } else {
                    c.sell_quantity = 0;
                    c.final_quantity = 0;
                }
            }
            return c;
        });

        this.setState({ cart: [] });
        this.setState({ cart: cart });

        // axios
        //     .post("/admin/cart/change-qty", { product_id, quantity: qty })
        //     .then((res) => {})
        //     .catch((err) => {
        //         Swal.fire("Error!", err.response.data.message, "error");
        //     });
    }

    handleChangeUnit(product_id, unit) {
        const cart = this.state.cart.map((c) => {
            if (c.product_id === product_id) {
                c.unit = unit;
                c.sell_quantity = 0;
                c.final_quantity = 0;
            }
            return c;
        });

        this.setState({ cart: [] });
        this.setState({ cart: cart });
    }

    getTotal(cart) {
        const total = cart?.map((c) => c.unit === 'pce' ?
            c.sell_quantity * c.sell_price :
            c.sell_quantity * c.sell_price * c.product.items_in_box
        );

        return sum(total);
    }

    getDiscount(cart) {
        let total = this.getTotal(cart);
        let discountAmount = 0;

        if (this.state.discount && this.state.discountPercent && total >= this.state.discount) {
            discountAmount = total * this.state.discountPercent / 100;
        }

        return discountAmount;
    }

    getTotalToPay(cart) {
        return this.getTotal(cart) - this.getDiscount(cart);
    }

    handleClickDelete(product_id) {
        const cart = this.state.cart.filter((c) => c.product_id !== product_id);
        this.setState({ cart });
    }
    handleEmptyCart() {
        this.setState({ customer: "" });
        this.setState({ cart: [] });
    }
    handleChangeSearch(event) {
        const search = event.target.value;
        this.setState({ search });
    }
    handleSeach(event) {
        setTimeout(() => {
            this.loadProducts(event.target.value);
        }, 1000);
        // if (event.keyCode === 13) {
        // }
    }

    addProductToCart(id) {
        let product = this.state.products.find((p) => p.id === id);
        if (!!product) {

            // if product is already in cart
            let cartProd = this.state.cart.find((c) => c.product_id === product.id);
            if (!!cartProd) {

                // update quantity
                this.setState({
                    cart: this.state.cart.map((c) => {
                        if (
                            c.product_id === product.id &&
                            product.quantity > c.quantity
                        ) {
                            c.sell_quantity = c.sell_quantity + 1;
                        }
                        return c;
                    }),
                });
            } else {
                if (product.quantity > 0) {
                    product = {
                        ...product,
                        product_id: product.id,
                        sell_quantity: null,
                        final_quantity: null,
                        max_qty: Number(product.quantity),
                        unit: 'pce',
                    };

                    this.setState({ cart: [...this.state.cart, product] });
                }
            }

            // axios
            //     .post("/admin/cart", { id })
            //     .then((res) => {
            //         // this.loadCart();
            //     })
            //     .catch((err) => {
            //         Swal.fire("Error!", err.response.data.message, "error");
            //     });
        }
    }

    setCustomerId(event) {
        this.setState({ customer_id: event.target.value });
    }

    setCustomer(event) {
        this.setState({ customer: event });
    }
    handleClickSubmit() {
        //check for null quantities
        if (this.state.cart.find((item) => !item.final_quantity)) {
            Swal.fire({
                icon: 'error',
                title: 'La facture contient 1 ou plusieurs quantités non valide',
                text: 'Les quantités valides sont celles superieur á zero',
            })

            return;
        }


        Swal.fire({
            title: "Veuillez confirmer",
            // input: "text",
            // inputValue: this.getTotal(this.state.cart),
            showCancelButton: true,
            confirmButtonText: "Imprimer",
            cancelButtonText: "Annuler",
            showLoaderOnConfirm: true,
            preConfirm: (amount) => {
                return axios
                    .post("/admin/cart-orders", {
                        customer: this.state.customer,
                        shop_id: this.state.shopId,
                        cart: this.state.cart,
                        total: this.getTotal(this.state.cart),
                        discount: this.getDiscount(this.state.cart),
                        paid: this.getTotalToPay(this.state.cart),
                    })
                    .then((res) => {
                        this.loadProducts();
                        // location.reload();
                        // Reset before printing
                        if (res && res.data && res.data.order) {
                            this.setState({ orderToPrint: res.data.order });
                            this.setState({ cart: [] });
                            this.setState({ customer: '' });
                            this.handlePrinting();
                        }
                    })
                    .catch((err) => {
                        this.loadProducts();
                        Swal.showValidationMessage(err.response.data.message);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                //
            }
        });
    }

    handlePrinting() {

        // old start
        // var content = document.getElementById("divcontents");
        // var pri = document.getElementById("ifmcontentstoprint").contentWindow;
        // pri.document.open();
        // pri.document.write(content.innerHTML);
        // pri.document.close();
        // pri.focus();
        // pri.print();
        // old end

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

    render() {
        const { cart, products, customers,customer, code, orderToPrint, currentShop } = this.state;

        // Define the styles for the outer div
        const containerStyle = {
            width: '2.3in', // 3 1/8 inches
            height: '230px',
            // border: '1px solid #000', // Add a border for visibility
            padding: '0px', // Add padding for spacing
            fontSize: '14px',
            pageBreakAfter: 'always',
        };

        // Define the styles for the table
        const tableStyle = {
            width: '100%',
            borderCollapse: 'collapse',
            marginBottom: '3px',
            marginTop: '6px',
            fontSize: '14px',
        };

        const thTdStyle = {
            border: '1px solid #000',
            padding: '5px',
            textAlign: 'left',
        };

        return (
            <div className="row">
                <div className="col-md-6">
                    <div className="row mb-2">
                        {/* <div className="col">
                            <form onSubmit={this.handleScanCode}>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Scan Code..."
                                    value={code}
                                    onChange={this.handleOnChangeCode}
                                />
                            </form>
                        </div> */}
                        <div className="col">
                            <input
                                type="text"
                                className="form-control"
                                value={customer}
                                placeholder="Nom du Client"
                                onChange={(event) =>
                                    this.setCustomer(
                                        event.target.value
                                    )
                                }
                            />
                            {/* <select
                                className="form-control"
                                onChange={this.setCustomerId}
                            >
                                <option value="">Walking Customer</option>
                                {customers.map((cus) => (
                                    <option
                                        key={cus.id}
                                        value={cus.id}
                                    >{`${cus.first_name} ${cus.last_name}`}</option>
                                ))}
                            </select> */}
                        </div>
                    </div>
                    <div className="user-cart" style={{overflowY: 'auto',}}>
                        <div className="card" style={{overflowY: 'auto',}}>
                            <table className="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Quantity</th>
                                        <th>Unité</th>
                                        <th className="text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {cart.map((c) => (
                                        <tr key={c.id}>
                                            <td><b>[code: {c.product.code}]</b> {c.product.name}</td>
                                            <td>
                                                <input
                                                    type="text"
                                                    className="form-control form-control-sm w-50 qty mr-1"
                                                    value={c.sell_quantity}
                                                    onChange={(event) =>
                                                        this.handleChangeQty(
                                                            c.product_id,
                                                            event.target.value
                                                        )
                                                    }
                                                />
                                                <button
                                                    className="btn btn-danger btn-sm"
                                                    onClick={() =>
                                                        this.handleClickDelete(
                                                            c.product_id
                                                        )
                                                    }
                                                >
                                                    <i className="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <select
                                                    className="mt-1"
                                                    value={c.unit}
                                                    onChange={(event) =>
                                                        this.handleChangeUnit(
                                                            c.product_id,
                                                            event.target.value
                                                        )
                                                    }
                                                >
                                                    <option value="pce">Piece</option>
                                                    {(c.quantity >= c.product.items_in_box) && <option value="crt">Carton</option>}
                                                </select>
                                            </td>
                                            <td className="text-right">
                                                { c.unit === 'pce'
                                                    ? c.sell_price * c.sell_quantity
                                                    : c.sell_price * c.sell_quantity * c.product.items_in_box
                                                }
                                                {" "}
                                                {window.APP.currency_symbol}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div className="card my-2 p-3">
                        <table>
                            <tr className="text-right">
                                <td></td>
                                <td>Total:</td>
                                <td className="text-left pl-2">{this.getTotal(cart)} {window.APP.currency_symbol}</td>
                            </tr>
                            <tr className="text-right">
                                <td></td>
                                <td>Reduction:</td>
                                <td className="text-left pl-2">{this.getDiscount(cart)} {window.APP.currency_symbol}</td>
                            </tr>
                            <tr className="text-right">
                                <td></td>
                                <td>Montant á Payer:</td>
                                <td className="text-left pl-2">{this.getTotalToPay(cart)} {window.APP.currency_symbol}</td>
                            </tr>
                        </table>
                    </div>

                    <div className="row">
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-danger btn-block"
                                onClick={this.handleEmptyCart}
                                disabled={!cart.length}
                            >
                                Annuler
                            </button>
                        </div>
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-primary btn-block"
                                disabled={!cart.length}
                                onClick={this.handleClickSubmit}
                            >
                                Sauvegarder
                            </button>
                        </div>
                        <div id="divcontents" style={{ display: 'none',}}>
                            <div style={containerStyle} className="receipt-container">
                                <div style={{display: 'flex', justifyContent: 'center', margingBottom: '8px',}}>
                                    <p className="centered"><span style={{ fontWeight: 'bold', fontSize: '16px', }}>3eme ADAM</span>
                                        <br />{ currentShop ? currentShop.address_line_1 : '' }
                                        <br />{ currentShop ? currentShop.address_line_2 : '' },
                                        <br />{ currentShop ? currentShop.address_line_3 : '' }
                                        {/* <br />Address: Av des Usines
                                        <br />C/Lubumbashi, RDC,
                                        <br />Contacts: +243 995 672 007 */}
                                    </p>
                                </div>

                                <div style={{display: 'flex', justifyContent: 'space-between',}}>
                                    <div>FACTURE No: { orderToPrint?.order_number }</div>
                                    <div>Date: <span style={{ fontWeight: 'bold', fontSize: '17px', }}>{ new Date().toLocaleDateString('en-GB') }</span></div>
                                </div>
                                <div style={{display: 'flex', justifyContent: 'space-between',}}>
                                    <div>Client: { orderToPrint?.customer }</div>
                                </div>

                                <table style={tableStyle}>
                                    <thead>
                                        <tr>
                                            <th style={thTdStyle}>No</th>
                                            <th style={thTdStyle}>Article</th>
                                            <th style={thTdStyle}>Qté</th>
                                            <th style={thTdStyle}>P.U</th>
                                            <th style={thTdStyle}>P.T</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {orderToPrint && orderToPrint.items && orderToPrint.items.map((item, i) => {
                                            return (
                                                <tr>
                                                    <td style={thTdStyle}>{ i+1 }</td>
                                                    <td style={thTdStyle}>{ item.product?.name}</td>
                                                    <td style={thTdStyle}>{ item.quantity }</td>
                                                    <td style={thTdStyle}>{ item.price * 1 }</td>
                                                    <td style={thTdStyle}> { item.price * item.quantity } </td>
                                                </tr>
                                            )
                                        })}
                                        {/* Add more rows as needed */}
                                    </tbody>
                                </table>

                                <div style={{display: 'flex', flexDirection: 'column', justifyContent: 'space-around', }}>
                                    <div style={{display: 'flex', justifyContent: 'space-between', fontWeight: 'bold', fontSize: '14px',}}>
                                        <div></div>
                                        <div>Total:</div>
                                        <div>{ orderToPrint?.total } FC</div>
                                    </div>
                                    <div style={{display: 'flex', justifyContent: 'space-between', fontWeight: 'bold', fontSize: '14px',}}>
                                        <div></div>
                                        <div>Reduction:</div>
                                        <div>{ orderToPrint?.discount }</div>
                                    </div>
                                    <div style={{display: 'flex', justifyContent: 'space-between', fontWeight: 'bold', fontSize: '14px',}}>
                                        <div></div>
                                        <div>Net a payer:</div>
                                        <div>{orderToPrint?.paid } FC</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="mb-2">
                        <input
                            type="text"
                            className="form-control"
                            placeholder="Search Product..."
                            onChange={this.handleChangeSearch}
                            onKeyDown={this.handleSeach}
                        />
                    </div>
                    <div className="order-product">
                        {products.map((p) => (
                            <div
                                onClick={() => this.addProductToCart(p.id)}
                                key={p.id}
                                className="item p-3 w-100 h-100"
                                style={{ maxWidth: '200px', maxHeight: '160px' }}
                            >
                                <h5 className="text-center mb-3 font-weight-bold">[{p.product.code}] { p.product.name }</h5>
                                <div>Stock Mag: {p.quantity} <i><small>Pce</small></i></div>
                                <div className="blockquote-footer">(1 CRT = {p.product.items_in_box} Pce)</div>
                                <div>Prix/pce: {p.sell_price} {window.APP.currency_symbol}</div>
                            </div>
                        ))}
                    </div>
                </div>
                <iframe
                    id="ifmcontentstoprint"
                    title="Reçue"
                    width="100%"
                    height="500px" // Set the desired height
                ></iframe>
                {/* <iframe id="ifmcontentstoprint" style={{ height: '0px', width: '0px', position: 'absolute'}}>
                </iframe> */}
            </div>
        );
    }
}

export default Cart;

if (document.getElementById("cart")) {
    ReactDOM.render(<Cart />, document.getElementById("cart"));
}
