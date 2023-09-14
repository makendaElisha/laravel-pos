import React, { Component } from 'react';

const slipStyles = {
  slip: {
    visibility: 'visible',
    position: 'absolute',
    left: 0,
    top: 0,
    width: '300px', // Adjust the width as needed
    textAlign: 'center',
  },
  slipTable: {
    borderCollapse: 'collapse',
    width: '100%',
  },
  tableHeader: {
    backgroundColor: '#f2f2f2',
  },
  tableCell: {
    border: '1px solid #ddd',
    padding: '8px',
  },
};

class Slip extends Component {
  render() {
    return (
      <div style={slipStyles.slip}>
        <div className="header">
          <h1>Slip</h1>
        </div>
        <table style={slipStyles.slipTable} className="slip-table">
          <thead>
            <tr style={slipStyles.tableHeader}>
              <th style={slipStyles.tableCell}>Item</th>
              <th style={slipStyles.tableCell}>Price</th>
            </tr>
          </thead>
          <tbody>
            <tr style={slipStyles.tableCell}>
              <td style={slipStyles.tableCell}>Item 1</td>
              <td style={slipStyles.tableCell}>$10.00</td>
            </tr>
            <tr style={slipStyles.tableCell}>
              <td style={slipStyles.tableCell}>Item 2</td>
              <td style={slipStyles.tableCell}>$15.00</td>
            </tr>
            <tr style={slipStyles.tableCell}>
              <td style={slipStyles.tableCell}>Item 3</td>
              <td style={slipStyles.tableCell}>$20.00</td>
            </tr>
          </tbody>
        </table>
      </div>
    );
  }
}

export default Slip;
