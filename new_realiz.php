<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Nuevo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>

<body>
<?php
include 'func_aux.php';
$ok = true;
if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && isset($_SESSION['username']))
    && (isset($_GET['id'])  || $_SERVER["REQUEST_METHOD"] == "POST")) {
    $conn = connect();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // añadir el realizador/a
        $id = clear_input($_POST["id"]);
        $stmt = $conn -> prepare("INSERT INTO realiz (id,realiz) VALUES (?,?)");
        $stmt->bind_param('is',$id,clear_input($_POST["nom"]));
        $stmt->execute();
        header("Refresh:0; url=realiz.php?id=".$id);
    }
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = clear_input($_GET["id"]);
    }

} else {
    $ok = false;
} ?>

<?php if ($ok) { ?>
<div class="container">
    <div class="page-header">
        <h2>Nuevo realizador/a</h2>
        <?php echo '<a class="btn btn-link" href="realiz.php?id='.$id.'">Atrás</a>'; ?>
        <a class="btn btn-link" href="logout.php">Salir</a>
    </div>
</div>

<div class="container p-3 my-3 border">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label for="nom">Nombre:</label>
            <input type="text" class="form-control" name="nom">
        </div>
        <input type="text" class="form-control" hidden="true" name="id" value="<?php echo $id; ?>">
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</div>

<?php
    $conn->close();
} else {
    header("Location: logout.php");
}?>

</body>
</html>
