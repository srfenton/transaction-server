# Transactions Table Application

This project is a web-based application for managing and displaying transactions in a tabular format. The application features a collapsible table that is collapsed by default and allows adding new transactions with ease.

## Features

- Collapsible transactions table for better UI management.
- A **Toggle Transactions** button to expand/collapse the table.
- An **Add** button styled to match the table's header color for creating new transactions.
- Transactions are displayed dynamically from a database using PHP and MySQL.
- Includes hover effects and alternating row colors for better readability.

## Usage

1. Ensure you have the required database configuration file (`login.json`) in the same directory.
2. Set up your MySQL database with a `transactions` table containing the following fields:
   - `id`
   - `item`
   - `cost`
   - `date`
   - `notes`

   Example SQL to create the table:
   ```sql
   CREATE TABLE transactions (
       id INT AUTO_INCREMENT PRIMARY KEY,
       item VARCHAR(255) NOT NULL,
       cost DECIMAL(10, 2) NOT NULL,
       date DATE NOT NULL,
       notes TEXT
   );
