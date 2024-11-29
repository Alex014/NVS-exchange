<?php
ini_set('display_errors', true);
require __DIR__ . '/../lib/Container.php';

use lib\Container;

$error = false;

$config = require __DIR__ . '/../config/config.php';
$fdb = $config['db'];

if (empty($_GET['slot'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

$slots = Container::createSlots();

$slot = $slots->showSlot($_GET['slot']);
$slot_id = $slot['slot_id'];

if (empty($slot)) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

if ('PAYED' !== $slot['status']) {
    header('location: /slot.php?slot=' . $slot_id);
}

if (!empty($_POST['value']) && !empty($_POST['days'])) {
    $name = $slot['name'];
    $value = $_POST['value'];
    $address = $_POST['address'];
    $days = (int)$_POST['days'];

    $slots->updateSlot($slot_id, $name, $value, $address, $days);
    header('location: /slot.php?slot=' . $slot_id);
} else {
    $name = $slot['name'];
    $value = $slot['value'];
    $address = $slot['address'];
    $days = 100;
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

    <!-- Custom CSS for appealing UI -->
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
        
        /* Back Link Styles */
        .back-link {
            display: block;
            text-align: left;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }

       div h3 {
            font-size: 12pt;
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
            transform: translateY(-50%);
            color: #999;
            transition: var(--transition-smooth);
            pointer-events: none;
            border-radius: 0;
            padding: 0px 6px 10px 6px;
        }
        
        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(54, 124, 165, 0.1);
        }
        
        .form-input:focus + .form-label,
        .form-input:not(:placeholder-shown) + .form-label {
            top: -11px;
            left: 1px;
            font-size: 12px;
            color: var(--primary-color);
            background-color: var(--white);
            padding: 0 5% 0 5%;
        }
        
        .form-text {
            margin-top: -0.2rem;
            font-size: 0.7rem;
            color: var(--text-color);
        }
        
        /* Name and Value Styles */
        .name-section {
            margin-bottom: 1.5rem;
        }
        
        .name {
            font-family: 'Courier New', monospace;
            color: var(--secondary-color);
            word-break: break-all;
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

        .nvs-show-alert,
        .db-show-alert {
            display: block;
        }
        
        .result {
            color: var(--accent-color);
            text-align: center;
            margin-top: 1rem;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
                width: calc(100% - 2rem);
                border-radius: 12px;
            }
        }
            </style>

    <link href="/css/darkmode.css" rel="stylesheet" />
</head>

<body>
    <div class="matrix-code" id="matrixCode"></div>
    <img class="toggle-icon" src="/img/dark-mode.png" alt="Dark Mode Toggle">

    <div class="container">
        <a href="/slot.php?slot=<?= $slot['slot_id'] ?>" class="back-link">&lt;&lt;&lt; GO BACK</a>

        <div class="name-section">
            <h3>NAME:</h3>
            <span class="name"><?= htmlentities($slot['name']) ?></span>
        </div>

        <form method="POST">
            <div class="form-group">
                <input type="number" class="form-input" id="days" name="days" value="<?= $days ?>" min="100" placeholder=" " required>
                <label for="days" class="form-label">Days</label>
                <div class="form-text">Days (amount of days to store your NVS Record)</div>
            </div>

            <div class="form-group">
                <input type="text" class="form-input" id="address" name="address" value="<?= $address ?>" placeholder=" ">
                <label for="address" class="form-label">Address (optional)</label>
                <div class="form-text">Enter your emercoin address (if you want to export this NVS to outer wallet)</div>
            </div>

            <div class="form-group">
                <textarea name="value" id="value" class="form-input" cols="25" rows="6" placeholder=" " required><?= $value ?></textarea>
                <label for="value" class="form-label">Value (NS=ns1.emercoin.com,ns2.emercoin.com)</label>
                <div class="form-text">Enter Your Emercoin NVS Value History</div>
            </div>

            <button type="submit" class="btn-primary">Update NVS Record</button>
        </form>
        <!-- Alert Messages are hidden in CSS by default -->

        <?php if ('nvs' === $error) : ?>

<div class="alert alert-danger
<?php if ('nvs' === $error)
    echo 'nvs-show-alert'; ?>" role="alert">

    NVS with the name
    <b><?= htmlentities($name) ?></b>
    Already exists.
</div><br>

<?php elseif ('db' === $error) : ?>

<div class="alert alert-danger 
<?php if ('db' === $error) echo 'db-show-alert'; ?>" role="alert">

    Slot with the name
    <b><?= htmlentities($name) ?></b>
    Already exists.
    <br>
    You can't pay it here!

    <a href="/slot.php?slot=<?= $slot['slot_id'] ?>">
        <?= $slot['slot_id'] ?>
    </a>
</div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && ('deleted' === $_GET['msg'])) : ?>

<?php
$name = '';

if (isset($_GET['name'])) {
    $name = $_GET['name'];
}
?>

<h3 class='result'>
    Name-Value record <code><?= htmlentities($name) ?></code> deleted
</h3>

<?php endif; ?>

</div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="/js/darkmode.js"></script>

    <?php if (!isset($_COOKIE['darkmode']) || (1 == $_COOKIE['darkmode'])): ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".toggle-icon").trigger("click");
            });

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
    <?php endif; ?>
</body>
</html>