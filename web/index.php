<?php
require __DIR__ . '/../lib/Slots.php';
require __DIR__ . '/../lib/DB.php';

ini_set('display_errors', true);

use lib\Stots;
use lib\DB;

$error = false;

$config = require __DIR__ . '/../config/config.php';
$fdb = $config['db'];

if (!empty($_POST['name']) && !empty($_POST['value'])) {
    $name = $_POST['name'];
    $value = $_POST['value'];

    $db = new DB($fdb['host'], $fdb['database'], $fdb['user'], $fdb['password']);
    $slots = new Stots($db, $config['exchange']['min_sum']);

    if (!empty($slots->locateSlot($_POST['name']))) {
        $error = 'nvs';
    } else {
        $last_slot_time = $slots->lastSlotTime();

        if ((time() - $slots->lastSlotTime()) < 30) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Denied");
            die('Time restriction');
        }

        $slot = $slots->findSlot($_POST['name']);

        if (!empty($slot)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Denied");
            $error = 'db';
        } else {
            $slot_id = $slots->createSlot($_POST['name'], $_POST['value']);
            header('location: /slot.php?slot=' . $slot_id );
        }

    }
} else {
    $name = '';
    $value = '';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Privateness Exchange Form</title>
  </head>
  <body>

  <div class="container">
  <div class="row">
    <div class="col">
        <h1>NVS Exchange</h1>
        <h3>Emercoin: EMC to NVS</h3>

        <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="name" class="form-control" id="name" name="name" value="<?=$name?>" placeholder="Name" required>
            <div id="nameHelp" class="form-text">Emercoin NVS name</div>
        </div>

        <div class="mb-3">
            <label for="value" class="form-label">Value</label>
            <textarea name="value" id="value" class="form-control" cols="30" rows="10" placeholder="Value" required><?=$value?></textarea>
            <div id="valueHelp" class="form-text">Emercoin NVS value</div>
        </div>
        <?php if ('nvs' === $error): ?>
        <div class="alert alert-danger" role="alert">
            NVS with name <b><?=htmlentities($name)?></b> olready exist.
        </div>
        <?php elseif ('db' === $error): ?>
        <div class="alert alert-danger" role="alert">
            Slot with name <b><?=htmlentities($name)?></b> olready exist. <br/>
            You cant pay it here <a href="/slot.php?slot=<?=$slot['slot_id']?>"><?=$slot['slot_id']?></a>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Create exchange token</button>
        </form>
    
    </div>
  </div>
  </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>