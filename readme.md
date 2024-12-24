Excel to MySQL Transaction Importer

This project reads an Excel spreadsheet containing transaction data, processes each row, checks for duplicate entries in a MySQL database, and inserts the data into the transactions table if no duplicate is found.
Prerequisites

Before you begin, ensure you have the following installed:

    Node.js: Install Node.js
    MySQL: Install MySQL
    ExcelJS: A library for reading Excel files.
    Moment.js: A library for parsing, validating, and formatting dates.
    MySQL Node.js Client: A MySQL driver for Node.js.

Dependencies

You can install the required dependencies via npm:

npm install moment-timezone mysql exceljs

Configuration
1. Database Connection

In the project folder, create a login.json file with the following structure:

{
  "host": "your-mysql-host",
  "user": "your-mysql-username",
  "password": "your-mysql-password",
  "database": "your-database-name"
}

Replace the placeholders with your actual MySQL credentials and database name.
2. Spreadsheet Location

Create a spreadsheet-location.json file with the path to the Excel file you want to process:

{
  "location": "path/to/your/excel-file.xlsx"
}

How It Works

This script performs the following steps:

    Load Excel Workbook: The script reads the Excel workbook located at the path defined in spreadsheet-location.json.

    Process Each Row: Starting from the second row (to avoid processing headers), the script:
        Extracts the values from each cell in the row.
        Checks if the cost is an object and extracts the result value.
        Handles missing or undefined values, particularly for the item and cost fields.
        Formats the transaction date to YYYY-MM-DD using moment.utc().

    Check for Duplicates: For each transaction, the script checks the transactions table in MySQL to see if a row with the same id and cost already exists.

    Insert New Transaction: If no duplicate is found, the script inserts the transaction into the transactions table.

    Exit Condition: After processing all rows, the script gracefully ends the connection to the MySQL database and exits.

Script Execution

Run the script using the following command:

node index.js

Notes

    The script assumes the Excel file has the following columns:
        id (Transaction ID)
        item (Item Name)
        cost (Cost of the Item)
        category (Category of the Transaction)
        payment_method (Payment Method)
        date (Date of the Transaction)
        notes (Additional Notes)

    The script exits immediately if an empty item value is found in any row or if a duplicate transaction is detected.

    Once the script finishes processing all rows, it will close the MySQL connection and exit.

Troubleshooting

    If you encounter any issues with database connections, ensure that your MySQL credentials in login.json are correct.
    If the script fails due to missing dependencies, run npm install to install the required libraries.
    In case of unexpected errors, check the logs printed in the console to identify the issue.