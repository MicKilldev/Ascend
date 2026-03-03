<?php
// Flash message display — shown once after login, then cleared
if (!empty($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
    echo '
    <div id="ascend-toast" style="
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border: 1px solid rgba(0, 210, 255, 0.3);
        border-left: 4px solid #00d2ff;
        color: #f8fafc;
        padding: 18px 22px 18px 18px;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(0,210,255,0.1);
        font-family: Outfit, sans-serif;
        font-size: 0.92rem;
        min-width: 300px;
        max-width: 380px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        animation: ascendSlideIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    ">
        <span style="font-size: 1.5rem; margin-top: 1px;">✅</span>
        <div style="flex:1;">
            <div style="font-weight: 700; font-size: 0.88rem; color: #00d2ff; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Login Successful</div>
            <div style="color: rgba(248,250,252,0.85); line-height: 1.5;">' . $flash . '</div>
        </div>
        <button onclick="document.getElementById(\'ascend-toast\').remove()" style="
            background: none; border: none; color: rgba(255,255,255,0.35);
            cursor: pointer; font-size: 1.1rem; padding: 0; margin-top: 2px;
            transition: color 0.2s;
        " onmouseover="this.style.color=\'#f87171\'" onmouseout="this.style.color=\'rgba(255,255,255,0.35)\'">✕</button>
    </div>
    <style>
        @keyframes ascendSlideIn {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0);    }
        }
        @keyframes ascendSlideOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(30px); }
        }
    </style>
    <script>
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            var t = document.getElementById("ascend-toast");
            if (t) {
                t.style.animation = "ascendSlideOut 0.4s ease-in forwards";
                setTimeout(function() { if(t) t.remove(); }, 400);
            }
        }, 5000);
    </script>
    ';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>

<body>