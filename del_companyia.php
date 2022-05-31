<?php
session_start();
include 'func_aux.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && isset($_SESSION['username'])) {
    if (isset($_GET['id']) && isset($_GET['con'])) {
        $conn = connect();
        $id = clear_input($_GET["id"]);

        // quitar el realizador/a
        $stmt = $conn -> prepare("DELETE FROM con WHERE id=? AND con=?");
        $stmt->bind_param('is',$id,clear_input($_GET["con"]));
        $stmt->execute();

        $conn->close();
        header("Refresh:0; url=companyia.php?id=".$id);
    }
}
exit();
?>
