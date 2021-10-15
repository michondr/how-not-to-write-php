<?php
require_once __DIR__ . '/db.php';

$allComments = $db->query('SELECT * FROM prispevky')->fetchAll();

if (!empty($_POST)) {
    var_dump($_POST);

    $db->query('insert into prispevky(autor, text) VALUES ("' . $_POST['autor'] . '", "' . $_POST['text'] . '")');
    $_POST = [];
    header('location: prispevky.php');
}

?>
<!DOCTYPE html>+
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Nástěnka s příspěvky</title>
</head>
<body>


<div class="container">
    <div class="row">
        <h1 class="col-12 mt-4 mb-3">Nástěnka s příspěvky</h1>
    </div>
    <div class="row mt-4 mb-4">
        <?php

        foreach ($allComments as $comment) {
            echo '<div class="col-lg-4">
                <div class="bg-light px-4 py-3 border border-primary">
                    <strong>' . $comment['autor'] . '</strong>
                    <p>' . $comment['text'] . '</p>
                </div>
            </div>';
        }

        ?>

    </div>
    <hr/>
    <div class="row">
        <div class="col-lg-9 col-xl-6">
            <form method="post">
                <div class="form-group">
                    <label for="text">Text příspěvku:</label>
                    <textarea class="form-control" id="text" rows="4" name="text" cols="40" required></textarea>
                </div>
                <div class="form-group">
                    <label for="autor">Autor:</label>
                    <input type="text" class="form-control" required id="autor" name="autor"/>
                </div>
                <button type="submit" class="btn btn-primary">uložit příspěvek</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>