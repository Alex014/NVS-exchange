<?php
require __DIR__ . '/../lib/Container.php';

ini_set('display_errors', true);

use lib\Container;

if (empty($_GET['slot'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

$slots = Container::createSlots();

$slot = $slots->showSlot($_GET['slot']);

if (empty($slot)) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

if ('PAYED' === $slot['status']) {
    $result = true;
} elseif (isset($_POST["check"])) {
    $result = $slots->processSlot($_GET['slot']);
} elseif (isset($_POST["delete"])) {
    $result = $slots->deleteSlot($_GET['slot']);
    header('location: /');
    die();
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
                
                <?php foreach ($slot['addr'] as $name => $addr): ?>
                <h2>
                    <b><?= $addr['descr']?>:</b> Send <?=  $addr['min_sum'] ?> <?=$name?> to
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-line me-2"><?=  $addr['addr'] ?></span> <button onclick="copy('<?=  $addr['addr'] ?>','#copy_button_<?=$name?>')" id="copy_button_<?=$name?>" class="btn btn-sm btn-success copy-button">Copy</button>
                    </div>
                </h2>
                <?php endforeach; ?>

                <h3>NAME: <?= htmlentities($slot['name']) ?></h3>
                <h3>VALUE: <?= nl2br(htmlentities($slot['value'])) ?></h3>

                <?php if(isset($result)): ?>
                    <?php if($result): ?>
                        <div class="alert alert-success" role="alert">
                            Payment confirmed.<br/>
                            NVS created !
                        </div>
                    <?php else: ?>


                <form method="POST">
                    <input type="hidden" name="check" value=""/>
                    <button type="submit" class="btn btn-primary">Confirm money send</button>
                </form>

                <div class="alert alert-danger" role="alert">
                    Transaction not confirmed yet.
                </div>
                    <?php endif; ?>
                <?php else: ?>

                    <dr/>

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