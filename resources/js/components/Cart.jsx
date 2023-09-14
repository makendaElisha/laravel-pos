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
        };

        this.setShop = this.setShop.bind(this);
        this.loadCart = this.loadCart.bind(this);
        this.loadDiscount = this.loadDiscount.bind(this);
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

    loadProducts(search = "") {
        const url = window.location.href;
        const parts = url.split('/');
        const shopId = parts[parts.length - 2];

        const query = !!search ? `?search=${search}` : "";
        axios.get(`/admin/shop-items/${shopId}/products${query}`).then((res) => {
            const products = res.data.products.data;
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
        if (!qty) return;
        const cart = this.state.cart.map((c) => {
            if (c.product_id === product_id) {
                const maxValue = c.unit === 'pce' ? Number(qty) : Number(qty) * c.product.items_in_box
                if (Number(c.max_qty) >= maxValue) {
                    c.sell_quantity = Number(qty);
                    c.final_quantity = maxValue;
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
            c.sell_quantity * c.product.sell_price :
            c.sell_quantity * c.product.sell_price * c.product.items_in_box
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
        if (event.keyCode === 13) {
            this.loadProducts(event.target.value);
        }
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
                        sell_quantity: 1,
                        final_quantity: 1,
                        max_qty: Number(product.quantity),
                        unit: 'pce',
                    };

                    console.log('ADDed ', product);

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
                        location.reload();
                    })
                    .catch((err) => {
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
    render() {
        const { cart, products, customers,customer, code } = this.state;
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
                    <div className="user-cart">
                        <div className="card">
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
                                                    ? c.product.sell_price * c.sell_quantity
                                                    : c.product.sell_price * c.sell_quantity * c.product.items_in_box
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
                                <div>Prix/pce: {p.product.sell_price} {window.APP.currency_symbol}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        );
    }
}

export default Cart;

if (document.getElementById("cart")) {
    ReactDOM.render(<Cart />, document.getElementById("cart"));
}
