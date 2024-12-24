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
   ```

3. Use the following structure in your `login.json` file:
   ```json
   {
       "host": "your_host",
       "user": "your_username",
       "password": "your_password",
       "database": "your_database_name"
   }
   ```

4. Open the application in a web browser and use the **Toggle Transactions** button to display the transactions table.

## Customization

- The colors for the table header, toggle button, and add button can be customized in the CSS section of the `index.html` file.
- Modify the SQL query in the PHP section to customize the transactions displayed.

## Dependencies

- **Bootstrap 3.3.7**: For styling the table and buttons.
- A PHP-enabled web server.
- MySQL database.

## Notes

- Make sure to secure your database credentials in `login.json` and restrict file access if deploying to a production server.
- The table is collapsible and initially hidden for better user experience, especially when dealing with a large number of transactions.
