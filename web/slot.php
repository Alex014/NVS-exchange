<?php
require __DIR__ . '/../lib/Slots.php';
require __DIR__ . '/../lib/DB.php';

ini_set('display_errors', true);

use lib\Stots;
use lib\DB;

if (empty($_GET['slot'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

$config = require __DIR__ . '/../config/config.php';
$fdb = $config['db'];

$db = new DB($fdb['host'], $fdb['database'], $fdb['user'], $fdb['password']);
$slots = new Stots($db, $config['exchange']['min_sum']);

$slot = $slots->showSlot($_GET['slot']);

if ('PAYED' === $slot['status']) {
    $result = true;
} elseif (isset($_POST["check"])) {
    $result = $slots->processSlot($_GET['slot']);
}

$slot = $slots->showSlot($_GET['slot']);
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background: #B9B4B4
        }

        .card {
            border: none;
            height: 100%
        }

        .copy-button {
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative
        }

        .tip {
            background-color: #263646;
            padding: 0 14px;
            line-height: 27px;
            position: absolute;
            border-radius: 4px;
            z-index: 100;
            color: #fff;
            font-size: 12px;
            animation-name: tip;
            animation-duration: .6s;
            animation-fill-mode: both
        }

        .tip:before {
            content: "";
            background-color: #263646;
            height: 10px;
            width: 10px;
            display: block;
            position: absolute;
            transform: rotate(45deg);
            top: -4px;
            left: 17px
        }

        #copied_tip {
            animation-name: come_and_leave;
            animation-duration: 1s;
            animation-fill-mode: both;
            bottom: -35px;
            left: 2px
        }
    </style>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Slot # <?= $slot['slot_id'] ?></title>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col">
                <h1> <a href="/">&lt;&lt;&lt; GO BACK</a> </h1>

                <h2>
                    Send <?= $config['exchange']['min_sum'] ?> EMC to
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-line me-2"><?= $slot['addr'] ?></span> <button onclick="copy('<?= $slot['addr'] ?>','#copy_button')" id="copy_button" class="btn btn-sm btn-success copy-button">Copy</button>
                    </div>
                </h2>

                <h3>NAME: <?= htmlentities($slot['name']) ?></h3>
                <h3>VALUE: <?= htmlentities($slot['value']) ?></h3>

                <?php if(isset($result)): ?>
                    <?php if($result): ?>
                        <div class="alert alert-success" role="alert">
                            Confirmed.
                        </div>
                    <?php else: ?>


                <form method="POST">
                    <input type="hidden" name="check" value=""/>
                    <button type="submit" class="btn btn-primary">Confirm money send</button>
                </form>

                <div class="alert alert-danger" role="alert">
                    Transaction not confirmed yet. <br/>
                    Wait ~ 10 min
                </div>
                    <?php endif; ?>
                <?php else: ?>

                <form method="POST">
                    <input type="hidden" name="check" value=""/>
                    <button type="submit" class="btn btn-primary">Confirm money send</button>
                </form>

                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous"></script>

    <script type="text/javascript">
        function copy(text, target) {
            setTimeout(function() {
                $('#copied_tip').remove();
            }, 800);
            $(target).append("<div class='tip' id='copied_tip'>Copied!</div>");
            var input = document.createElement('input');
            input.setAttribute('value', text);
            document.body.appendChild(input);
            input.select();
            var result = document.execCommand('copy');
            document.body.removeChild(input)
            return result;
        }
    </script>
</body>

</html>