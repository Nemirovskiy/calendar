'use strict';
var winWidth = 304; // ширина окна
var winHeight = 314; // высота окна
// изменяем размер
window.resizeTo(winWidth, winHeight);

// окно в центр экрана
var winPosX = screen.width - winWidth;
var winPosY = screen.Height - winHeight;
window.moveTo(winPosX, winPosY);
var server = "http://widget/calendar.php"; 		// адрес сервера
var status = 'offline'; // статус сети
var reload = 15;        // период обновления статуса
var pingTimer;          // таймер пинга сети
var stepOnline = 60 * 1000;  // шаг проверки сети если онлайн
var stepOffline = 500;  // шаг проверки сети если оффлайн
var pingCount = 0;      // счетчик запросов статуса
var frame = null;              // фрейм виджета
var curtain;              // блок заглушка
var today;

function ajax(url,param,method) {
	var strParam;
	if(param === undefined) strParam = '';
	else if (typeof(param) == "object") {
		strParam = "?";
		for (var key in param){
			if(strParam.length > 3) strParam += '&';
			strParam += key + "=" + param[key];
			
		}
	}
	if(method === undefined) method = 'GET';
	var request = new XMLHttpRequest();
	request.open(method, url + strParam, false);
	try {
		request.send();
			return request;
	} catch (e) {
		return false;
	}
}

/** функция определения статуса
* вернет истину только если получит нормальный заголовок
**/
function appIsOnline(){
	var x = ajax(server,{'code':'isonline'},'HEAD');
	if (x === false) return false;
	if (x.status >= 200 && x.status < 304) {
		return true;
	} else {
		return false;
	}
}

/**
 * функция добавления/обновления календаря
 */
function calendarLoad(){
	var x = ajax(server);
	if(x.status == 200){
		calendarBox(x.responseText);
	}else{
		status = 'offline';
		widget();
	}
}

/**
 * Функция размещения календаря на странице
 */
function calendarBox(content) {
	if(frame === null){
		frame = document.getElementById('calendar');
	}
	today = new Date();
	frame.innerHTML = content;
	curtain.style.display = 'none';
	digitalWatch();
	moonBox();
}

/**
 * Функция основного приложения
 * 1. проверить есть ли сеть
 * (+)
 *     1. Загрузить/перезагрузить календарь
 *     2. Проверять сеть с шагом stepOnline
 *        и обновить календарь через reload раз
 * (-) 1. Включить заставку
 *     2. Изменить шаг проверки сети на stepOffline
 */
function widget(){
	if(appIsOnline() === true){
		if(status === 'offline'){
			status = 'online';
			calendarLoad();
			pingCount = 0;
		}
		if(pingCount >= reload){
			calendarLoad();
			pingCount = 0;
		}else{
			pingCount++;
		}
		pingTimer = setTimeout(widget,stepOnline);
	}
	else{
		if(status === 'online'){
			status = 'offline';
			curtain.style.display = 'block';
			clearTimeout(pingTimer);
			pingCount = 0;
		}
		pingTimer = setTimeout(widget, stepOffline);
	}
}

/**
 * функция проверки текущей даты
 * перезагрузит календарь если дата в календаре не текущая
 */
function noToday(date) {
	if (today.getDate() != date.getDate()) {
		status = 'offline';
		curtain.style.display = 'block';
		clearTimeout(pingTimer);
		pingCount = 0;
		widget();
	}
}

/**
 *
 */
function digitalWatch() {
	var date = new Date();
	noToday(date);
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	if (hours < 10) hours = "0" + hours;
	if (minutes < 10) minutes = "0" + minutes;
	if (seconds < 10) seconds = "0" + seconds;
	document.getElementById("digital_watch").innerHTML = hours + ":" + minutes + ":" + seconds;
	setTimeout("digitalWatch()", 1000);
}

function moonBox(){
	var box  = document.getElementById('moonBox');
	var img  = document.getElementById('moon');
	var text = document.getElementById("moonText");
	box.onmouseover = function () {
		img.style.display = 'none';
		text.style.display = 'block';
	}
	box.onmouseout = function () {
		img.style.display = 'block';
		text.style.display = 'none';
	}
}

window.onload=function(){
	curtain = document.getElementById('block');
	widget();
};