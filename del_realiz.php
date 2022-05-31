<?php
session_start();
include 'func_aux.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && isset($_SESSION['username'])) {
    if (isset($_GET['id']) && isset($_GET['realiz'])) {
        $conn = connect();
        $id = clear_input($_GET["id"]);

        // quitar el realizador/a
        $stmt = $conn -> prepare("DELETE FROM realiz WHERE id=? AND realiz=?");
        $stmt->bind_param('is',$id,clear_input($_GET["realiz"]));
        $stmt->execute();

        $conn->close();
        header("Refresh:0; url=realiz.php?id=".$id);
    }
}
exit();
?>
