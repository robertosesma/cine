<?php
session_start();
include 'func_aux.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username']) && isset($_GET['year'])) {
    $conn = connect();
    $year = clear_input($_GET["year"]);

    // obtener los datos del período
    $stmt = $conn -> prepare("SELECT * FROM listado WHERE periodo = ? AND user = ? ORDER BY mejor DESC, nota DESC");
    $stmt->bind_param('ii', $year, $_SESSION['userid']);
    $stmt->execute();
    $d = $stmt->get_result();

    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename='.$year.'.csv');
    header("Content-Transfer-Encoding: UTF-8");

    $f = fopen('php://output', 'a'); // Configure fopen to write to the output buffer
    fputcsv($f, ["Título","Realización","Fecha","Cine/Soporte","Nota","Rev.","Mejor"]);
    while ($r = mysqli_fetch_array($d)) {
        fputcsv($f, [$r["titulo"],$r["realizacion"],$r["fecha"],$r["detail"],$r["nota"],($r["rev"]==1 ? "Sí" : "No" ),($r["mejor"]==1 ? "Sí" : "No" )]);
    }
    // Close the file & download
    fclose($f);
} else {
    header("Location: logout.php");
}
exit();
?>
