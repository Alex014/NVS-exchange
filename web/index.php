<?php
ini_set('display_errors', true);
require __DIR__ . '/../lib/Container.php';

use lib\Container;

$error = false;

$config = require __DIR__ . '/../config/config.php';
$fdb = $config['db'];

if (!empty($_POST['name']) && !empty($_POST['value'])) {
    $name = $_POST['name'];
    $value = $_POST['value'];

    $slots = Container::createSlots();

    if (!empty($slots->locateSlot($_POST['name']))) {
        $error = 'nvs';
    } else {
        $last_slot_time = $slots->lastSlotTime();

        if ((time() - $slots->lastSlotTime()) < 30) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Denied");
            die('Time restriction (30 sec)');
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
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emercoin Name-Value Exchange</title>
    
    <!-- Custom CSS for appealing UI -->
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

        .form-label {
            font-weight: bold;
            color: grey;
            position: absolute;
            margin-top: 6%;
            margin-left: 50px;
        }
        
        

        .btn-primary {
            background-color: #367CA5;
            border: none;
            margin-top: 7%;
            margin-left: 50px;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
        }

        .btn-primary:active {
            background-color: #1B4F73;
            cursor: pointer;
        }
        
 
      
        
        h1, h3 {
            color: #367CA5;
            text-align: center;
        }

        .form-control {
            border: 1px solid #367CA5;
            border-radius: 5px;
            color: #367CA5;
            outline-color: #367CA5;
            margin-top: 7%;
            margin-left: 50px;
        }
        

        .form-text {
            color: #1B4F73;
            font-size: 8px;
            margin-left: 55px;
        }
        
        textarea {
           
           outline-color: #367CA5;
        }
        
        .alert {
             color: red;
             font-size: 4%;
             font-weight: bold;
             display: none;
        }
        
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col">
            <h1>NVS Exchange</h1>
            <h3>Emercoin: EMC to NVS</h3>

  <form method="POST">
    <div class="mb-3">
         <label 
          for="name"     
          class="form-label">
          Name:
         </label><br>
         
   <input 
    
    type="name" 
    class="form-control" 
    id="name" 
    name="name" 
    value="" 
    placeholder=" dns:ness.bts" 
    required>
                   
                   
      <div 
        id="nameHelp" 
        class="form-text">
        Enter Your Emercoin NVS Name
      </div>
   </div>

               
      <div class="mb-3">
         <label 
           for="value" 
           class="form-label">
           Value Hitory:
         </label><br>
         
   <textarea 
     name="value" 
     id="value" 
     class="form-control" 
     cols="25" 
     rows="6" 
     placeholder = "A=127.0.0.1|NS=seed1.emercoin.com,seed2.emercoin.com" 
     required>
   </textarea>
   
   
       <div 
         id="valueHelp" 
         class="form-text">
         Enter Your Emercoin NVS Value History</div>
     </div>
            
     <button 
       type="submit" 
       class="btn btn-primary">
       Create payment slot
     </button>
</form> <br>

<!-- Alert Messages are hidden in CSS by default -->

<?php if ('nvs' === $error): ?>

          <div 
            class="alert alert-danger"
            role="alert">
            NVS with the name
            <b><?=htmlentities($name)?></b>    
              Already exist.
          </div><br>


  <?php elseif ('db' === $error): ?>

     <div 
      class="alert alert-danger"
      role="alert">
      Slot with the name
            <b><?=htmlentities($name)?></b> 
            Already exist.
            <br>
            You can't pay it here!

            <a 
             href="/slot.php?slot=<?=$slot['slot_id']?>">
             <?=$slot['slot_id']?>
            </a>
      </div>

          <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
