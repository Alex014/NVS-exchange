<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

require __DIR__ . '/../lib/Container.php';

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

$outer_address = isset($slot['address']) && $slots->isMyAddress($slot['address']);
$info = "";

if ('PAYED' === $slot['status']) {
    // Address check ...
    if ($outer_address) {
        $result = false;
        $error = false;
        $info = "The NVS record has been moved to outer wallet (address: $slot[address])";
    } else {
        $result = true;
    }
} elseif (isset($_POST["check"])) {
    $error = false;

    try {
        $result = $slots->processSlot($_GET['slot']);

        if( !$result ) {
            $error = "Transaction not confirmed yet !";
        }
    } catch (Exception $e) {
        $result = false;

        if (false !== strpos($e->getMessage(), 'pending operations')) {
            $error = 'There are pending operation on that name (' . $slot['name'] . ') '
                . ' <br/> This can be New name or edit name operation '
                . ' <br/> Try to wait 10 min';
        } elseif (false !== strpos($e->getMessage(), 'this name tx is not yours')) {
            $error = 'This NVS was sent to address which is outside this exchange'
                . ' <br/> You can\'t modify this record here';
        } else {
            $error = $e->getMessage();
        }
    }
} elseif (isset($_POST["delete"])) {
    $result = $slots->deleteSlot($_GET['slot']);
    header('location: /?msg=deleted&name=' . urlencode($slot['name']));
    die();
} else {
    // ...
}

$slot = $slots->showSlot($_GET['slot']);

$show_payments = ('GENERATED' === $slot['status']) || ('UPDATED' === $slot['status']);

$allow_edit = ('PAYED' === $slot['status']) && !$outer_address;

$allow_delete = empty($result) && ('GENERATED' === $slot['status']);

$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

            /* Matrix Palette */
            --matrix-green: #00ff41;
            --matrix-dark: #001100;
            --matrix-mid-green: #00bb00;

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
            max-width : 600px;
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

        h2, h3, .bookmark {
            font-size: 10pt;
        }

        .bookmark, #copy_url {
            color: #555;
            text-decoration: none;
        }

        .eddit-NVS {
            display: flex;
            text-align: center;
            justify-content: center;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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
            transform: translateY(-58%);
            color: #999;
            transition: var(--transition-smooth);
            pointer-events: none;
            background-color: var(--white);
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
            padding: 0 5% 0 5%;
        }

        .form-text {
            margin-top: -0.2rem;
            font-size: 0.7rem;
            color: var(--text-color);
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
        }

        .result {
            color: var(--accent-color);
            text-align: center;
            margin-top: 1rem;
        }

        /* Display Classes */
.money-send-conf-form,
.float-start,
.deleting-slot-form {
    display: none;
}

.display-payment-conf-nvs-created,
.display-money-send-conf-form,
.display-tx-not-conf-yet-msg,
.display-deleting-slot-form {
    display: block;
}

.tx-not-conf-yet-msg {
    color: var(--error-color);
    font-style: italic;
    font-weight: bold;
    display: none;
    padding-top: 20px;
}

.copy-button {
    height: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    background-color: var(--accent-color);
    border: none;
    color: var(--white);
    border-radius: 5px;
    padding: 8px 15px;
    cursor: pointer;
    transition: var(--transition-smooth);
    font-size: 12px;
}

.copy-button:hover {
    background-color: chartreuse;
    transform: translateY(-2px);
    box-shadow: var(--shadow-subtle);
}

.copy-button:active {
    background-color: lightgreen;
}

/* Delete Slot Button Styles */
.btn-danger {
    background-color: var(--error-color);
    border: none;
    color: white;
    border-radius: 5px;
    padding: 8px 15px;
    cursor: pointer;
    transition: var(--transition-smooth);
}

.btn-danger:hover {
    background-color: firebrick;
    transform: translateY(-2px);
    box-shadow: var(--shadow-subtle);
}

.btn-danger:active {
    background-color: lightcoral;
}

.payment-success-msg {
            color: #5cb85c;
            font-style: italic;
            font-weight: bold;
            display: none;
        }


.name,
.value {
            color: black;
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            overflow-wrap: break-word;
            white-space: normal;
        }

/* Tip Styles (for copy button) */
.tip {
    background-color: #263646;
    padding: 0 14px;
    line-height: 27px;
    position: absolute;
    border-radius: 4px;
    z-index: 100;
    color: #fff;
    font-size: 12pt;
    animation-name: tip;
    animation-duration: 0.6s;
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

.text-line {
            color: black;
            font-size: 10pt;
        }


.back-link {
    font-size: 14px;
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

    
    <!-- Primary SEO Meta Tags -->
    <title>
        Slot # <?= $slot['slot_id'] ?>
    </title>
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
</head>

<body>
    <div class="matrix-code" id="matrixCode"></div>

    <!-- Dark mode toggle icons -->
    <img class="toggle-icon" src="/img/dark-mode.png" alt="/img/dark-mode.png" >

    <div class="container">
        <div class="row">
            <div class="col">

                <h1>
                    <a href="/" class="back-link">
                        &lt;&lt;&lt; GO BACK
                    </a>
                </h1>

                <a href="javascript:void(0)" onclick="CreateBookmarkLink()" class="bookmark">
                    Bookmark page (press Ctrl+D to bookmark this page if link does not work)
                </a>
                <br /><br />
                <div id="copy_url">
                    <a href="javascript:void(0)" onclick="copy('<?= $actual_link ?>','#copy_url')" id="copy_url">
                        Copy link
                    </a>
                </div>
                <?php if (!isset($result) || (false === $result)): ?>

                    <?php foreach ($slot['addr'] as $name => $addr): ?>

                        <h2>
                            <b class="address">
                                <?= $addr['descr'] ?> 
                            </b>
                            SEND
                            <?= $addr['min_sum'] ?> 
                            <?= $name ?> TO: 
                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <span class="text-line me-2">
                                    <?= $addr['addr'] ?> 
                                </span>

                                <button onclick="copy('<?= $addr['addr'] ?>','#copy_button_<?= $name ?>')" id="copy_button_<?= $name ?>" class="btn btn-success copy-button">
                                    Copy
                                </button>
                            </div>
                        </h2>
                        

                    <?php endforeach; ?>

                <?php endif; ?>

                <?php if (isset($result) && $result && isset($slot['nvs'])): ?>
                    <h3>NAME: <br />
                        <span class="name">
                            <?= nl2br(htmlentities($slot['nvs']['name'])) ?> 
                        </span>
                    </h3>
                <?php else: ?>
                    <h3>NAME: <br />
                        <span class="name">
                            <?= htmlentities($slot['name']) ?> 
                        </span>
                    </h3>
                <?php endif; ?>

                <?php if (isset($result) && $result && isset($slot['nvs'])): ?>
                    <h3>VALUE: <br />
                        <span class="value">
                            <?= nl2br(htmlentities($slot['value'])) ?> 
                        </span>
                    </h3>
                <?php else: ?>
                    <h3>VALUE: <br />
                        <span class="value">
                            <?= nl2br(htmlentities($slot['value'])) ?> 
                        </span>
                    </h3>
                <?php endif; ?>

                <?php if (isset($result) && $result && isset($slot['nvs'])): ?>
                    <h3>Expires in: <br />
                        <span class="value">
                            <?= round($slot['nvs']['expires_in'] / 144) ?> days
                        </span>
                    </h3>
                <?php else: ?>
                    <h3>DAYS: <br />
                        <span class="value">
                            + <?= nl2br(htmlentities($slot['addr']['EMC']['days'])) ?> 
                        </span>
                    </h3>
                <?php endif; ?>

                <div class="payment-success-msg <?php if (isset($result) && $result): ?>display-payment-conf-nvs-created<?php endif; ?>" role="alert">
                    Payment confirmed !
                    <br>
                    Name-Value record created or updated !
                </div>

                <form method="POST" class="money-send-conf-form <?php if ((!isset($result) || (false === $result)) && !$outer_address): ?>display-money-send-conf-form<?php endif; ?>">
                    <input type="hidden" name="check" value="" />

                    <button type="submit" class="btn btn-primary">
                        Confirm money send
                    </button>
                </form>

                <?php if (isset($result) && (false === $result) && (false !== $error)): ?>
                    <code style="color: red;">
                        <br/>
                        <?= $error ?>
                    </code>
                <?php endif; ?>

                <div class="tx-not-conf-yet-msg <?php if (isset($result) && (false === $result) && (false === $error)): ?>display-tx-not-conf-yet-msg<?php endif; ?>" role="alert">
                    <?= $info ?>
                </div>

                <br>

                <div class="deleting-slot-form <?php if ($allow_delete): ?>display-deleting-slot-form<?php endif; ?>">
                    <form method="POST" class="float-right">
                        <input type="hidden" name="delete" value="" />
                        <button type="submit" class="btn btn-danger">
                            Delete slot (!)
                        </button>
                    </form>
                </div>

                <?php if ($allow_edit): ?>
                    <a class="btn btn-primary eddit-NVS" href="/edit.php?slot=<?= $slot['slot_id'] ?>">
                        Edit Name-Value Record
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

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
            document.body.removeChild(input);
            return result;
        }

        function CreateBookmarkLink() {
            title = "NVS exchange";
            //or title = document.title
            url = "<?= $actual_link ?>";
            //or url = location.href

            if (window.sidebar) { // Mozilla Firefox Bookmark
                window.sidebar.addPanel(title, url, "");
            } else if (window.external && window.external.AddFavorite) { // IE Favorite
                window.external.AddFavorite(url, title);
            }
        }

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>

    <!-- jQuery script for light/dark mode toggle -->
    <script src="/js/darkmode.js"></script>

    <?php if (!isset($_COOKIE['darkmode']) || (1 == $_COOKIE['darkmode'])): ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".toggle-icon").trigger("click");
            });
        </script>
    <?php endif; ?>
    
</body>
</html>