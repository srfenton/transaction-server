

<!DOCTYPE HTML>
<html>  
<head>
    <style>
        form {
            text-align: center;
            display: inline;
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 12px 20px;
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 4px;
            background-color: #f8f8f8;
            resize: none;
        }           
    </style>
    <script>
        function validateForm() {
            const category = document.getElementById("category").value;
            const paymentMethod = document.getElementById("payment_method").value;

            if (category === "none") {
                alert("Please select a valid category.");
                return false;
            }

            if (paymentMethod === "none") {
                alert("Please select a valid payment method.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
<!-- the below php script should be a get request that needs modified to accept an id number from the index page form action-->
<?php
$loginData = json_decode(file_get_contents("login.json"), true);
$servername = $loginData["host"];
$username = $loginData["user"];
$password = $loginData["password"];
$dbname = $loginData["database"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//ATTN the id value needs to be obtained from the request/form action.
$id = htmlspecialchars($_GET["id"]);
$stmt = $conn->prepare("SELECT id, item, cost, date, category, payment_method, notes FROM transactions where id = ?");
$stmt->bind_param('s', $id);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}
$stmt->execute();
$result = $stmt->get_result(); 
// Fetch the data
$data = $result->fetch_assoc();

?>

<form action="update_item.php" method="post" onsubmit="return validateForm()">
<input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>" required>   
<input type="text" placeholder="item" name="item" value="<?php echo htmlspecialchars($data['item']); ?>" required><br><br>
<input type="number" placeholder="cost" name="cost" value="<?php echo htmlspecialchars($data['cost']);?>" required><br><br>
<select id="category" name="category" size="1" value="<?php echo htmlspecialchars($data['category']); ?>" required>
    <!--<option value="none" selected disabled hidden > category </option>-->
    <option value="Cash Withdrawal">Cash Withdrawal</option>
    <option value="Dining">Dining</option>
    <option value="Education">Utilities</option>
    <option value="Fees">Fees</option>
    <option value="Fun">Fun</option>
    <option value="Gifts">Gifts</option>
    <option value="Groceries">Groceries</option>
    <option value="Health & Beauty">Health & Beauty</option>
    <option value="Household">Household</option>
    <option value="Housing">Housing</option>
    <option value="Tax">Tax</option>
    <option value="Transportation">Transportation</option>
    <option value="Travel">Travel</option>
    <option value="Utilities">Utilities</option>
        
  </select><br><br>
<input type="date" placeholder="date" name="date" value="<?php echo htmlspecialchars($data['date']); ?>" required><br><br>
<select id="payment_method" name="payment_method" size="1" value="<?php echo htmlspecialchars($data['payment_method']); ?>" required>
    <!--<option value="none" selected disabled hidden > payment method </option> -->
    <option value="Cash">Cash</option>
    <option value="Credit">Credit</option>
    <option value="Shared Expenses">Shared Expenses</option>
</select><br><br>
<textarea placeholder="notes" name="notes"><?php echo htmlspecialchars($data['notes']); ?></textarea> <br><br>

<input type="submit">
</form>
<br><br>
<a href="/">home</a>
</body>
</html>
