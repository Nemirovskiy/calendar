<?
if(!empty($_GET['code'])):
    header("Access-Control-Allow-Origin: *");
    header("Cache-Control: no-cache");
    if ($_GET['code'] == 'e3sueck4k'){
        $urlMoon = 'https://rot.lyna.info/rotator/preview/?type_id=1&city_id=303';
        $urlEvents='https://calendar.google.com/calendar/ical/udidbi4ns794kt5eukbvtghbig%40group.calendar.google.com/public/basic.ics';
        $file = file_get_contents($urlMoon);
        preg_match("#\/([\d]{1,2})\.png#s", $file, $moon);
        $file = file_get_contents($urlEvents);
        preg_match_all("#START:(\d+)#s",$file,$events);
        echo "moon".$moon[1]." ".join(" ",$events[1]);
    }
    else
        die();
else:?>
<!DOCTYPE html>
<html>
<head>
    <title>Расписание</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="main.js"></script>
    <link rel="widget/stylesheet" href="widget/style.css">
</head>
<body>
</body>
</html>

<?endif;?>