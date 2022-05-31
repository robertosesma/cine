<?php
session_start();
include 'func_aux.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username']) && isset($_GET['id'])) {
    $conn = connect();
    $id = clear_input($_GET["id"]);

    // eliminar la peli
    $stmt = $conn -> prepare("DELETE FROM cine WHERE id=?");
    $stmt->bind_param('i',$id);
    $stmt->execute();

    $conn->close();
    header("Refresh:0; url=listado.php?n=1");
}
exit();
?>
