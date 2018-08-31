<?
function ShowMon($event)
{
    $max_day = date("t", mktime(0, 0, 0, $mon, 1, $year));
    $w_day = date("w", mktime(0, 0, 0, date('n')));
    $arr_day_name = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
    if ($w_day == 0) $w_day = 7;//если воскресенье - то 7
    for ($i = 1, $x = 1; $i <= 35; $i++) {
        $day = date('j', mktime(0, 0, 0, date('n'), date('j') - $w_day + $i));
        $mon = date('n', mktime(0, 0, 0, date('n'), date('j') - $w_day + $i));
        echo "<td align='center' class='";
        if (($mon == date('n')) and ($day == date('j'))) //если день и месяц соотв сегодня - помечаем
        {
            echo " today ";
        }
        if ($mon < date("n")) echo "aftermon"; //  не текущий месяц помечаем
        if ($mon > date("n")) echo "beforemon"; //  не текущий месяц помечаем
        echo "'><div class='";
        foreach ($event as $item) {
            ///////// разбираем дату по частям на месяц и день, а если первый 0, от отрезаем его
            $item_mon = substr($item, 4, 2);
            if (substr($item_mon, 0, 1) == 0) {
                $item_mon = substr($item_mon, 1, 1);
            }
            $item_day = substr($item, 6, 2);
            if (substr($item_day, 0, 1) == 0) {
                $item_day = substr($item_day, 1, 1);
            }
            if ($mon == $item_mon and $day == $item_day)//если день и месяц равен item то помечаем его 
            {
                echo ' active ';
            }
        }
        echo " '>";
        if ($x >= 6) echo "<b> $day</b>";
        else echo $day;
        echo "</div></td>";
        $x++;//счетчик дней недели, если было 7 - то конец строки и сбросить счетчик
        if ($x == 8) {
            echo "</tr><tr>";
            $x = 1;
        }
    }
}

$arr_mon_name = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
$arr_day_name = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];

$ar_moon = file('https://www.life-moon.pp.ru/');
foreach ($ar_moon as $item) {
    $t = preg_match('#static/img/moon_d([0-9]+).gif#', $item, $arr_item);//текущяя картинка луны
    if ($t) {
        $url_moon = 'https://www.life-moon.pp.ru/' . $arr_item[0];
        $day_moon = $arr_item[1];
    }
}

$arr = file('https://calendar.google.com/calendar/ical/udidbi4ns794kt5eukbvtghbig%40group.calendar.google.com/public/basic.ics');
foreach($arr as $item)
{
    if(substr($item,0,7)=='DTSTART')
    {
        $date = substr($item,8,10);
        if(substr($date,0,4) == date("Y")) $arr_event[]=$date;
    }
}
?>
<!DOCTYPE>
<html>
<head>
    <title>Календарь</title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta content="text/html; charset=utf-8">
    <script type="text/javascript">
        //скрипт часов
        function digitalWatch() {
            var date = new Date();
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();
            if (hours < 10) hours = "0" + hours;
            if (minutes < 10) minutes = "0" + minutes;
            if (seconds < 10) seconds = "0" + seconds;
            document.getElementById("digital_watch").innerHTML = hours + ":" + minutes + ":" + seconds;
            setTimeout("digitalWatch()", 1000);
        }
    </script>
</head>
<body onload="digitalWatch()">
<table align="left" border="0" cellspacing="0" cellpadding="0" width="300px">
    <tr align="left" valign="top">
        <th colspan="7"><h1 id="digital_watch" ></h1><?//часы?></th>
    </tr>
    <tr align="left" valign="top">
        <td colspan="5">
            <h1 class="date1">
                <div class="moon" title="<?=$day_moon?> лунный день">
                    <div class="ring"></div>
                </div><?echo date('j');?>
            </h1>
            <h1 class="date2"><?echo $arr_mon_name[date('n')-1];?></h1>
            <h1 class="date3"><?echo $arr_day_name[date('w')];?></h1>
        </td>
        <th colspan="2">
            <div class="weater" title="Обновлено:  <?="\n".date('j.n.y G:i')?>">
                <div class="quadr"></div>
            </div>
        </th>
    </tr>
    <tr align="center" valign="top">
        <?
        $arr_w = array('ПН','ВТ','СР','ЧТ','ПТ','СБ','ВС');
        foreach($arr_w as $w_item)
        {
            echo "<th> $w_item </th>";}
        ?>
    </tr>
    <tr align="center" valign="top">
        <? ShowMon($arr_event);?>
    </tr>
</table>
<style>
    * {
        margin: 0;
        padding: 0;
        text-align: center;    font-family: 'MS Sans Serif', Geneva, sans-serif;;
    }
    td {
        padding: 2px;width: 40px;
    }
    .today {

        padding: 0px;
    }
    .today div {
        border: 6px solid #02e002;
        margin: -4px;position: relative;
    }
    .aftermon {
        background: #ccc;
        color: #fff;
    }
    .active {
        background: red;
    }
    .aftermon .active, .beforemon .active {
        background: #f99;
    }
    .beforemon {
        background: #f5f5f5;
        color: #9a9a9a;
    }
    .date2 {
        font-size: 25px;
    }
    .date3 {
        font-size: 25px;
        font-weight: normal;
    }
    .weater, .quadr{
        width: 80px;
        height: 80px;    margin: 10px 0px;
    }
    .weater{
        background: #ececec url(https://info.weather.yandex.net/2/4.ru.png?domain=ru) -35px -31px;
    }
    .quadr{
        background: url(quadr.png) 0 0;
        margin:0;
    }
    h1.date1 {
        float: left;width: 180px;
    }
    .moon, .ring{
        float: left;
        width: 50px;
        height: 50px;
    }
    .moon{
        background-image: url(<?=$url_moon?>);
    }
    .ring{
        background-image: url(ring.png);
    }
</style>
</body>
</html>