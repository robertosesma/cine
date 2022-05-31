<?php
session_start();
include 'func_aux.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username']) && ($_SERVER["REQUEST_METHOD"] == "POST")) {

    $id = clear_input($_POST['id']);
    if (isset($_POST["afegir"])) {
        // añadir un realizador/a que ya existe
        $conn = connect();
        $realiz = clear_input($_POST['realiz']);

        $stmt = $conn -> prepare('SELECT * FROM realiz WHERE id = ? AND realiz = ?');
        $stmt->bind_param('is', $id, $realiz);
        $stmt->execute();
        $d = $stmt->get_result();
        if ($d->num_rows==0) {
            // si el realizador/a no se ha añadido, añadirlo
            $stmt = $conn -> prepare("INSERT INTO realiz (id,realiz) VALUES (?,?)");
            $stmt->bind_param('is',$id,$realiz);
            $stmt->execute();
        } else {
            // si el autor ya se ha añadido, no hacer nada
        }
        $d->free();
        $conn->close();
        header("Refresh:0; url=realiz.php?id=".$id);
    }

    if (isset($_POST["nuevo"])) {
        // añadir un artista nuevo: pedir los datos
        header("Location: new_realiz.php?id=".$id);
    }
} else {
    header("Location: logout.php");
}
?>
