<?php

$params = [
    "e3sueck4k" => [
        "file" => "test.json",
        "api_key" => "f247e0e1-0692-4cdd-97ba-87720fe89586",
        "lat" =>"55.75387847762528",
        "lon" =>"37.619244952392535",
        "ical" => "udidbi4ns794kt5eukbvtghbig",
    ],
];

if (!empty($_GET['code'])) {
    if (array_key_exists($_GET['code'], $params)){
        $param = $params[$_GET['code']];
        $data = @json_decode( @file_get_contents($param["file"]) , true);
        if (!$data || $data["expire"] < time()) {
            $arMoonText = [
                "0" => "полнолуние",
                "1" => "убывающая луна",
                "2" => "убывающая луна",
                "3" => "убывающая луна",
                "4" => "последняя четверть",
                "5" => "убывающая луна",
                "6" => "убывающая луна",
                "7" => "убывающая луна",
                "8" => "новолуние",
                "9" => "растущая луна",
                "10" => "растущая луна",
                "11" => "растущая луна",
                "12" => "первая четверть",
                "13" => "растущая луна",
                "14" => "растущая луна",
                "15" => "растущая луна",
            ];
            $header = "X-Yandex-API-Key: {$param["api_key"]}";
            $url = "https://api.weather.yandex.ru/v2/informers?lat={$param["lat"]}&lon={$param["lon"]}";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [$header]);
            $source = json_decode(curl_exec($ch), true);
            curl_close($ch);

            $urlEvents = "https://calendar.google.com/calendar/ical/{$param["ical"]}%40group.calendar.google.com/public/basic.ics";
            $file = file_get_contents($urlEvents);
            preg_match_all("#START\D+:(\d+)#s", $file, $events);
            $data = [
                "events" => join(" ", $events[1]),
                "temp" => $source['fact']['temp'],
                "min" => 100,
                "max" => 0,
                "img" => $source['fact']['icon'],
                "moon_code" => $source['forecast']['moon_code'],
                "moon_text" => $arMoonText[$source['forecast']['moon_code']],
                // 50 запросов 24 часа = максимум 2 запроса в час ( 1 раз в 30 минут)
                "expire" => time() + (30 * 60),
            ];

            foreach ($source['forecast']['parts'] as $item) {
                $data['max'] = max($data['max'], $item['temp_max']);
                if (!empty($item['temp_min'])) {
                    $data['min'] = min($data['min'], $item['temp_min']);
                }
            }
            file_put_contents($param["file"], json_encode($data));
        }
        header("Access-Control-Allow-Origin: *");
        header("Cache-Control: no-cache");
        echo implode("|", $data);
    } elseif ($_GET["code"] == "isonline"){
        header("Access-Control-Allow-Origin: *");
        header("Cache-Control: no-cache");
    }
}else{?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Расписание</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <script src="widget/main.js"></script>
        <link rel="stylesheet" href="widget/style.css">
    </head>
    <body>
    <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter50634631 = new Ya.Metrika2({ id:50634631, clickmap:true, trackLinks:true, accurateTrackBounce:true, ut:"noindex" }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/tag.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks2"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/50634631?ut=noindex" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
    </body>
    </html>
<?php }