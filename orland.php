<?php
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $search_name = $_POST["search_name"];
    $sql = "SELECT * FROM cruddd WHERE name LIKE :search_name";
    $stmt = $conn->prepare($sql);
    $search_name = "%" . $search_name . "%";
    $stmt->bindParam(":search_name", $search_name);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    try {
        $sql = "SELECT * FROM cruddd";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$editing = false;
$id = $name = $email = $mobile = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];
    $editing = true;

    try {
        $sql = "SELECT * FROM cruddd WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() == 1) {
            $name = $row["name"];
            $email = $row["email"];
            $mobile = $row["mobile"];
            $password = $row["password"];
        } else {
            echo "Record not found.";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        include("add.php");
    } elseif (isset($_POST["update"])) {
        include("update.php");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD ANEKKK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        h2,h3 {
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 800px;
            margin: 20px auto;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #bfbfbf;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #d9d9d9;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        a,input[type="submit"] {
            text-decoration: none;
            padding: 5px 10px;
            background-color: #007BFF;
            color: #ffffff;
            border-radius: 3px;
            cursor: pointer;
        }
        input[type="button"] {
            text-decoration: none;
            padding: 5px 10px;
            color: black;
            border-radius: 3px;
            cursor: pointer;
        }
        input[type="button"]:hover {
            background-color: #d9d9d9;
        }
        a:hover, input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .add {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
        }
        form {
            margin: auto auto;
        }
        .form-container {
            width: 550px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<h2>CRUD ANEKKK</h2>

<form action="index.php" method="POST" class="add">
    <label for="search_name"><b>Search Name:</b></label>
    <input type="text" id="search_name" name="search_name" placeholder="e.g., Orland" style="font-style: italic;">
    <input type="submit" name="search" value="Search">
</form>

<?php
if ($editing) {
    // para sa update
    echo "<h3>UPDATE RECORD FOR " . strtoupper($name) . "</h3>";
} else {
    // para sa add
    echo '<h3>ADD RECORD</h3>';
}
?>

<form action="index.php" method="POST" class="form-container">
        <?php if ($editing) : ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php endif; ?>

        <label for="name"><b>Name:&nbsp</b></label>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>

        <label for="email"><b>&nbsp &nbspEmail:&nbsp &nbsp &nbsp </b></label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required><br><br>

        <label for="mobile"><b>Mobile:</b></label>
        <input type="text" id="mobile" name="mobile" value="<?php echo $mobile; ?>" required>

        <label for="password"><b>&nbspPassword:</b></label>
        <input type="text" id="password" name="password" value="<?php echo $password; ?>" required><br><br>

        <?php if ($editing) : ?>
            <input type="submit" name="update" value="Update Record">
        <?php else : ?>
            <input type="submit" name="add" value="Add Record">
        <?php endif; ?>

        <input type="button" value="Cancel" onclick="window.location='index.php';">
    </form>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Password</th>
        <th>Action</th>
    </tr>
    <?php
    if (isset($result) && count($result) > 0) {
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["mobile"] . "</td>";
            echo "<td>" . $row["password"] . "</td>";
            echo "<td>";
            echo "<a href='index.php?id=" . $row["id"] . "'>Update</a> ";
            echo "<a href='delete.php?id=" . $row["id"] . "' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found.</td></tr>";
    }
    ?>
</table>
</body>
</html>