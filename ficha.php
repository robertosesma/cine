<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Película</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>

<body>
<?php
include 'func_aux.php';
$ok = true;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && isset($_SESSION['username'])
    && (isset($_GET['id']) || $_SERVER["REQUEST_METHOD"] == "POST")) {
    // Create connection
    $conn = connect();

    if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
        $id = clear_input($_POST["id"]);
        if (isset($_POST["realiz"])) {
            // editar la realización
            header("Location: realiz.php?id=".$id);
        }
        if (isset($_POST["companyia"])) {
            // editar la compañía
            header("Location: companyia.php?id=".$id);
        }
        if (isset($_POST["guardar"])) {
            // grabar los cambios
            $title = clear_input($_POST["title"]);
            $trad = clear_input($_POST["trad"]);
            $year = clear_input($_POST["year"]);
            list($d,$m,$y) = explode("/",clear_input($_POST["fecha"]));
            $fecha = date("Y-m-d",mktime(0,0,0,$m,$d,$y));
            $periodo = $y;
            $donde = clear_input($_POST["donde"]);
            $detail = clear_input($_POST["detail"]);
            $nota = clear_input($_POST["nota"]);
            $nota = (strlen($nota)==0 ? null : $nota);
            $rev = clear_input($_POST["rev"]=="activado");
            $rev = ($rev == 1 ? 1 : 0);
            $mejor = clear_input($_POST["mejor"]=="activado");
            $mejor = ($mejor == 1 ? 1 : 0);

            if (checkdate($m,$d,$y) && $y>2020) {
                $stmt = $conn -> prepare("UPDATE cine SET title=?, trad=?, year =?, fecha=?, periodo=?,
                    donde=?, detail=?, nota=?, rev=?, mejor=? WHERE id=? AND user=?");
                $stmt->bind_param('ssisiiiiiiii', $title, $trad, $year, $fecha, $periodo, $donde,
                            $detail, $nota, $rev, $mejor, $id, clear_input($_SESSION['userid']));
                $stmt->execute();
            }
            header("Location: listado.php?id=".$id);
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "GET" ) {
        $new = 0;
        $id = clear_input($_GET["id"]);

        // diccionarios select
        $stmt = $conn -> prepare('SELECT * FROM donde');
        $stmt->execute();
        $donde = $stmt->get_result();
        $stmt = $conn -> prepare('SELECT * FROM detail ORDER BY value');
        $stmt->execute();
        $detail = $stmt->get_result();
        $stmt = $conn -> prepare('SELECT * FROM nota');
        $stmt->execute();
        $nota = $stmt->get_result();

        if ($id==0) {
            $new = 1;
            // añadir el nuevo registro
            $id = getnextid($conn);
            $stmt = $conn -> prepare('INSERT INTO cine (id,user) VALUES (?,?)');
            $stmt->bind_param('ii',$id,clear_input($_SESSION['userid']));
            $stmt->execute();
            $title = $trad = $realizacion = $year = $date = $companyia = '';
            $fecha = date('d/m/Y');
            $periodo = date('Y');
            $rev = 0;
            $mejor = 0;
        } else {
            $stmt = $conn -> prepare('SELECT * FROM cine WHERE id = ?');
            $stmt->bind_param('i',  $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows>0) {
                $d = mysqli_fetch_array($result);

                $title = $d["title"];
                $trad = $d["trad"];
                $stmt = $conn -> prepare("SELECT GROUP_CONCAT(realiz order by realiz ASC separator ', ') AS realizacion
                        FROM realiz WHERE id = ? GROUP BY id");
                $stmt->bind_param('i',$id);
                $stmt->execute();
                $r = mysqli_fetch_array($stmt->get_result());
                $realizacion = $r["realizacion"];
                $year = $d["year"];
                $date = new DateTime($d["fecha"]);
                $fecha = $date->format('d/m/Y');
                $stmt = $conn -> prepare("SELECT GROUP_CONCAT(con order by con ASC separator ', ') AS companyia
                        FROM con WHERE id = ? GROUP BY id");
                $stmt->bind_param('i',$id);
                $stmt->execute();
                $r = mysqli_fetch_array($stmt->get_result());
                $companyia = $r["companyia"];
                $periodo = $d["periodo"];
                $rev = $d["rev"];
                $mejor = $d["mejor"];
            } else {
                $ok = false;
            }
        }
    }
} else {
    $ok = false;
}
?>

<?php if ($ok) { ?>
<div class="container">
    <div class="page-header">
        <?php if ($new==0) {
            echo "<h2>Película</h2>";
        } else {
            echo "<h2>Nueva película</h2>";
        } ?>
        <h4>Identificador: <?php echo $id; ?></h4>
        <?php echo '<a class="btn btn-link" href="del_peli.php?id='.$id.'">Borrar</a>';
        if ($new!=1) {
            echo '<a class="btn btn-link" href="listado.php?id='.$id.'">Atrás</a>';
        } ?>
        <a class="btn btn-link" href="logout.php">Salir</a>
    </div>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-row mb-2">
            <label for="title">Título:</label>
            <textarea class="form-control" rows="1" max-rows="2" id="title" name="title"><?php echo $title ?></textarea>
        </div>
        <div class="form-row mb-3">
            <label for="title">Traducción:</label>
            <textarea class="form-control" rows="1" max-rows="2" id="trad" name="trad"><?php echo $trad ?></textarea>
        </div>
        <div class="input-group mb-2">
            <input type="text" class="form-control" readonly=true value="<?php echo $realizacion ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-primary" name="realiz" type="submit">Realización</button>
            </div>
        </div>
        <div class="form-row mb-2">
            <div class="col">
                <label for="min">Año:</label>
                <input type="text" class="form-control" type="number" name="year" value="<?php echo $year ?>">
            </div>
            <div class="col">
                <label for="fecha">Fecha:</label>
                <input name="fecha" data-date-format="dd/mm/yyyy" class="form-control" value="<?php echo $fecha ?>">
            </div>
            <div class="col">
                <label for="year">Período:</label>
                <input type="text" class="form-control" type="number" readonly=true name="periodo" value="<?php echo $periodo ?>">
            </div>
        </div>
        <div class="form-row mb-3">
            <div class="col">
                 <label for="donde">Donde:</label>
                 <select name="donde" class="custom-select">
                     <?php while ($t = mysqli_fetch_array($donde)) {
                         $selected = ($t["value"]==$d["donde"] ? "selected" : "");
                         echo '<option '.$selected.' value="'.$t["value"].'">'.$t["label"].'</option>';
                     } ?>
                 </select>
             </div>
             <div class="col">
                  <label for="detail">Soporte/Cine:</label>
                  <select name="detail" class="custom-select">
                      <?php echo '<option value=""> </option>';
                      while ($t = mysqli_fetch_array($detail)) {
                          if (is_null($d["detail"])) $selected = "";
                          else $selected = ($t["value"]==$d["detail"] ? "selected" : "");
                          echo '<option '.$selected.' value="'.$t["value"].'">'.$t["label"].'</option>';
                      } ?>
                  </select>
              </div>
              <div class="col">
                   <label for="nota">Nota:</label>
                   <select name="nota" class="custom-select">
                       <?php
                       echo '<option value=""> </option>';
                       while ($t = mysqli_fetch_array($nota)) {
                           if (is_null($d["nota"])) $selected = "";
                           else $selected = ($t["value"]==$d["nota"] ? "selected" : "");
                           echo '<option '.$selected.' value="'.$t["value"].'">'.$t["value"].": ".$t["label"].'</option>';
                       } ?>
                   </select>
               </div>
        </div>
        <div class="input-group mb-2">
            <input type="text" class="form-control" readonly=true value="<?php echo $companyia ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" name="companyia" type="submit">Compañía</button>
            </div>
        </div>
        <div class="custom-control custom-checkbox">
            <?php $check_rev = ($rev==1 ? "checked" : ""); ?>
            <input type="checkbox" class="custom-control-input" name="rev" id="rev"
                value="activado" <?php echo $check_rev; ?>>
            <label class="custom-control-label" for="rev">Revisión</label>
        </div>
        <div class="custom-control custom-checkbox">
            <?php $check_mejor = ($mejor==1 ? "checked" : ""); ?>
            <input type="checkbox" class="custom-control-input" name="mejor" id="mejor"
                value="activado" <?php echo $check_mejor; ?>>
            <label class="custom-control-label" for="mejor">Mejor</label>
        </div>

        <input type="text" class="form-control" hidden="true" name="id" value="<?php echo $id; ?>">
        <button class="btn btn-primary mt-2" name="guardar" type="submit">Guardar</button>
    </form>
</div>

<?php
    location.reload();
    $conn->close();
} else {
    header("Location: logout.php");
}?>

</body>
</html>
