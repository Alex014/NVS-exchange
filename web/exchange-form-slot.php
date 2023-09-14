<?php
// Enable error display for debugging purposes
ini_set('display_errors', true);

// Include necessary PHP files and classes
require __DIR__ . '/../lib/Container.php';
require __DIR__ . '/../modules/ExchangeForm.php';

// Import the necessary classes and namespaces
use lib\Container;
use modules\ExchangeForm;

// Check if 'slot' parameter is empty in the GET request
if (empty($_GET['slot'])) {
    // If empty, send a 404 Not Found header and exit with an error message
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

// Create an instance of the ExchangeForm class
$exForm = Container::createExchangeForm();

// Check the status of the exchange form
$active = $exForm->pingExchangeForm();

// Retrieve information about the specified slot
$slot = $exForm->showSlot($_GET['slot']);

// Check if the slot is empty (not found)
if (empty($slot)) {
    // If empty, send a 404 Not Found header and exit with an error message
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

// Set the status based on the slot's status
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Slot # <?= $slot['id'] ?></title>
    <style>
        
        body {
            background: #367CA5;
            color: #333; 
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            margin: 20px auto;
            max-width: 85%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .alert {
            
            color: #FF5A5A;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            font-style: italic;
            font-weight: bold;
            display: none;
            
        }
        
        .tx-not-conf-yet {
             display: block;
        }
        
        .alert-success {
             color: #4CAF50;
        }

        .btn-primary {
            background-color: #367CA5;
            border: none;
            color: white;
            border-radius: 5px;
            padding: 8px 14px;
            margin-left: 1%;
        }

        .btn-primary:active {
            background-color: #1B4F73;
        }

        .btn-success {
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
            float: right;
        }
        
        .btn-danger {
             background-color: #FF5A5A;
             border: none;
             color: white;
             border-radius: 5px;
             padding: 8px 15px;
        }
        
        .btn-danger:active {
             background-color: lightcoral;
        }

        .btn-success:active {
            background-color: #45a049;
        }

        .text-line {
            text-decoration: underline;
            font-size: 0.6em;
            color: dimgrey;
            font-style: italic;
        }
        
        .address, .payment-address {
             font-size: 0.8em;
             color: dimgrey;
             font-style: italic;
        }

        .copy-button {
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        
        .copy-button:active {
             background-color: lightgreen;
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
            animation-fill-mode: both;
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
            left: 17px;
        }

        #copied_tip {
            animation-name: come_and_leave;
            animation-duration: 1s;
            animation-fill-mode: both;
            bottom: -35px;
            left: 2px;
        }
        
        input {
        
           border: 2px solid #367CA5;
           outline-color: skyblue;
           border-radius: 3px;
           margin: 1.4%;
           padding: 1.5%;  
        }
        
        .active-and-generated-conf-money-send-form, 
        .h2,
        .conf-money-send-and-deleting-slot-form,
        .delete-slot-form,
        .payed-reload-form,
        .token-activation-reload-form,
        .token-activated-token-info,
        .v2-token-address,
        .ness-to-receive-info,
        .ness-to-receive-reload-form,
        .hours-payment-success-hours-info,
        .ness-amount-received-info,
        .token-balance-address {
             display: none;
        }
        .display-active-and-generated-conf-money-send-form,
        .h2-shown,
        .display-conf-money-send-and-deleting-slot-form,
        .display-delete-slot-form,
        .display-payed-reload-form,
        .display-alert-success-payment,
        .display-token-activation-reload-form,
        .display-token-activation-success-msg,
        .display-token-activated-token-info,
        .display-v2-token-address,
        .display-ness-to-receive-info,
        .display-ness-to-receive-reload-form,
        .display-token-payment-success-msg,
        .display-hours-payment-success-hours-info,
        .display-ness-amount-received-info,
        .display-token-balance-address,
        .show-error,
        .display-server-failure {
             display: block;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col">
            <h1><a href="/">&lt;&lt;&lt; GO BACK</a></h1>
            
            <?php if ($active): ?>
            
            <?php if ('generated' === $status): ?>
            
            <!-- Form for confirming money send -->
            <form 
            method="POST"
         class="active-and-generated-conf-money-send-form <?php if ($active && 'generated'=== $status): ?>display-active-and-generated-conf-money-send-form<?php endif; ?>">
                <input 
                 type="text" 
                 name="check" 
                 value="" 
                 placeholder="Enter confirmation"
                 required />
                <button 
                type="submit" 
                class="btn btn-primary">
                Confirm money send
                </button>
            </form>
            
        <!-- Loop through addresses to send money to -->
   <?php foreach ($slot['addr'] as $name => $addr): ?>
            <h2 class="h2 <?php if ($active && 'generated' === $status): ?>h2-shown<?php endif; ?>">
                <b><?= $addr['descr'] ?></b> Send <?= $addr['min_sum'] ?> <?= $name ?> to:
                <div class="d-flex mb-3">
                    <span class="text-line me-2"><?= $addr['addr'] ?> 
                    </span>
                    <button 
                     onclick="copy('<?= $addr['addr'] ?>','#copy_button_<?= $name ?>')" 
                     id="copy_button_<?= $name ?>" class="btn btn-sm btn-success copy-button">
                     Copy
                     </button>
                </div>
            </h2>
   <?php endforeach; ?>
            
            <?php endif; ?>
            
            <!-- Display the slot's address and payment address -->
            <h3>Address: 
              <span class="address">
                   <?= htmlentities($slot['address']) ?>
              </span>
              </h3>
              
            <h3>Payment address:         
              <span class="payment-address"><?= nl2br(htmlentities($slot['pay_address'])) ?>
              </span>
            </h3>
              
           <?php if ('generated' === $status): ?>
           
         <!-- Alert for unconfirmed transaction --> 
         <div class="alert alert-danger <?php if ('generated' === $status): ?>tx-not-conf-yet<?php endif; ?>" role="alert"> Transaction not confirmed yet! 
         </div>
            
            <!-- Forms for confirming money send and deleting slot -->
            <div class="float-start">
                <form method="POST" 
              class="conf-money-send-and-deleting-slot-form <?php if ('generated' === $status): ?>display-conf-money-send-and-deleting-slot-form<?php endif; ?>">
                    <input 
                      type="text" 
                      name="check" 
                      value="" 
                      placeholder="Enter confirmation"
                      required />
                    <button 
                     type="submit" 
                     class="btn btn-primary">
                     Confirm money send
                    </button>
                </form>
            </div>
            <div class="float-end">
                <form method="POST" 
                 class="delete-slot-form <?php if ('generated' === $status): ?>display-delete-slot-form<?php endif; ?>">
                    <input 
                     type="text" 
                     name="delete" 
                     value="" 
                     placeholder="Enter slot to delete"
                     required />
                    <button 
                     type="submit" 
                     class="btn btn-danger">
                     Delete slot (!)
                    </button>
                </form>
            </div>
            
            <?php elseif ('payed' === $status): ?>
            
            <!-- Form for reloading the page -->
            <div class="float-start">
                <form method="GET" 
                class="payed-reload-form <?php if ('payed' === $status): ?>display-payed-reload-form<?php endif; ?>">
                    <input 
                     type="text" 
                     name="slot" 
                     value="" 
                     placeholder="Press Reload button"
                     />
                   <button 
                    type="submit" 
                    class="btn btn-primary">
                    Reload
                   </button>
                </form>
            </div>
            
            <!-- Alert for successful payment -->
            <div 
            class="alert alert-success <?php if ('payed' === $status): ?>display-alert-success-payment<?php endif; ?>" role="alert">
                <b>Payment confirmed !</b><br>
                <b>NVS created !</b><br><br>
                Waiting for the exchange to accept the token (This can take up to 10 min)...
            </div>
            
  <div class="float-start">
     <form method="GET"
     class="payed-reload-form <?php if ('payed' === $status): ?>display-payed-reload-form<?php endif; ?>">
         <input 
            type="text" 
            name="slot" 
            value=""
            placeholder="Press Reload button"
            />
          <button 
             type="submit" 
             class="btn btn-primary">
             Reload
          </button>
      </form>
  </div>
  
  
  <?php elseif ('activated' === $status): ?>
        
        <!-- Form for reloading the page -->
        <div class="float-start">
            <form method="GET" 
            class="token-activation-reload-form <?php if ('activated' === $status): ?>display-token-activation-reload-form<?php endif; ?>">
                <input 
                 type="text" 
                 name="slot" 
                 value="" 
                 placeholder="Press Reload button"
                 />
                <button type="submit" class="btn btn-primary">Reload</button>
            </form>
        </div>
        
        <br><br>
        
        <!-- Alert for activated token -->
        <div 
         class="alert alert-success <?php if ('activated' === $status): ?>display-token-activation-success-msg<?php endif; ?>" 
         role="alert">
            <p>Your token is activated !</p>
        </div>
        
        <p style="color: grey; font-style: italic;" 
         class="token-activated-token-info <?php if ('activated' === $status): ?>display-token-activated-token-info<?php endif; ?>">You have <b><?= $slot['hours'] ?></b> HOURS on <b><?= $slot['address'] ?></b> (v1)</p>
         
        <p style="color: grey; font-style: italic;" 
        class="token-activated-token-info <?php if ('activated' === $status): ?>display-token-activated-token-info<?php endif; ?>">Transmit any amount (0.000001)</p>
        
        <!-- Copy address buttons -->
        <div 
        class="mb-3 v2-token-address <?php if ('activated' === $status): ?>display-v2-token-address<?php endif; ?>">
            <span style="color: grey;" class="text-line me-2">From <b><?= $slot['address'] ?></b> (v2)
            </span>
            
            <button 
            onclick="copy('<?= $slot['address'] ?>','#copy_button_<?= $slot['address'] ?>')" 
            id="copy_button_<?= $slot['address'] ?>" 
            class="btn btn-sm btn-success copy-button">
            Copy
            </button>
        </div>
        
        
        <div class="mb-3 v2-token-address <?php if ('activated' === $status): ?>display-v2-token-address<?php endif; ?>">
            <span style="color: grey;" class="text-line me-2">To <b><?= $slot['gen_address'] ?></b> (v2)
            </span>
            <button 
            onclick="copy('<?= $slot['gen_address'] ?>','#copy_button_<?= $slot['gen_address'] ?>')" 
            id="copy_button_<?= $slot['gen_address'] ?>" 
            class="btn btn-sm btn-success copy-button">
            Copy
            </button>
        </div>
        
        <p style="color: grey; font-style: italic;" 
        class="ness-to-receive-info <?php if ('activated' === $status): ?>display-ness-to-receive-info<?php endif; ?>">You will receive <?= $slot['recieve'] ?> NESS on your address <b><?= $slot['pay_address'] ?></b> (v2)</p>
        
        <!-- Form for reloading the page -->
        <div class="float-start">
            <form method="GET" 
            class="ness-to-receive-reload-form <?php if ('activated' === $status): ?>display-ness-to-receive-reload-form<?php endif; ?>">
                <input 
                 type="text" 
                 name="slot" 
                 value="" 
                 placeholder="Press Reload button"
                  />
                <button 
                 type="submit" 
                 class="btn btn-primary">
                 Reload
                </button>
            </form>
        </div>
        
        <?php elseif ('done' === $status): ?>
        
        <!-- Alert for successful token payment -->
        <div 
        class="alert alert-success <?php if ('done' === $status): ?>display-token-payment-success-msg<?php endif; ?>" role="alert">
            <p><b>Your token is paid !</b></p>
        </div>
        
        <p style="color: grey; font-style: italic;" 
        class="hours-payment-success-hours-info <?php if ('done' === $status): ?>display-hours-payment-success-hours-info<?php endif; ?>">You had <b><?= $slot['hours'] ?></b> HOURS on <b><?= $slot['address'] ?></b> (v1)
        </p>
        
        <p style="color: grey; font-style: italic;" 
        class="ness-amount-received-info <?php if ('done' === $status): ?>display-ness-amount-received-info<?php endif; ?>">You have been paid <?= $slot['recieve'] ?>
         NESS
        </p>
        
        <p style="color: grey; font-style: italic;"
         class="token-balance-address <?php if ('done' === $status): ?>display-token-balance-address<?php endif; ?>">
             Check your balance at <b><?= $slot['pay_address'] ?></b> (v2)
        </p>
        
        <?php elseif ('error' === $status): ?>
        
        <!-- Alert for error status -->
        <div 
        class="alert alert-danger <?php if ('error' === $status): ?>show-error<?php endif; ?>" role="alert"><?= nl2br(htmlentities($slot['error'])) ?>
        </div>
        
        <?php endif; ?>
        
        <?php else: ?>
        
        <!-- Alert for inactive exchange -->
        <div 
         class="alert alert-danger <?php if (true): ?>display-server-failure<?php endif; ?>" role="alert">Privateness V1 - V2 exchange found, but it is inactive (possible server failure) !
        </div>
        
        <?php endif; ?>
    </div>
</div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous">      
    </script>
    <script>
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
            document.body.removeChild(input);
            return result;
        }
    </script>
</body>
</html>
