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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <title>Slot # <?= $slot['id'] ?></title>
    <meta name="description" content="Secure, decentralized Name-Value Storage solution on Emercoin. Protect and manage your data with advanced blockchain technology. Easy, private, and reliable.">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://nvs.ness.cx/">
    <meta property="og:title" content="NVS: Decentralized Name-Value Storage">
    <meta property="og:description" content="Secure your data with Emercoin's advanced Name-Value Storage. Private, decentralized, and user-friendly blockchain solution.">
    <meta property="og:image" content="https://nvs.ness.nx/social-share-image.jpg">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://nvs.ness.cx/">
    <meta name="twitter:title" content="NVS: Secure Decentralized Data Storage">
    <meta name="twitter:description" content="Protect your data with Emercoin's Name-Value Storage. Blockchain-powered, private, and secure.">
    <meta name="twitter:image" content="https://nvs.ness.cx/social-share-image.jpg">
    
    <!-- Geo Tags -->
    <meta name="geo.region" content="Global">
    <meta name="geo.position" content="0;0">
    <meta name="ICBM" content="0, 0">
    
    <!-- Robots and Crawling -->
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    
    <!-- Canonical Link -->
    <link rel="canonical" href="https://nvs.ness.cx/">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="keywords" content="NVS, Name-Value Storage, Emercoin, Blockchain, Decentralized Storage, Privacy, Secure Data, Cryptocurrency">
    
    <!-- Language and Localization -->
    <meta http-equiv="content-language" content="en-US">
    
    <!-- Verification Tags (examples, replace with your actual verification codes) -->
    <meta name="google-site-verification" content="your_google_verification_code">
    <meta name="msvalidate.01" content="your_bing_verification_code">
    
    <!-- Favicon and App Icons -->
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <!-- Structured Data / JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebApplication",
      "name": "NVS - Name-Value Storage",
      "url": "https://yourapp.com",
      "description": "Secure decentralized Name-Value Storage solution powered by Emercoin",
      "applicationCategory": "Blockchain Storage",
      "operatingSystem": "Web-based",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
      },
      "publisher": {
        "@type": "Community",
        "name": "PrivateNess Network",
        "logo": "https://nvs.ness.cx/logo.png"
      }
    }
</script>
    <style>

/* Modern Reset and Base Styles */
:root {
    /* Color Palette */
    --primary-color: #367CA5;
    --secondary-color: #1B4F73;
    --accent-color: #4CAF50;
    --error-color: #FF5A5A;
    --background-color: #f4f4f4;
    --text-color: #333;
    --white: #FFFFFF;
    --light-gray: #f9f9f9;
    --border-color: #e0e0e0;

    /* Typography */
    --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    
    /* Shadows and Transitions */
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
    font-family: 'Courier New', monospace;
    background-color: var(--primary-color);
    line-height: 1.6;
    color: var(--text-color);
}

h1 {
    font-size: 14px;
}

h2, h3 {
    font-size: 12pt;
    color: #555;
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

/* Modern Float Label Input */
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
    transform: translateY(-50%);
    color: #999;
    transition: var(--transition-smooth);
    pointer-events: none;
    /*background-color: var(--white);*/
    border-radius: 0;
    padding: 0px 6px 10px 6px;
}

.form-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(54, 124, 165, 0.1);
}

.form-input:focus + .form-label,
.form-input:not(:placeholder-shown) + .form-label {
    top: -10px;
    left: 1px;
    font-size: 12px;
    color: var(--primary-color);
}

/* Button Styles */
.btn {
    display: inline-block;
    padding: 12px 20px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: var(--transition-smooth);
    font-size: 14px;
    font-family: 'Courier New', monospace;
}

.btn-primary {
    background-color: var(--primary-color);
    color: var(--white);
}

.btn-primary:hover {
    background-color: var(--secondary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-subtle);
}

.btn-success {
    background-color: var(--accent-color);
    color: var(--white);
}

.btn-success:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.btn-danger {
    background-color: var(--error-color);
    color: var(--white);
}

.btn-danger:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

/* Address and Copy Styles */
.address-container {
    background-color: var(--light-gray);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.address-text {
    font-size: 14px;
    color: var(--text-color);
    overflow-wrap: break-word;
    word-break: break-all;
}

.copy-btn {
    background-color: transparent;
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
}

.copy-btn:hover {
    background-color: var(--primary-color);
    color: var(--white);
}

/* Alert Styles */
.alert {
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-size: 14px;
}

.alert-success {
    background-color: rgba(76, 175, 80, 0.1);
    color: var(--accent-color);
    border: 1px solid var(--accent-color);
}

.alert-danger {
    background-color: rgba(255, 90, 90, 0.1);
    color: var(--error-color);
    border: 1px solid var(--error-color);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .container {
        margin: 1rem;
        padding: 1.5rem;
        width: calc(100% - 2rem);
        border-radius: 12px;
    }

    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .address-container {
        flex-direction: column;
        align-items: flex-start;
    }

    .copy-btn {
        margin-top: 10px;
        width: 100%;
    }
}

/* Animation for Copy Tip */
@keyframes fadeInOut {
    0%, 100% { opacity: 0; }
    10%, 90% { opacity: 1; }
}

.copy-tip {
    position: absolute;
    bottom: -40px;
    left: 50%;
    transform: translateX(-50%);
    background-color: var(--text-color);
    color: var(--white);
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    animation: fadeInOut 2s;
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
    <div class="matrix-code" id="matrixCode"></div>
<div class="container">
    <div class="row">
        <div class="col">
            <h1><a href="/">&lt;&lt;&lt; GO BACK</a></h1> <br>
            
            <?php if ($active): ?>
    <?php if ('generated' === $status): ?>
        <!-- Form for confirming money send -->
        <form method="POST" class="active-and-generated-conf-money-send-form">
            <div class="form-group">
                <input type="text" id="confirmation" class="form-input" placeholder=" " required />
                <label for="confirmation" class="form-label">Enter Confirmation</label>
            </div>
            <button type="submit" class="btn btn-primary">Confirm Money Send</button>
        </form>
        
        <!-- Loop through addresses to send money to -->
        <?php foreach ($slot['addr'] as $name => $addr): ?>
            <h2 class="h2 <?php if ($active && 'generated' === $status): ?>h2-shown<?php endif; ?>">
                <b class="looping-address"><?= $addr['descr'] ?></b> SEND <b class="send-min"><?= $addr['min_sum'] ?></b> <?= $name ?> TO:
                <div class="d-flex mb-3">
                    <span class="text-line me-2 address-text"><?= htmlentities($addr['addr']) ?></span>
                    <button 
                        onclick="copy('<?= htmlentities($addr['addr']) ?>','#copy_button_<?= $name ?>')" 
                        id="copy_button_<?= $name ?>" class="btn btn-sm btn-success copy-button">
                        Copy
                    </button>
                    <div class="qr-code-container">
                        <canvas id="qr-code-<?= $name ?>" width="100" height="100"></canvas>
                        <span class="qr-code-label"><?= $name ?></span>
                    </div>
                </div>
            </h2>

            <script>
                $(document).ready(function() {
                    $('#qr-code-<?= $name ?>').qrcode({
                        text: "<?= htmlentities($addr['addr']) ?>",
                        width: 100,
                        height: 100
                    });
                });
            </script>
        <?php endforeach; ?>
    <?php endif; ?>
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
            <div class="form-section float-start">
                <form method="POST" 
                    class="conf-money-send-and-deleting-slot-form <?php if ('generated' === $status): ?>display-conf-money-send-and-deleting-slot-form<?php endif; ?>">
                    <div class="form-group">
                        <input 
                            type="text" 
                            id="confirmationInput"
                            name="check" 
                            class="form-input" 
                            placeholder=" " 
                            required 
                        />
                        <label for="confirmationInput" class="form-label">Enter Confirmation</label>
                    </div>
                    <button 
                        type="submit" 
                        class="btn btn-primary">
                        Confirm Money Send
                    </button>
                </form>
            </div>
            <div class="form-section">
                <form method="POST" 
                    class="delete-slot-form <?php if ('generated' === $status): ?>display-delete-slot-form<?php endif; ?>">
                    <div class="form-group">
                        <input 
                            type="text" 
                            id="deleteConfirmationInput"
                            name="delete" 
                            class="form-input" 
                            placeholder=" " 
                            required 
                        />
                        <label for="deleteConfirmationInput" class="form-label">Enter Slot to Delete</label>
                    </div>
                    <button 
                        type="submit" 
                        class="btn btn-danger">
                        Delete Slot (!)
                    </button>
                </form>
            </div>
            
            <?php elseif ('payed' === $status): ?>
            
            <!-- Form for reloading the page -->
            <div class="form-section">
                <form method="GET" 
                    class="payed-reload-form <?php if ('payed' === $status): ?>display-payed-reload-form<?php endif; ?>">
                    <div class="form-group">
                        <input 
                            type="text" 
                            id="reloadSlotInput"
                            name="slot" 
                            class="form-input" 
                            placeholder=" " 
                            required 
                        />
                        <label for="reloadSlotInput" class="form-label">Press Reload Button</label>
                    </div>
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
            
            <div class="form-section">
                <form method="GET" 
                    class="payed-reload-form <?php if ('payed' === $status): ?>display-payed-reload-form<?php endif; ?>">
                    <div class="form-group">
                        <input 
                            type="text" 
                            id="reloadSlotInput"
                            name="slot" 
                            class="form-input" 
                            placeholder=" " 
                            required 
                        />
                        <label for="reloadSlotInput" class="form-label">Press Reload Button</label>
                    </div>
                    <button 
                        type="submit" 
                        class="btn btn-primary">
                        Reload
                    </button>
                </form>
            </div>
  
  
  <?php elseif ('activated' === $status): ?>
        
        <!-- Form for reloading the page -->
        <div class="form-section">
            <form method="GET" 
                class="token-activation-reload-form <?php if ('activated' === $status): ?>display-token-activation-reload-form<?php endif; ?>">
                <div class="form-group">
                    <input 
                        type="text" 
                        id="tokenActivationReloadInput"
                        name="slot" 
                        class="form-input" 
                        placeholder=" " 
                        required 
                    />
                    <label for="tokenActivationReloadInput" class="form-label">Press Reload Button</label>
                </div>
                <button 
                    type="submit" 
                    class="btn btn-primary">
                    Reload
                </button>
            </form>
        </div>
        
        <br><br>
        
        <!-- Alert for activated token -->
        <div 
         class="alert alert-success <?php if ('activated' === $status): ?>display-token-activation-success-msg<?php endif; ?>" 
         role="alert">
            <p>Your token is activated !</p>
        </div>
        
        <p style="color: grey; font-family: 'Courier New', monospace; font-size: 12pt" 
         class="token-activated-token-info <?php if ('activated' === $status): ?>display-token-activated-token-info<?php endif; ?>">You have <b><?= $slot['hours'] ?></b> HOURS on <b><?= $slot['address'] ?></b> (v1)</p>
         
        <p style="color: grey; font-family: 'Courier New', monospace;" 
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
        
        <p style="color: grey; font-family: 'Courier New', monospace; font-size: 12pt" 
        class="ness-to-receive-info <?php if ('activated' === $status): ?>display-ness-to-receive-info<?php endif; ?>">You will receive <?= $slot['recieve'] ?> NESS on your address <b><?= $slot['pay_address'] ?></b> (v2)</p>
        
        <!-- Form for reloading the page -->
        <div class="form-section">
            <form method="GET" 
                class="ness-to-receive-reload-form <?php if ('activated' === $status): ?>display-ness-to-receive-reload-form<?php endif; ?>">
                <div class="form-group">
                    <input 
                        type="text" 
                        id="nessToReceiveReloadInput"
                        name="slot" 
                        class="form-input" 
                        placeholder=" " 
                        required 
                    />
                    <label for="nessToReceiveReloadInput" class="form-label">Press Reload Button</label>
                </div>
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
        
        <p style="color: grey; font-family: 'Courier New', monospace; font-size: 12pt" 
        class="hours-payment-success-hours-info <?php if ('done' === $status): ?>display-hours-payment-success-hours-info<?php endif; ?>">You had <b><?= $slot['hours'] ?></b> HOURS on <b><?= $slot['address'] ?></b> (v1)
        </p>
        
        <p style="color: grey; font-family: 'Courier New', monospace; font-size: 12pt" 
        class="ness-amount-received-info <?php if ('done' === $status): ?>display-ness-amount-received-info<?php endif; ?>">You have been paid <?= $slot['recieve'] ?>
         NESS
        </p>
        
        <p style="color: grey; font-family: 'Courier New', monospace; font-size: 12pt;"
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
        };

        function createMatrixRain() {
            const codeContainer = document.getElementById('matrixCode');
            
            for (let i = 0; i < 100; i++) {
                const span = document.createElement('span');
                span.textContent = Math.random() > 0.5 ? '0' : '1';
                span.style.left = `${Math.random() * 100}%`;
                span.style.animationDuration = `${Math.random() * 10 + 5}s`;
                span.style.opacity = `${Math.random() * 0.3}`;
                codeContainer.appendChild(span);
            }
        }

        createMatrixRain();
    </script>
</body>
</html>
