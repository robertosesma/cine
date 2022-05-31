<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Listado</title>
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
    && isset($_SESSION['username'])) {
    $conn = connect();
    $detect = new Mobile_Detect;

    $titulo = "%".clear_input($_POST["titulo"])."%";
    $realiz = "%".clear_input($_POST["realiz"])."%";

    // obtener resultado de la búsqueda
    $stmt = $conn -> prepare("SELECT * FROM listado WHERE titulo LIKE ? AND realizacion LIKE ? AND user = ?");
    $stmt->bind_param('ssi', $titulo, $realiz, $_SESSION['userid']);
    $stmt->execute();
    $d = $stmt->get_result();
    $nrows = $d->num_rows;
} else {
    $ok = false;
}
?>

<?php if ($ok){ ?>
<div class="container">
    <div class="container p-3 my-3 border">
        <h3>Resultado búsqueda</h3>
        <?php echo "<h6>Número de registros: ".$nrows."</h6>";?>
        <!-- <a class="btn btn-link" href="export.php?n=1">Exportar</a> -->
        <a class="btn btn-link" href="listado.php?n=1">Atrás</a>
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
                    <th><div class='text-center'>Año</div></th>
                    <th>Dónde</th>
                    <th>Cine/Soporte</th>
                    <th>Nota</th>
                    <th>Compañía</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php

            while ($r = mysqli_fetch_array($d)) {
                $nota = ($r["rev"] == 1 ? '*' : '');?>
                <tr>
                    <td><?php echo "<a href='ficha.php?id=".$r["id"]."'>".$r["titulo"].$nota."</a>"; ?></td>
                    <?php if ( $detect->isMobile() ) { ?>
                        <td><?php echo $r["fecha"]; ?></td>
                    <?php } else { ?>
                        <td><?php echo $r["realizacion"]; ?></td>
                        <td><?php echo $r["fecha"]; ?></td>
                        <td><?php echo $r["periodo"]; ?></td>
                        <td><?php echo $r["donde"]; ?></td>
                        <td><?php echo $r["detail"]; ?></td>
                        <td><?php echo $r["nota"]; ?></td>
                        <td><?php echo $r["companyia"]; ?></td>
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
