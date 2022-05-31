<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Período</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>

<body>
<?php
require '../lib/Mobile_Detect.php';
include 'func_aux.php';
$ok = true;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username']) && isset($_GET['year'])) {
    $conn = connect();
    $detect = new Mobile_Detect;
    $year = clear_input($_GET["year"]);

    // obtener repetidos
    $stmt = $conn -> prepare("SELECT COUNT(*) AS n FROM cine WHERE periodo = ? AND user = ? AND rev = 1 GROUP BY periodo");
    $stmt->bind_param('ii', $year, $_SESSION['userid']);
    $stmt->execute();
    $d = $stmt->get_result();
    $nrev = mysqli_fetch_array($d)["n"];

    // obtener los datos del período
    $stmt = $conn -> prepare("SELECT * FROM listado WHERE periodo = ? AND user = ? ORDER BY mejor DESC, nota DESC");
    $stmt->bind_param('ii', $year, $_SESSION['userid']);
    $stmt->execute();
    $d = $stmt->get_result();
    $ntot = $d->num_rows;
    $ok = ($ntot>0);
} else {
    $ok = false;
}
?>

<?php if ($ok){ ?>
<div class="container">
    <div class="container p-3 my-3 border">
        <h3>Período <?php echo $year;?></h3>
        <?php echo "<h6>".$ntot." registros (".$nrev." revisiones)</h6>";?>
        <a class="btn btn-link" href="listado.php?n=1">Atrás</a>
        <?php echo "<a class='btn btn-link' href='export_csv.php?year=".$year."'>Exportar</a>";?>
        <a class="btn btn-link" href="logout.php">Salir</a><br>
    </div>
</div>

<div class="container">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-hover table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Título</th>
                <?php if ( $detect->isMobile() ) { ?>
                    <th><div class='text-center'>Fecha</div></th>
                <?php } else { ?>
                    <th>Realización</th>
                    <th><div class='text-center'>Fecha</div></th>
                    <th>Cine/Soporte</th>
                    <th>Nota</th>
                    <th>Rev.</th>
                    <th>Mejor</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($r = mysqli_fetch_array($d)) {
                if ($r["mejor"] == 1) {
                    echo "<tr class=table-primary>";
                } else {
                    echo "<tr>";
                } ?>
                    <td><?php echo "<a href='ficha.php?id=".$r["id"]."'>".$r["titulo"]."</a>"; ?></td>
                    <?php if ( $detect->isMobile() ) { ?>
                        <td><?php echo $r["fecha"]; ?></td>
                    <?php } else { ?>
                        <td><?php echo $r["realizacion"]; ?></td>
                        <td><?php echo $r["fecha"]; ?></td>
                        <td><?php echo $r["detail"]; ?></td>
                        <td><?php echo $r["nota"]; ?></td>
                        <td><?php echo ($r["rev"]==1 ? "Sí" : "No" ); ?></td>
                        <td><?php echo ($r["mejor"]==1 ? "Sí" : "No" ); ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <h6>* Revisión</h6>
</div>

<?php
    location.reload();
    $conn->close(); ?>
<?php } else {
    header("Location: logout.php");
}?>

</body>
</html>
