<?php
ini_set('display_errors', true); // For development only; turn off in production
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
            header('Location: /exchange-form-slot.php?slot=' . $slot_id);
            exit; // Ensure no further code is executed after a redirect
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
        /* Modern Reset and Base Styles */
        :root {
            --primary-color: #367CA5;
            --secondary-color: #1B4F73;
            --accent-color: #4CAF50;
            --error-color: #FF5A5A;
            --background-color: #f4f4f4;
            --text-color: #333;
            --white: #FFFFFF;
            --light-gray: #f9f9f9;
            --border-color: #e0e0e0;
        
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            
            --shadow-subtle: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-medium: 0 10px 25px rgba(0,0,0,0.15);
            --transition-smooth: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: var(--font-primary);
            background-color: var(--primary-color);
            line-height: 1.6;
            color: var(--text-color);
        }
        
        /* Container Styles */
        .container {
            max-width: 600px;
            margin: 2rem auto;
            background-color: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow-medium);
            padding: 2.5rem;
            position: relative;
        }
        
        /* Header Styles */
        .header {
            background-color: var(--secondary-color);
            color: var(--white);
            text-align: center;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .header h1 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .header h3 {
            font-size: 0.9rem;
            font-weight: normal;
        }
        
 /* Navigation Styles */
        .nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            background-color: var(--light-gray);
            padding: 1rem;
            border-radius: 10px;
        }
        
        .nav-link {
            text-decoration: none;
            color: var(--text-color);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: var(--transition-smooth);
        }
        
        .nav-link:hover {
            background-color: rgba(54, 124, 165, 0.1);
        }
        
        .nav-link.active {
            font-weight: bold;
            color: var(--primary-color);
        }
        
        /* Form Group Styles */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            outline: none;
            transition: var(--transition-smooth);
            font-size: 16px;
        }
        
        .form-label {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-40%);
            color: #999;
            transition: var(--transition-smooth);
            pointer-events: none;
            background-color: var(--white);
            border-radius: 5px;
            padding: 0 6px;
        }
        
        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(54, 124, 165, 0.1);
        }
        
        .form-input:focus + .form-label,
        .form-input:not(:placeholder-shown) + .form-label {
            top: -12px;
            left: 1px;
            font-size: 12px;
            background-color: var(--white);
            color: var(--primary-color);
            padding: 0 5% 0 5%;
        }
        
        .form-text {
            margin-top: -0.2rem;
            font-size: 0.7rem;
            color: var(--text-color);
        }
        
        /* Alert Styles */
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            background-color: rgba(255, 90, 90, 0.1);
            color: var(--error-color);
            border: 1px solid var(--error-color);
            display: none;
        }

        .nvs-show-alert, .db-show-alert, .server-failure {
            display: block;
        }
        
        /* Button Styles */
        .btn-primary {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-subtle);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
                width: calc(100% - 2rem);
                border-radius: 12px;
            }
        
            .nav {
                flex-direction: column;
                align-items: stretch;
            }
        
            .nav-link {
                text-align: center;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>NVS Exchange</h1>
            <h3>EMC, NESS, NCH to Emercoin NVS</h3>
        </div>
    
        <nav class="nav">
            <a href="/" class="nav-link">BUY Name-Value record</a>
            <a href="#" class="nav-link active">Privateness V1 - V2 exchange</a>
        </nav>
    
        <?php if ($active): ?>
        <form method="POST">
            <div class="form-group">
                <input 
                    type="text" 
                    id="address" 
 name="address" 
                    class="form-input" 
                    placeholder=" " 
                    required
                >
                <label for="address" class="form-label">Your Token Address</label>
                <div class="form-text">The address in privateness v1 network, where you have coin-hours.</div>
            </div>
    
            <div class="form-group">
                <input 
                    type="text" 
                    id="pay_address" 
                    name="pay_address" 
                    class="form-input" 
                    placeholder=" " 
                    required
                >
                <label for="pay_address" class="form-label">Your Pay_Address Payment Address</label>
                <div class="form-text">The address in privateness v2 network, to receive coins.</div>
            </div>
            <!-- Alert Messages Are Hidden in CSS By Default -->
                    
            <?php if ('nvs' === $error): ?>
                <div class="alert <?php if ('nvs' === $error) echo 'nvs-show-alert'; ?>" role="alert">
                    NVS record with address <?= htmlspecialchars($address) ?> and payment address <?= htmlspecialchars($pay_address) ?> already exists.
                </div>
            <?php elseif ('db' === $error): ?>
                <div class="alert <?php if ('db' === $error) echo 'db-show-alert'; ?>" role="alert">
                    Slot with address <?= htmlspecialchars($address) ?> and payment address <?= htmlspecialchars($pay_address) ?> already exists.<br>
                    You can't pay it here 
                    <a href="/slot.php?slot=<?= htmlspecialchars($slot_id) ?>"><?= htmlspecialchars($slot['name']) ?></a>
                </div>
            <?php else: ?> 
                <div class="alert <?php if (!$error) echo 'server-failure'; ?>" role="alert"> 
                    Privateness V1 - V2 exchange found, but it is inactive (possible server failure)!
                </div>
            <?php endif; ?>
    
            <button type="submit" class="btn-primary">Create Payment Slot</button>
        </form>
    </div>
</body>
</html>