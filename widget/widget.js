'use strict';
var winWidth = 305; // ширина окна
var winHeight = 310; // высота окна
// изменяем размер
window.resizeTo(winWidth, winHeight);

// окно в центр экрана
window.moveTo(screen.width - winWidth, screen.Height - winHeight);

var server = "https://tech.nemin.ru/widget/";// адрес сервера
var codeCalendar = 'e3sueck4k';		// код получаемого календаря
var status = 'offline'; 	// статус сети
var reload = 15;        	// период обновления статуса
var pingTimer;          	// таймер пинга сети
var stepOnline = 60 * 1000; // шаг проверки сети если онлайн
var stepOffline = 500;  	// шаг проверки сети если оффлайн
var pingCount = 0;      	// счетчик запросов статуса
var frame = null;           // фрейм виджета
var curtain = null;         // блок заглушка
var today;					// текщее число
var watch = null;			// блок часов

window.onload = function(){
    widget();
};

/**
 * запрос данных с сервера
 * @param url - адрес сервера
 * @param param - параметры запроса
 * @param method - метод запроса
 * @returns {*} - false/объект XMLHttpRequest
 */
function ajax(url, param, method) {
    var strParam;
    if(param === undefined) strParam = '';
    else if (typeof(param) === "object") {
        strParam = "?";
        for (var key in param){
            if(strParam.length > 3) strParam += '&';
            strParam += key + "=" + param[key];
        }
    }
    if(method === undefined)
    	method = 'GET';
    var request = new XMLHttpRequest();
    request.open(method, url + strParam, false);
    try {
        request.send();
    } catch (e) {
        return false;
    }
    return request;
}

/**
 * определение статуса
 * вернет истину только если получит нормальный заголовок
 **/
function appIsOnline(){
    var x = ajax(server,{'code':'isonline'},'HEAD');
    if (x === false)
    	return false;
    if (x.status >= 200 && x.status < 304){
        return true;
    }else{
        return false;
    }
}

/**
 * получение календаря
 */
function calendarLoad(){
    var x = ajax(server,{'code': codeCalendar });
    if(x.status === 200){
        calendarBox(x.responseText);
    }else{
        status = 'offline';
        widget();
    }
}

/**
 * размещение календаря на странице
 */
function calendarBox(content) {
    if(frame === null){
        frame = document.getElementById('calendar');
    }
    today = new Date();
    createWidget(content);
	curtain.style.display = 'none';
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
    if(curtain === null)
        curtain = newElem(document.getElementsByTagName('body')[0],'curtain');
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
 * проверка текущей даты
 * перезагрузит календарь если дата в календаре не текущая
 */
function noToday(date) {
    if (today.getDate() !== date.getDate()) {
        status = 'offline';
        curtain.style.display = 'block';
        clearTimeout(pingTimer);
        pingCount = 0;
        widget();
    }
}

/**
 * добавление нуля к цифре меньше 10
 * @param num
 * @returns {string}
 */
function upZero(num) {
    if(num < 10) num = "0"+num;
    return num.toString() ;
}
/**
 *	отображение часов
 */
function digitalWatch() {
    var date = new Date();
    noToday(date);
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    watch.innerText = upZero(hours) + ":" + upZero(minutes) + ":" + upZero(seconds);
    setTimeout(digitalWatch, 1000);
}

/**
 * размещение погодного блока
 * @param parent
 */
function weatherBox(parent) {
    var date = new Date();
    var weather = newElem(parent,'weather');
    newElem(weather,'weather__border');
    weather.style.backgroundImage = 'url(https://info.weather.yandex.net/2/3.ru.png)';
    weather.title = 'Погода: \n'+date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
}

/**
 * размещение блока луны
 * @param box
 * @param moonDay
 */
function moonBox(box,moonDay){
    var img  = newElem(box,'moon__image','','img');
    var text = newElem(box,'moon__text');
    img.src =  server + 'img/moon' + moonDay + '.png';
    text.innerText = moonDay + ' лунный день';
    box.onmouseover = function () {
        img.style.display = 'none';
        text.style.display = 'block';
    };
    box.onmouseout = function () {
        img.style.display = 'block';
        text.style.display = 'none';
    };
}

/**
 * создаем новый элемент
 * и размещаем его у родителя
 * @param parent   - родитель элемента
 * @param selector - класс элемента
 * @param value	   - содержимое элемента
 * @param type	   - тип элемента
 * @returns {HTMLElement}
 */
function newElem(parent,selector,value,type){
    if(type === undefined) type = 'div';
    var result = document.createElement(type);
    if(value !== undefined && type !== 'img')
        result.innerText = value;
    result.className = selector;
    parent.appendChild(result);
    return result;
}

/**
 * создние блока даты
 * @param parent
 * @param name
 */
function createDateBox(parent,name){
    var monthName = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
    var dayName = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
    var date = new Date();
    var dateBox = newElem(parent,'header__center '+name);
    watch   = newElem(dateBox, name + '__watch');
    digitalWatch();
    var params = {
        'number':date.getDate(),
        'month':monthName[date.getMonth()],
        'name':dayName[date.getDay()]
    };
    for(var k in params){
        newElem(dateBox, name + '__' + k,params[k]);
    }
}

/**
 * создание блока календаря
 * @param parent
 * @param events
 */
function createCalendar(parent,events){
    var dayNameMin = ['ПН','ВТ','СР','ЧТ','ПТ','СБ','ВС'];
    var date = new Date();
    var today = date.getDate();
    var dayWeek = date.getDay();
    if (dayWeek === 0) dayWeek = 7;
    var month = date.getMonth();
    var name    = newElem(parent,'name');
    var main    = newElem(parent,'calendar');
    date.setDate(today - dayWeek);
    for(var x=1;x<=(5*7);x++){
        var itemDate = date.getDate();
        date.setDate(itemDate + 1);
        var dateToEvent = date.getFullYear() + upZero(date.getMonth()+1) + upZero(date.getDate());
        var selector = '';
        if(date.getDay() === 6 || date.getDay() === 0)
            selector += " calendar__day__weekend";
        if(x<8){
            newElem(name,'calendar__day'+selector,dayNameMin[x - 1]);
        }
        if(month !== date.getMonth())
            selector += " calendar__day__other";
        var item = newElem(main,'calendar__day' + selector);
        selector = '';
        if(events.indexOf(dateToEvent) > -1){
            if(month === date.getMonth())
                selector += " calendar__day__active";
            else
                selector += " calendar__day__other_active";
        }
        if(date.getDate() === today && month === date.getMonth())
            selector += " calendar__day__today";
        newElem(item,selector,date.getDate());
    }
}

/**
 * создание блока виджета из разных блоков
 * @param events - список событий календаря
 */
function createWidget(events) {
    var content = document.getElementById('content');
    if(content !== null){
        content.innerHTML = '';
    }else{
        content = newElem(document.getElementsByTagName('body')[0],'content');
        content.id = 'content';
    }
    var header  = newElem(content,'header');
    moonBox(newElem(header, 'header__left'),events.substr(4,2));
    weatherBox(newElem(header, 'header__right'));
    createDateBox(header,'date');
    createCalendar(content,events);
}
