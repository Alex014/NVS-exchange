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
            $slot_id = $slot['id'];
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emercoin Name-Value Exchange</title>
    
    <style>
        body {
            background-color: #367CA5;
            color: white;
            font-family: Arial, sans-serif;
        }

        .container {
            padding: 20px;
            margin: 20px auto;
            max-width: 90%;
            background-color: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .nav {
            background-color: #1B4F73;
            padding: 10px;
            border-radius: 5px;
        }

        .nav-link {
            color: white;
            font-size: 80%;
        }
        
        li {
             list-style: none;
        }

        .nav-link.active {
            font-weight: bold;
        }

        .form-label {
            font-weight: bold;
            color: grey;
            font-size: 65%;
        }

        .form-control {
            border: 2px solid #367CA5;
            border-radius: 5px;
            color: #367CA5;
            outline-color: skyblue;
        }

        .form-text {
            color: #1B4F73;
            font-size: 12%;
        }

        .alert {
            background-color: #FF5A5A;
            color: white;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            display: none;
        }

        .btn-primary {
            background-color: #367CA5;
            border: none;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
        }

        .btn-primary:active {
            background-color: #1B4F73;
        }
        
        .header{
            background-color: #1B4F73;
            text-align: center;
            border-radius: 5px;
            padding: 2%;
            font-size: 65%;
        }
        
        input {
             padding: 2%;
        }
        
    </style>
</head>
<body>

<div class="container">
  <div class="row">
     <div class="col">
          <div class="header">
             <h1>NVS Exchange</h1>
             <h3>EMC, 
                 NESS, 
                 NCH to Emercoin NVS
              </h3>
          </div>
       

   <ul class="nav">
        <li class="nav-item">
          <a class="nav-link" href="/">
               BUY Name-Value record
          </a>
          
        </li>
         <li class="nav-item">
           <a class="nav-link active"
            aria-current="page"
             href="#">
             Privateness V1 - V2 exchange
           </a>
          </li>
   </ul><br>

   
   <?php if ($active): ?>
      <form method="POST">
        <div class="mb-3">
         <label for="address"
           class="form-label">
            Your Token Address:
         </label><br>
           <input type="text"
            class="form-control" 
            id="address" 
            name="address" 
            value=""  placeholder="" 
            required>
    
    <div class="form-text">
         The address in privateness v1 network, where you have coin-hours.
    </div>
 </div><br>
                    
     
     <div class="mb-3">
         <label for="pay_address"
          class="form-label">
          Your Pay_Address Payment Address:
         </label><br>
                        
    <input type="text" 
      class="form-control" 
      id="token" 
      name="pay_address" 
      value="" placeholder="" required>

   <div class="form-text">
        The address in privateness v2 network, to receive coins.
   </div>
</div><br>
      <!-- Alert Messages Are Hidden in CSS By Default -->
                    
<?php if ('nvs' === $error): ?>
    <div class="alert" role="alert">
      NVS record with address 
      <?=$address?> and payment address 
      <?=$pay_address?> already exists.
     </div>
              
              
 <?php elseif ('db' === $error): ?>
    <div class="alert" role="alert">
      Slot with address <?=$address?> 
      and payment address <?=$pay_address?> already exists.<br>
      You can't pay it here 
      <a href="/slot.php?slot=<?=$slot_id?>"><?=$slot['name']?>
      </a>
     </div>
                    
 
 <?php endif; ?>
    <button type="submit" 
            class="btn btn-primary">
            Create Payment Slot
    </button>
</form>


    <?php else: ?>
      <div class="alert" role="alert">
         Privateness V1 - V2 exchange found, but it is inactive (possible server failure).
      </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>