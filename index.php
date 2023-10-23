<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrudApp</title>
    <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,700;0,800;1,600&display=swap');

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Poppins', sans-serif;
            }
    </style>
</head>
<body>
    <?php
require_once "dbcon.php";

$updateMode = false;
$fname = $lname = $course = "";

if (isset($_POST["submit"])) {
    if (isset($_POST["update-mode"])) {
        $id = $_POST["update-mode"];
        if (isset($_POST["fname"])) {
            $fname = $_POST["fname"];
        }
        if (isset($_POST["lname"])) {
            $lname = $_POST["lname"];
        }
        if (isset($_POST["course"])) {
            $course = $_POST["course"];
        }

        $query = "UPDATE students SET fname=:fname, lname=:lname, course=:course WHERE id=:id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':course', $course);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php");
    } else {
        if (isset($_POST["fname"])) {
            $fname = $_POST["fname"];
        }
        if (isset($_POST["lname"])) {
            $lname = $_POST["lname"];
        }
        if (isset($_POST["course"])) {
            $course = $_POST["course"];
        }

        $query = "INSERT INTO students (fname, lname, course) VALUES (:fname, :lname, :course)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':course', $course);

        if($stmt->execute()) {
            header("Location: index.php");
        } else {
            echo "Oops, something went wrong";
        }
    }
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $query = "SELECT * FROM students WHERE id=:id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    $fname = $data["fname"];
    $lname = $data["lname"];
    $course = $data["course"]; 

    $updateMode = true;
}
?>

    <?php if(!$updateMode) : ?>
        <form action="" method="post">
            First Name: <input type="text" name="fname"><br><br>
            Last Name: <input type="text" name="lname"><br><br>
            Course: <input type="text" name="course"><br><br>
            <input type="submit" name="submit" value="Add">
        </form>
    <?php else : ?>
        <form action="?id=<?php echo $id; ?>" method="post">
            <input type="hidden" name="update-mode" value="<?php echo $id; ?>">
            First Name: <input type="text" name="fname" value="<?php echo $fname; ?>"><br><br>
            Last Name: <input type="text" name="lname" value="<?php echo $lname; ?>"><br><br>
            Course: <input type="text" name="course" value="<?php echo $course; ?>"><br><br>
            <input type="submit" name="submit" value="Update">
        </form>
    <?php endif; ?>
    <div class="data">
        <?php
        try {
            $query = "SELECT * FROM students";
            $stmt = $conn->query($query);

            if ($stmt) {
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($data) > 0) {
                    echo "<table border='1'>";
                    echo "<tr><th>First Name</th><th>Last Name</th><th>Position</th><th>Update</th><th>Delete</th></tr>";

                    foreach ($data as $row) {
                        echo "<tr>";
                        echo "<td>" . $row["fname"] . "</td>";
                        echo "<td>" . $row["lname"] . "</td>";
                        echo "<td>" . $row["course"] . "</td>";
                        echo "<td>
                            <form action='' method='get'>
                                <input type='hidden' name='id' value='" . $row["id"] . "'>
                                <input type='submit' value='Update'>
                            </form>
                        </td>";
                        echo "<td><button class='del-btn'><a href='delete.php?id=" . $row["id"] . "'>Delete</a></button></td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "No Data Found";
                }
            } else {
                echo "Error executing the query.";
            }
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
        ?>
    </div>
</body>
</html>