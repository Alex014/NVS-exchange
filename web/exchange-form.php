<?php
ini_set('display_errors', true);
require __DIR__ . '/../lib/Container.php';
require __DIR__ . '/../modules/ExchangeForm.php';

use lib\Container;
use modules\ExchangeForm;

$error = false;

$exForm = Container::createExchangeForm();

$active = $exForm->pingExchangeForm();

if ($active && !empty($_POST['address']) && !empty($_POST['pay_address'])) {
    $address = $_POST['address'];
    $pay_address = $_POST['pay_address'];

    $fields = [
        'address' => $address,
        'pay_address' => $pay_address 
    ];

    if (!empty($exForm->locateSlot($fields))) {
        $error = 'nvs';
    } else {
        $last_slot_time = $exForm->getSlot()->lastSlotTime();

        if ((time() - $last_slot_time) < 30) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Denied");
            die('Time restriction (30 sec)');
        }

        $slot = $exForm->findSlot($fields);

        if (!empty($slot)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Denied");
            $error = 'db';
        } else {
            $slot_id = $exForm->createSlot($fields);
            header('location: /exchange-form-slot.php?slot=' . $slot_id );
        }
    }
} else {
    $address = '';
    $pay_address = '';
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

    <title>Emercoin Name-Value Exchange</title>
  </head>
  <body>

  <div class="container">
  <div class="row">
    <div class="col">
        <h1>NVS Exchange</h1>
        <h3>EMC,NESS,NCH to Emercoin NVS</h3>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link"href="/">BUY Name-Value record</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" aria-current="page"  href="#">Privateness V1 - V2 exchange</a>
            </li>
        </ul>

        <br>

        <?php if ($active): ?>
        
        <form action="/token.php" method="GET">

        <div class="mb-3">
            <label for="address" class="form-label">Your token address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?=$address?>"  placeholder="your_address_address-v1-with-coinhours" required>
            <div class="form-text">The address in privateness v1 network, where you have coin-hours.</div>
        </div>
        <div class="mb-3">
            <label for="pay_address" class="form-label">Your pay_address payment address</label>
            <input type="text" class="form-control" id="token" name="pay_address" value="<?=$pay_address?>"  placeholder="your_address_to-recieve-coins-v2" required>
            <div class="form-text">The address in privateness v2 network, to recieve coins.</div>
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
        <button type="submit" class="btn btn-primary">Create payment slot</button>

        </form>
        <?php else: ?>
        <div class="alert alert-danger" role="alert">
        Privateness V1 - V2 exchange found, but it is inactive (possible server failure).
        </div>
        <?php endif; ?>
    
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