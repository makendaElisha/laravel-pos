import React, { Component } from "react";
import ReactDOM from "react-dom";
import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";

class CartReprint extends Component {
    constructor(props) {
        super(props);
        this.state = {
            order: null,
            shopId: null,
            currentShop: null,
            orderToPrint: null,
        };

        this.loadOrder = this.loadOrder.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this);
    }

    componentDidMount() {
        this.loadOrder();
    }

    loadOrder() {
        const url = window.location.href;
        const parts = url.split('/');
        const orderId = parts.pop();

        axios.get(`/admin/current/reprint/order/${orderId}`).then((res) => {
            if (res && res.data) {
                this.setState({ order: res.data.order });
                this.setState({ orderToPrint: res.data.order });

            }
        });
    }

    handleClickSubmit() {
        this.handlePrinting();
    }

    handlePrinting() {
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
                        <div className="col">
                            <input
                                type="text"
                                className="form-control"
                                value={orderToPrint?.customer}
                                placeholder="Nom du Client"
                                readOnly
                            />
                        </div>
                    </div>
                    <div className="user-cart" style={{overflowY: 'auto',}}>
                        <div className="card" style={{overflowY: 'auto',}}>
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
                                <div style={{display: 'flex', flexDirection: 'column', justifyContent: 'space-around', padding: '10px'}}>
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

                    <div className="row">
                        {/* <div className="col">
                            <button
                                type="button"
                                className="btn btn-danger btn-block"
                                onClick={this.handleEmptyCart}
                                disabled={!cart.length}
                            >
                                Annuler
                            </button>
                        </div> */}
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-primary btn-block"
                                onClick={this.handleClickSubmit}
                            >
                                Re-Imprimer
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

export default CartReprint;

if (document.getElementById("cartreprint")) {
    ReactDOM.render(<CartReprint />, document.getElementById("cartreprint"));
}
