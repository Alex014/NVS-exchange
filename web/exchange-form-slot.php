<?php
ini_set('display_errors', true);
require __DIR__ . '/../lib/Container.php';
require __DIR__ . '/../modules/ExchangeForm.php';

use lib\Container;
use modules\ExchangeForm;

if (empty($_GET['slot'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

$exForm = Container::createExchangeForm();

$active = $exForm->pingExchangeForm();

$slot = $exForm->showSlot($_GET['slot']);

if (empty($slot)) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

$status = strtolower($slot['status']);

if ('generated' === $status) {
    if (isset($_POST["check"])) {
        $exForm->getSlot()->processSlot($_GET['slot']);
        $slot = $exForm->showSlot($_GET['slot']);
    } elseif (isset($_POST["delete"])) {
        $exForm->getSlot()->deleteSlot($_GET['slot']);
        header('location: /');
        die();
    }
}

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

    <title>Slot # <?= $slot['id'] ?></title>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col">
                <h1> <a href="/">&lt;&lt;&lt; GO BACK</a> </h1>
                
                <?php if ($active): ?>

                <?php if('generated' === $status): ?>

                <form method="POST">
                    <input type="hidden" name="check" value=""/>
                    <button type="submit" class="btn btn-primary">Confirm money send</button>
                </form>

                <?php endif; ?>

                <?php foreach ($slot['addr'] as $name => $addr): ?>
                <h2>
                    <b><?= $addr['descr']?>:</b> Send <?=  $addr['min_sum'] ?> <?=$name?> to
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-line me-2"><?=  $addr['addr'] ?></span> <button onclick="copy('<?=  $addr['addr'] ?>','#copy_button_<?=$name?>')" id="copy_button_<?=$name?>" class="btn btn-sm btn-success copy-button">Copy</button>
                    </div>
                </h2>
                <?php endforeach; ?>

                <h3>Address: <?= htmlentities($slot['address']) ?></h3>
                <h3>Payment address: <?= nl2br(htmlentities($slot['pay_address'])) ?></h3>

                <?php if('generated' === $status): ?>

                <div class="alert alert-danger" role="alert">
                    Transaction not confirmed yet.
                </div>

                <div class="float-start">
                <form method="POST"  class="float-left">
                    <input type="hidden" name="check" value=""/>
                    <button type="submit" class="btn btn-primary">Confirm money send</button>
                </form>
                </div>

                <div class="float-end">
                <form method="POST" class="float-right">
                    <input type="hidden" name="delete" value=""/>
                    <button type="submit" class="btn btn-danger">Delete slot (!)</button>
                </form>
                </div>

                <?php elseif('payed' === $status): ?>

                <div class="alert alert-success" role="alert">
                Payment confirmed.<br/>
                NVS created !
                <br/><br/>
                Waiting the exchange to accept the token
                </div>

                <?php elseif('activated' === $status): ?>

                <div class="alert alert-success" role="alert">
                <p>Your token is activated</p>
                </div>
                <p>Your have <b><?=$slot['hours']?></b> HOURS on <b><?=$slot['address']?></b> (v1)</p>
                <p>Transmit any ammount (0.000001) 
                    from <b><?=$slot['address']?></b> (v2) 
                    to <b><?=$slot['gen_address']?></b> (v2) 
                    and you will recieve <?=$slot['recieve']?> NESS on your address <b><?=$slot['pay_address']?></b> (v2) </p>

                <?php elseif('done' === $status): ?>

                <div class="alert alert-success" role="alert">
                <p>Your token is payed</p>
                </div>
                <p>Your had <b><?=$slot['hours']?></b> HOURS on <b><?=$slot['address']?></b> (v1)</p>
                <p>You have been payed <?=$slot['recieve']?> NESS</p>
                <p>Check your balance at <b><?=$slot['pay_address']?></b> (v2) </p>

                <?php elseif('error' === $status): ?>

                <div class="alert alert-danger" role="alert">
                <?= nl2br(htmlentities($slot['error'])) ?>
                </div>

                <?php endif; ?>

                <?php else: ?>

                <div class="alert alert-danger" role="alert">
                Privateness V1 - V2 exchange found, but it is inactive (possible server failure).
                </div>

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