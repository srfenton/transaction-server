<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<form method="POST" action="map_headers.php">

    <!-- Table -->
    <table class="table table-striped table-dark">
        <thead>
            <tr>
                <th scope="col">CSV</th>
                <th scope="col">Database Columns</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Assuming the file was uploaded and processed earlier
            $target_dir = "uploads/";
            $file_name = basename($_FILES["fileToUpload"]["name"]);
            $uploaded_file = $_FILES["fileToUpload"]["tmp_name"];
            $db_columns = ['item', 'cost', 'date']; // The columns for mapping

            function is_csv($filename) {
                $length = strlen($filename);
                if ($length < 3) {
                    return false;
                }
                $lastThree = substr($filename, -3);
                return ($lastThree === 'csv');
            }

            if (is_csv($file_name)) {
                $my_file = fopen($uploaded_file, "r") or die("Unable to open file!");

                // Read the first line as headers
                $headers = fgetcsv($my_file);

                // Read the rest of the lines as transactions
                $transactions = [];
                while ($x = fgetcsv($my_file)) {
                    $transactions[] = $x;
                }

                fclose($my_file);

                // Serialize the transactions into a hidden field
                $transactions_json = json_encode($transactions);

                // Display the table with headers and dropdowns for each column
                foreach ($headers as $index => $x) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($x) . "</td>"; // Display header name
                    echo "<td>";
                    echo "<select name='db_column_mapping[]'>"; // Array name for the dropdowns
                    foreach ($db_columns as $db_col) {
                        echo "<option value='" . htmlspecialchars($db_col) . "'>" . htmlspecialchars($db_col) . "</option>";
                    }
                    echo "</select>";
                    echo "</td>";
                    echo "</tr>";
                }

                // Add hidden input to pass the transactions to the next page
                echo "<input type='hidden' name='transactions' value='" . htmlspecialchars($transactions_json) . "'>";
            } else {
                echo "<script>alert('Please submit a CSV file');
                window.location.href = 'upload_test.php';
                </script>";
            }
            ?>
        </tbody>
    </table>

    <!-- Submit Button -->
    <button type="submit" name="submit" class="btn btn-primary">Submit</button>

</form>

</body>
</html>
