<?
if($_GET['code'] == 'isonline'){
	header("Access-Control-Allow-Origin: *");
	die();
}

function ShowMon($event)
{
    $max_day = date("t", mktime(0, 0, 0, $mon, 1, $year));
    $w_day = date("w", mktime(0, 0, 0, date('n')));
    $arr_day_name = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
    if ($w_day == 0) $w_day = 7;//если воскресенье - то 7
    for ($i = 1, $x = 1; $i <= 35; $i++) {
        $day = date('j', mktime(0, 0, 0, date('n'), date('j') - $w_day + $i));
        $mon = date('n', mktime(0, 0, 0, date('n'), date('j') - $w_day + $i));
        echo "<td class='calendar__day ";
        if ($mon < date("n")) echo "calendar__aftermon"; //  не текущий месяц помечаем
        if ($mon > date("n")) echo "calendar__beforemon"; //  не текущий месяц помечаем
        echo "'><div class='";
		if (($mon == date('n')) and ($day == date('j'))) //если день и месяц соотв сегодня - помечаем
		{
			echo " calendar__day__today ";
		}
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
                echo ' calendar__day__active ';
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
<div class="header">
	<h1 id="digital_watch" class="header__watch"></h1>
	<div class="header__left moon" id="moonBox">
		<img id = "moon" src = "img/moon<?=$day_moon?>.png" 
			class="moon__image">
		<p class="moon__text" id="moonText">
			<?=$day_moon?> лунный день
		</p>
	</div>
	<div class="header__center date">
		<p class="date__number">
		   <?echo date('j');?>
		</p>
		<p class="date__month">
			<?echo $arr_mon_name[date('n')-1];?>
		</p>
		<p class="date__name">
			<?echo $arr_day_name[date('w')];?>
		</p>
	</div>
	<div class="header__right">
		<div class="weather" 
				style="background: #ececec url(https://info.weather.yandex.net/2/3.ru.png) -19px -32px;" 
				title="Обновлено:  <?="\n".date('j.n.y G:i')?>">
			<div class="weather__border"></div>
		</div>
	</div>

</div>
<table class="calendar">
    <tr>
        <?
        $arr_w = array('ПН','ВТ','СР','ЧТ','ПТ','СБ','ВС');
        foreach($arr_w as $w_item)
        {
            echo "<th> $w_item </th>";}
        ?>
    </tr>
    <tr>
        <? ShowMon($arr_event);?>
    </tr>
</table>

<style>

</style>

