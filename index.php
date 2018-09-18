<?
header("Access-Control-Allow-Origin: *");
if($_GET['code'] == 'isonline'){

}
else{
    $urlMoon = 'https://rot.lyna.info/rotator/preview/?type_id=1&city_id=303';
    $urlEvents='https://calendar.google.com/calendar/ical/udidbi4ns794kt5eukbvtghbig%40group.calendar.google.com/public/basic.ics';
    $file = file_get_contents($urlMoon);
    preg_match("#\/([\d]{1,2})\.png#s", $file, $moon);
    $file = file_get_contents($urlEvents);
    preg_match_all("#START:(\d+)#s",$file,$events);
    echo "moon".$moon[1]." ".join(" ",$events[1]);
}