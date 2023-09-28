<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title><?= $titulo ?></title>
</head>

<body>
    <div>
        <h2 align="center"><?= $mostrar_titulo ? $titulo : '' ?></h2>
        <br>
        <?= $html ?>
        <table id="reporte">
            <?= $datos ?>
        </table>
    </div>
</body>

</html>