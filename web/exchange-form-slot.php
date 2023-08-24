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
            display: none;
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
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col">
      <h1>
         <a href="/">&lt;&lt;&lt; GO BACK</a>    
       </h1>
            
      <?php if ($active): ?>

      <?php if('generated' === $status): ?>

    <form method="POST">
        <input 
          type="text" 
          name="check" 
          value=""
          required 
          />
            <button 
              type="submit" 
              class="btn btn-primary">
              Confirm money send
             </button>
            </form>
            
            
            <!-- The below php code '$addr):' is rendering on the broswer which shouldn't -->

  <?php foreach ($slot['addr'] as $name => $addr): ?>
            
            
       <h2>
          <b><?= $addr['descr']?></b> Send 
          <?=  $addr['min_sum'] ?> 
          <?=$name?> to:
           <div class="d-flex justify-content-between align-items-center mb-3">
                    
                    
      <span class="text-line me-2">
           <?=  $addr['addr'] ?>
      </span> 
      
      <button onclick="copy('<?=  $addr['addr'] ?>','#copy_button_<?=$name?>')" 
           id="copy_button_<?=$name?>"
           class="btn btn-sm btn-success copy-button">
           Copy
      </button>
           </div>
      </h2>
            
            
    <?php endforeach; ?>

      <?php endif; ?>

        <h3>Address: 
         <?= htmlentities($slot['address']) ?>
        </h3>
            
            
        <h3>Payment address: 
        <?= nl2br(htmlentities($slot['pay_address'])) ?>
        </h3>
        

       <?php if('generated' === $status): ?>
         <div 
             class="alert alert-danger"
             role="alert">
             Transaction not confirmed yet!
         </div>

     <div class="float-start">
        <form method="POST"
              class="float-left">
           <input 
               type="text" 
               name="check" 
               value=""
               required 
               />
            <button 
              type="submit" 
              class="btn btn-primary">
              Confirm money send
            </button>
        </form>
     </div>

      <div class="float-end">
        <form method="POST"
             class="float-right">
           <input 
             type="text" 
             name="delete" 
             value=""
             required 
             />
          <button 
             type="submit" 
             class="btn btn-danger">
             Delete slot (!)
          </button>
        </form>
      </div>

     
   <?php elseif('payed' === $status): ?>

    <div class="float-start">
         <form method="GET">
             <input 
                 type="text" 
                 name="slot" 
                 value=""
                 required 
                 />
              <button 
                  btype="submit" 
                  class="btn btn-primary">
                  Reload
              </button>
         </form>
   </div>

                <br><br>
        <!-- Alert Messages Are Hidden in CSS by Default -->
                
    <div class="alert alert-success"
         role="alert">
          <b>Payment confirmed !</b><br>
          <b>NVS created !</b>
               <br><br>
     Waiting the exchange to accept the token<br>
      This can take up to 10 min...
    </div>

   
   
  <div class="float-start">
     <form method="GET">
         <input 
            type="text" 
            name="slot" 
            value=""
            required 
            />
          <button 
             type="submit" 
             class="btn btn-primary">
             Reload
          </button>
      </form>
  </div>

    
    <?php elseif('activated' === $status): ?>

   <div class="float-start">
        <form method="GET">
            <input 
                type="text" 
                name="slot" 
                value=""
                required 
                />
           <button 
               type="submit" 
               class="btn btn-primary">
               Reload
           </button>
        </form>
    </div>

                <br><br>

  <div class="alert alert-success" role="alert">
      <p>Your token is activated !</p>
  </div>
  
  
  <p style="color: grey;">Your have <b><?=$slot['hours']?></b> HOURS on <b><?=$slot['address']?></b> (v1)
  </p>
                
  <p style="color: grey;">Transmit any ammount (0.000001)<p>

    
    
   <div class="d-flex justify-content-between align-items-center mb-3">
       
       <span style="color: grey;"
          class="text-line me-2">
          From <b><?=$slot['address']?></b>  (v2)   </span> 
          <button 
            onclick="copy('<?=$slot['address']?>','#copy_button_<?=$slot['address']?>')" 
            id="copy_button_<?=$slot['address']?>" 
            class="btn btn-sm btn-success copy-button">
            Copy
          </button>
  </div>

   
   <div class="d-flex justify-content-between align-items-center mb-3">
      <span style="color: grey;" 
         class="text-line me-2">
         To <b><?=$slot['gen_address']?></b> (v2)  </span> 
         <button 
             onclick="copy('<?=  $slot['gen_address'] ?>','#copy_button_<?=  $slot['gen_address'] ?>')" 
             id="copy_button_<?=  $slot['gen_address'] ?>" 
             class="btn btn-sm btn-success copy-button">
             Copy
          </button>
    </div>

        <p style="color: grey;">and you will recieve <?=$slot['recieve']?> NESS on your address <b><?=$slot['pay_address']?></b> (v2) 
        </p>


   <div class="float-start">
      <form method="GET">
          <input 
             type="text" 
             name="slot" 
             value=""
             required 
             />
          <button 
              type="submit" 
              class="btn btn-primary">
              Reload
          </button>
       </form>
    </div>

                
    <?php elseif('done' === $status): ?>

     <div class="alert alert-success"
           role="alert">
         <p><b>Your token is payed !</b></p>
     </div>
                
                
     <p style="color: grey;">Your had <b><?=$slot['hours']?></b> HOURS on <b><?=$slot['address']?></b> (v1)
     </p>
           
     <p style="color: grey;">You have been payed !<?=$slot['recieve']?> NESS
     </p>
     
     
     <p style="color: grey;">Check your balance at <b><?=$slot['pay_address']?></b> (v2) 
     </p>

 
 
 <?php elseif('error' === $status): ?>

   <div class="alert alert-danger"
        role="alert">
                <?= nl2br(htmlentities($slot['error'])) ?>
   </div>

     <?php endif; ?>

       <?php else: ?>

  <div class="alert alert-danger"
       role="alert">
     Privateness V1 - V2 exchange found, but it is inactive (possible server failure) !
  </div>

                <?php endif; ?>
            </div>
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
