<?php
session_start();
include 'func_aux.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username']) && ($_SERVER["REQUEST_METHOD"] == "POST")) {

    $id = clear_input($_POST['id']);
    if (isset($_POST["afegir"])) {
        // añadir una compañía que ya existe
        $conn = connect();
        $companyia = clear_input($_POST['companyia']);

        $stmt = $conn -> prepare('SELECT * FROM con WHERE id = ? AND con = ?');
        $stmt->bind_param('is', $id, $companyia);
        $stmt->execute();
        $d = $stmt->get_result();
        if ($d->num_rows==0) {
            // si la compañía no se ha añadido, añadirla
            $stmt = $conn -> prepare("INSERT INTO con (id,con) VALUES (?,?)");
            $stmt->bind_param('is',$id,$companyia);
            $stmt->execute();
        } else {
            // si el autor ya se ha añadido, no hacer nada
        }
        $d->free();
        $conn->close();
        header("Refresh:0; url=companyia.php?id=".$id);
    }

    if (isset($_POST["nuevo"])) {
        // añadir un artista nuevo: pedir los datos
        header("Location: new_companyia.php?id=".$id);
    }
} else {
    header("Location: logout.php");
}
?>
