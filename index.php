<?php

if (!empty($_GET['code'])):
    header("Access-Control-Allow-Origin: *");
    header("Cache-Control: no-cache");
    if ($_GET['code'] == 'e3sueck4k') {
        $urlEvents = 'https://calendar.google.com/calendar/ical/udidbi4ns794kt5eukbvtghbig%40group.calendar.google.com/public/basic.ics';
    } else {
        die();
    }
    $urlMoon = 'https://rot.lyna.info/rotator/preview/?type_id=1&city_id=303';
    $file = file_get_contents($urlMoon);
    preg_match("#\/([\d]{1,2})\.png#s", $file, $moon);
    $file = file_get_contents($urlEvents);
    preg_match_all("#START\D+:(\d+)#s", $file, $events);
    header("Access-Control-Allow-Origin: *");
    echo "moon" . $moon[1] . " " . join(" ", $events[1]);
else:?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Расписание</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <script src="widget/main.mini.js"></script>
        <link rel="stylesheet" href="widget/style.mini.css">
    </head>
    <body>
    <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter50634631 = new Ya.Metrika2({ id:50634631, clickmap:true, trackLinks:true, accurateTrackBounce:true, ut:"noindex" }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/tag.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks2"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/50634631?ut=noindex" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
    </body>
    </html>
<?endif;
