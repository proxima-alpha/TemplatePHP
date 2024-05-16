/**
 * @file 캘린더 모듈 자동생성 스크립트
 * @todo setCalendar 파악, 스타일 설정 option 추가
 */

Date.prototype.toFormatString = function () {
    let year = this.getFullYear();
    let month = this.getMonth() + 1;
    let day = this.getDate();
    return `${year.toString().padStart(4, '0')}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`
}

const calendarWeekKeys = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

/**
 * jquery object 에서 해당 함수 호출 시 내부에 calendar 를 추가해주는 기능
 * @param option
 * @todo refactoring & release as an open source
 */
jQuery.prototype.initCalendar = function (option) {
    let $parent = this;
    if ($parent.length == 0) return;
    $parent.empty();
    $parent.css({
        width: '100%',
    })

    // initialize options
    let cellSize = 60;
    let dateStandard = new Date();
    let standardDate = dateStandard.toFormatString();
    let selectedDate = null;
    let styleType = 'circle';
    let language = 'en';
    let limitPrevious = true;
    let limitStandard = true;
    let endDate = null;
    let limitedDayOfWeek = [];
    let textSize = 14;
    let selectedStyle = true;

    if (option) {
        styleType = option.style ?? styleType;
        if (option.standardDate) {
            try {
                dateStandard = new Date(option.standardDate);
                standardDate = dateStandard.toFormatString();
            } catch (e) {
            }
        }
        cellSize = option.cellSize ?? (cellSize < 60 ? 60 : cellSize);
        language = option.lang ?? language;
        limitPrevious = option.limitPrevious ?? limitPrevious;
        limitStandard = option.limitStandard ?? limitStandard;
        if (option.endDate) {
            try {
                let date = new Date(option.endDate);
                endDate = date.toFormatString();
            } catch (e) {
            }
        }
        if (option.selectedDate) {
            try {
                let date = new Date(option.selectedDate);
                selectedDate = date.toFormatString();
            } catch (e) {
            }
        }
        limitedDayOfWeek = option.limitedDayOfWeek ?? limitedDayOfWeek;
        textSize = option.textSize ?? textSize;
        selectedStyle = option.selectedStyle ?? selectedStyle;

        // function allocation
        $parent.onDateSelected = option.onDateSelected
        $parent.onLoaded = option.onLoaded
        $parent.onRefreshed = option.onRefreshed
    }
    $parent.getSelectedDate = function () {
        let $input = $parent.find('input[type=hidden]');
        return $input.val();
    }

    let dayOfWeek = {};
    switch (language) {
        case 'ko' :
            dayOfWeek = {
                'mon': '월',
                'tue': '화',
                'wed': '수',
                'thu': '목',
                'fri': '금',
                'sat': '토',
                'sun': '일',
            };
    }
    const rand = Math.random().toString(36).substr(2, 11);
    let className = `calendar-view-${rand}`;
    let NumberPosition = 5;
    $parent.addClass(className);
    let cellMargin = 10;
    if ($parent.width() != 0 && $parent.width() < cellSize * 7) {
        cellSize = Math.floor($parent.width() / 7 * 100) / 100;
        cellMargin = Math.floor(cellSize / 20);
        NumberPosition = Math.floor(cellSize / 10);
    }
    let innerCellSize = cellSize - cellMargin * 2;
    let innerCellSizeHalf = innerCellSize / 2;

    // rebinding
    let savingOptions = {};
    savingOptions.style = styleType;
    savingOptions.standardDate = standardDate;
    savingOptions.selectedDate = selectedDate;
    savingOptions.cellSize = cellSize;
    savingOptions.cellMargin = cellMargin;
    savingOptions.lang = language;
    savingOptions.limitPrevious = limitPrevious;
    savingOptions.limitStandard = limitStandard;
    savingOptions.endDate = endDate;
    savingOptions.limitedDayOfWeek = limitedDayOfWeek;
    savingOptions.textSize = textSize;
    savingOptions.selectedStyle = selectedStyle;
    try {
        $parent.attr({
            option: JSON.stringify(savingOptions),
        })
    } catch (e) {
        // do nothing
    }
    $parent.addClass(styleType);
    let borderWidth = Math.floor(cellSize / 40);

    $parent.css({
        'width': `${cellSize * 7}px`,
        'position': 'relative',
        'display': 'inline-block',
        'vertical-align': 'middle',
    })

    getOptionStyle = function () {
        let disabledColor = '#dedede';
        return `
        <style>
        .calendar.${className} {
            display: inline-block;
            vertical-align: middle;
            position: relative;
        }
    
        .calendar.${className} li {
            display: inline-block;
            vertical-align: middle;
        }
    
        .calendar.${className} .date li {
            width: ${cellSize}px;
            height: ${cellSize}px;
            font-weight: 400;
            position: relative;
            overflow: hidden;
        }
    
        .calendar.${className} .date li .calendar-inner-cell {
            width: ${innerCellSize}px;
            height: ${innerCellSize}px;
            line-height: ${innerCellSize}px;
            margin: -${innerCellSizeHalf}px -${innerCellSizeHalf}px 0 0;
            position: absolute;
            font-size: 0;
            top: 50%;
            right: 50%;
            z-index: 2;
        }
    
        .calendar.${className} .date li .calendar-number {
            font-size: ${textSize}px;
        }
    
        .calendar.${className}.square .date li .calendar-number {
            position: absolute;
            top: ${NumberPosition}px;
            left: ${NumberPosition}px;
            line-height: normal;
        }
    
        .calendar.${className}.circle .date li.standard .calendar-inner-cell {
            font-weight: bold;
            background: #efefef;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
        }
    
        .calendar.${className}.square .date li.standard {
            font-weight: bold;
            background: #efefef;
        }
    
        .calendar.${className}.circle .date li.selected .calendar-inner-cell {
            box-shadow: 0 0 0 ${borderWidth}px #222 inset; 
            font-weight: bold;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
        }
    
        .calendar.${className}.square .date li.selected {
            box-shadow: 0 0 0 ${borderWidth}px #222 inset; 
        }

        .calendar.${className}.circle .disabled .calendar-inner-cell{
            border-radius: ${innerCellSizeHalf}px;
            background: ${disabledColor};
        }

        .calendar.${className}.square .disabled{
            background: ${disabledColor};
        }
    
        .calendar.${className} .date li.red .calendar-number {
            color: #a40000;
        }
    
        .calendar.${className} .date li.blue .calendar-number {
            color: #6799FF;
        }
    
        .calendar.${className} .calendar-week-title-row {
            font-size: 0;
        }
    
        .calendar.${className} .calendar-week-title-row li {
            width: ${cellSize}px;
            height: 30px;
            line-height: 30px;
            font-size: 16px;
            font-weight: 600;
        }
    
        .calendar.${className} .nav {
            height: 80px;
            line-height: 80px;
            position: relative;
            font-size: 0;
        }
    
        .calendar.${className} .nav .calendar-button {
            width: 30px;
            height: 30px;
            line-height: 30px;
            cursor: pointer;
            position: absolute;
            margin-top: -12px;
            top: 50%;
            text-align: center;
        }
    
        .calendar.${className} .nav .calendar-button * {
            vertical-align: middle;
        }
    
        .calendar.${className} .nav .calendar-button.prev {
            left: 15px;
        }
    
        .calendar.${className} .nav .calendar-button.next {
            right: 15px;
        }
    
        .calendar.${className} .nav .text {
            display: inline-block;
            line-height: normal;
            vertical-align: middle;
            font-size: 20px;
        }
    
        .calendar.${className} .nav .year {
            font-size: 14px;
        }
    
        .calendar.${className} .nav .month {
            font-size: 25px;
        }
    
        .calendar.${className} .calendar-disabled {
            position: absolute;
            top: 110px;
            height: 1000px;
            text-align: left;
            overflow: visible;
        }
        .calendar.${className} .calendar-disabled > ul {
            position: absolute;
            top: 0;
            left: 0;
        }
    
        .calendar.${className}.circle .calendar-disabled li {
            width: 100%;
            height: ${innerCellSize}px;
            border-radius: ${innerCellSizeHalf}px;
            background: ${disabledColor};
        }        

        .calendar.${className}.square .calendar-disabled li {
            width: 100%;
            height: ${innerCellSize}px;
            background: ${disabledColor};
        }
        </style>`
    }

    // 캘린더 기본 html 구조 생성
    let html = `
    ${getOptionStyle()}
    <div class="nav">
        <span class="calendar-button prev">
            <svg width="10px" height="18px"
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8.74 16.06">
                <defs>
                    <style>.cls-1 {
                        fill: none;
                        stroke: #000;
                        stroke-miterlimit: 10;
                    }</style>
                </defs>
                <polyline class="cls-1" points="8.38 0.35 0.71 8.03 8.38 15.71"/>
            </svg>
        </span>
        <span class="text">
            <p class="year"></p>
            <p class="month"></p>
        </span>
        <span class="calendar-button next">
            <svg width="10px" height="18px"
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8.74 16.06">
                <defs>
                    <style>.cls-1 {
                        fill: none;
                        stroke: #000;
                        stroke-miterlimit: 10;
                    }</style>
                </defs>
                <polyline class="cls-1" points="0.35 15.71 8.03 8.03 0.35 0.35"/>
            </svg>
        </span>
    </div>`;

    html += `<ul class="calendar-week-title-row">`;

    for (let i in calendarWeekKeys) {
        let weekKey = calendarWeekKeys[i];
        let weekOptionString = dayOfWeek[weekKey];
        if (weekOptionString) {
            html += `<li style="user-select: none">${weekOptionString}</li>`;
        } else {
            html += `<li style="user-select: none">${weekKey.toUpperCase()}</li>`;
        }
    }

    html += `</ul>`;
    html += `
    <ul class="date">
    </ul>
    <div class="calendar-disabled">
        <ul class="previous"></ul>
        <ul class="following"></ul>
    </div>`
    $parent.append(html);

    // nav 에서 이전, 이후 탐색 버튼 설정
    let $prev = $parent.find('.calendar-button.prev');
    let $next = $parent.find('.calendar-button.next');
    $prev.click(function () {
        let rawTime = $parent.attr('now') && $parent.attr('now').length > 0 ? Number($parent.attr('now')) : undefined;
        let date = new Date(rawTime);
        date.setDate(1);
        date.setMonth(date.getMonth() - 1);
        drawCalendarView($parent, date);
    })
    $next.click(function () {
        let rawTime = $parent.attr('now') && $parent.attr('now').length > 0 ? Number($parent.attr('now')) : undefined;
        let date = new Date(rawTime);
        date.setDate(1);
        date.setMonth(date.getMonth() + 1);
        drawCalendarView($parent, date);
    })
    // 해당 년, 월에 따라 날짜 그리기
    drawCalendarView($parent, dateStandard);
    if ($parent && $parent.onLoaded && typeof $parent.onLoaded == 'function') {
        $parent.onLoaded($parent);
    }
}

/**
 * drawCalendarView 를 다시 호출해 주는 기능
 * (drawCalendarView 에서 조건에 맞게 새로 그려준다)
 */
jQuery.prototype.refreshCalendar = function () {
    const $parent = this;
    let rawTime = $parent.attr('now') && $parent.attr('now').length > 0 ? Number($parent.attr('now')) : undefined;
    let date = new Date(rawTime);
    drawCalendarView($parent, date);
}

jQuery.prototype.getCell = function (day) {
    const $parent = this;
    if (typeof day == 'string') {
        day = parseInt(day)
    }
    if (typeof day != 'number') return null;
    return $parent.find(`.day-${day}`);
}

function setCalendarDefaultStyle($view) {
    $view.css({
        '-webkit-touch-callout': 'none',
        '-webkit-user-select': 'none',
        '-khtml-user-select': 'none',
        '-moz-user-select': 'none',
        '-ms-user-select': 'none',
        'user-select': 'none',
    })
    $view.attr('unselectable', 'on')
    $view.attr('onselectstart', 'return false;')
    $view.attr('onmousedown', 'return false;')
}

/**
 * 클릭 여부에 따라 스타일 설정
 * @param $view
 */
function setCalendarStyle($view, selectable = true) {
    setCalendarDefaultStyle($view);
    if (selectable) {
        $view.css({
            'cursor': 'pointer',
        })
    } else {
        $view.addClass('disabled')
    }
}

/**
 * cell 선택 시 호출되는 공통 기능
 * @param year
 * @param month
 * @param $parent
 * @param $cell
 */
function selectCalendarCell($parent, $cell, year, month, isStyled = true) {
    let $input = $parent.find('input[type=hidden]');
    if ($input != undefined) {
        $input.remove();
    }
    let day = $cell.find('span.calendar-number')[0].innerHTML;
    let value = year.toString().padStart(4, '0') + '-' + month.toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');
    $parent.append('<input type="hidden" name="date" value="' + value + '">')

    let optionString = $parent.attr('option');
    let option;
    if (optionString) {
        try {
            option = JSON.parse(optionString);
        } catch (e) {
            // do nothing
        }
    }
    if ($parent && $parent.onDateSelected && typeof $parent.onDateSelected == 'function') {
        $parent.onDateSelected($parent, value, year, month, day);
    }
    if ($cell.hasClass('limited-day')) return;
    if (isStyled) {
        let $liSelected = $parent.find('.selected');
        if ($liSelected != undefined) {
            $liSelected.removeClass('selected')
        }
        $cell.addClass('selected');
    }
}

/**
 * 주어진 조건에 맞게 캘린더 생성해주는 기능
 * @param $parent
 * @param date
 */
function drawCalendarView($parent, date) {
    $parent.attr({
        now: date.getTime(),
    })
    let optionString = $parent.attr('option');
    let option;
    if (optionString) {
        try {
            option = JSON.parse(optionString);
        } catch (e) {
            // do nothing
        }
    }
    let cellWidth = option.cellSize;
    let dateStandard = new Date(option.standardDate);
    let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    switch (option.lang) {
        case 'ko' :
            months = [];
            for (let i = 1; i <= 12; ++i) {
                months.push(`${i.toString().padStart(2, '0')} 월`)
            }
    }


    let $year = $parent.find('.nav .year');
    let $month = $parent.find('.nav .month');
    let $weeks = $parent.find('.week li');
    setCalendarDefaultStyle($weeks);
    setCalendarDefaultStyle($year);
    setCalendarDefaultStyle($month);
    let $cells = $parent.find('ul.date');
    $cells.empty();

    let year = date.getFullYear();
    let month = date.getMonth();
    let hasDateStandard = (year == dateStandard.getFullYear() && month == dateStandard.getMonth());
    let dateSelected = null;
    let hasDateSelected = false;
    if (option.selectedDate) {
        dateSelected = new Date(option.selectedDate)
        hasDateSelected = (year == dateSelected.getFullYear() && month == dateSelected.getMonth());
    }
    $year[0].innerHTML = year;
    $month[0].innerHTML = months[month];

    // 달력의 첫 번째 날
    let dateFirstOfMonth = new Date(year, month);
    // 달력의 마지막 날
    let dateLastOfMonth = new Date(year, month + 1);
    dateLastOfMonth.setDate(dateLastOfMonth.getDate() - 1);
    let numberOfDays = 32 - new Date(year, month, 32).getDate();
    // 달력의 총 row 개수
    let rows = parseInt((dateFirstOfMonth.getDay() + numberOfDays) / 7);
    if ((dateFirstOfMonth.getDay() + numberOfDays) % 7 != 0) rows += 1;

    // 기준 날짜의 어제 (option 에서 이전 날짜 선택을 제한하고자 하는 경우 어제 날짜를 알 필요가 있음)
    let dateYesterday = new Date(dateStandard);
    dateYesterday.setDate(dateStandard.getDate() - 1);
    let dateCompare = dateYesterday;
    if (option.limitStandard) {
        dateCompare = dateStandard;
    }

    let $prev = $parent.find('.calendar-button.prev');
    let $next = $parent.find('.calendar-button.next');
    // option 에서 이전 날짜 선택을 제한하고자 하는 경우 처리
    if (option.limitPrevious && hasDateStandard) {
        $prev.css({
            'display': 'none',
        })
    } else {
        $prev.css({
            'display': 'inline-block',
        })
    }
    // option 에서 선택을 제한하고자 하는 마지막 날이 있는 경우 처리
    let dateEnd = null;
    let hasDateEnd = false;
    try {
        dateEnd = new Date(option.endDate);
        dateEnd.setDate(dateEnd.getDate() + 1);
        hasDateEnd = (year == dateEnd.getFullYear() && month == dateEnd.getMonth());
    } catch (e) {

    }
    if (dateEnd) {
        let dateNextMonth = new Date(date);
        dateNextMonth.setMonth(dateNextMonth.getMonth() + 1);
        if (hasDateEnd ||
            dateEnd.getDate() == 1 && dateEnd.getMonth() == dateNextMonth.getMonth() && dateEnd.getFullYear() == dateNextMonth.getFullYear()) {
            $next.css({
                'display': 'none',
            })
        } else {
            $next.css({
                'display': 'inline-block',
            })
        }
    } else {
        $next.css({
            'display': 'inline-block',
        })
    }

    // 날짜 rendering
    let days = 0;
    // 달의 첫 번째 날짜 이전 빈칸 채움
    for (let i = 0; i < dateFirstOfMonth.getDay(); i++, days++) {
        let $cell = $('<li></li>');
        setCalendarDefaultStyle($cell)
        $cells.append($cell);
    }
    for (let i = 0; i < numberOfDays; i++, days++) {
        let $cell = $(`<li class="day-${(i + 1)}"><span class="calendar-inner-cell"></span></li>`);
        $cell.find(`.calendar-inner-cell`).append(`<span class="calendar-number">${(i + 1)}</span>`)
        if (hasDateStandard && i == dateStandard.getDate() - 1) {
            $cell.addClass('standard');
        }
        if (hasDateSelected && i == dateSelected.getDate() - 1) {
            $cell.addClass('selected')
            $parent.append('<input type="hidden" name="date" value="' + dateSelected.toFormatString() + '">')
        }

        if (days % 7 == 0) {
            // 일요일
            $cell.addClass('red');
        } else if (days % 7 == 6) {
            // 토요일
            $cell.addClass('blue');
        }

        function setCell() {
            if ((option.limitPrevious && hasDateStandard && i <= dateCompare.getDate() - 1) || // 이전날짜 제한중인 경우
                (dateEnd && hasDateEnd && i >= dateEnd.getDate() - 1) // 마지막 날짜로 제한중인 경우
            ) {  // 이전날짜 제한중인 경우
                // 선택 불가 처리
                setCalendarStyle($cell, false);
            } else {
                setCalendarStyle($cell);
                $cell.click(function () {
                    selectCalendarCell($parent, $(this), year, month + 1, option.selectedStyle);
                })
            }
            if (Array.isArray(option.limitedDayOfWeek)) {
                for (let i in option.limitedDayOfWeek) {
                    let idx = calendarWeekKeys.indexOf(option.limitedDayOfWeek[i]);
                    if (idx > 0 && days % 7 == idx) {
                        $cell.addClass('disabled')
                        $cell.addClass('limited-day')
                        // setCalendarStyle($cell, false);
                        return;
                    }
                }
            }
        }

        setCell();
        $cells.append($cell);
    }
    // 달의 마지막 날짜 이후 빈칸 채움
    for (; days < 6 * 7; days++) {
        let $cell = $('<li></li>');
        setCalendarDefaultStyle($cell)
        $cells.append($cell);
    }

    let isCircleStyle = option.style == 'circle';
    let cellHeight = isCircleStyle ? (cellWidth - option.cellMargin * 2) : cellWidth;
    let cellMargin = isCircleStyle ? option.cellMargin : 0;

    let $ulDisabledPrevious = $parent.find('.calendar-disabled .previous');
    let $ulDisabledFollowing = $parent.find('.calendar-disabled .following');
    $ulDisabledPrevious.empty();
    $ulDisabledFollowing.empty();

    // 시작날짜 기준
    function getRow(first, now) {
        let rows = parseInt((first.getDay() + now.getDate()) / 7);
        if ((first.getDay() + now.getDate()) % 7 != 0) rows += 1
        return rows;
    }

    // 이전 제한 영역 UI 추가
    if (option.limitPrevious) {
        let numberRowPreviousDisabled = rows;
        if (hasDateStandard) {
            numberRowPreviousDisabled = getRow(dateFirstOfMonth, dateCompare);
        } else if (dateYesterday.getFullYear() == year && dateYesterday.getMonth() < month ||
            dateYesterday.getFullYear() < year) {
            numberRowPreviousDisabled = 0;
        }

        for (let i = 0; i < numberRowPreviousDisabled; ++i) {
            let $row = $('<li></li>');

            let count = 7;
            let left = 0;

            if (i == 0) {
                count -= dateFirstOfMonth.getDay();
                left += dateFirstOfMonth.getDay();
                $row.css({
                    'margin-top': cellMargin + 'px',
                })
            } else {
                $row.css({
                    'margin-top': cellMargin * 2 + 'px',
                })
            }
            if (hasDateStandard) {
                if (i == numberRowPreviousDisabled - 1) {
                    count -= (7 - dateCompare.getDay() - 1);
                }
            } else {
                if (rows == numberRowPreviousDisabled && i == numberRowPreviousDisabled - 1) {
                    count -= (7 - dateEnd.getDay() - 1);
                }
            }

            let width = cellWidth * count;
            let marginLeft = cellWidth * left;
            if (isCircleStyle && count > 0) {
                marginLeft += 10
                width -= 20;
            }
            $row.css({
                'width': width + 'px',
                'height': cellHeight + 'px',
                'margin-left': marginLeft + 'px',
            })
            $ulDisabledPrevious.append($row);
        }
    }

    // 이후 제한 영역 UI 추가
    if (hasDateEnd) {
        let numberRowLimited = getRow(dateFirstOfMonth, dateEnd);
        let numberRowLast = getRow(dateFirstOfMonth, dateLastOfMonth);
        let loop_count = numberRowLast - numberRowLimited + 1;
        for (let i = 0; i < loop_count; ++i) {
            let $row = $('<li></li>');
            let count = 7;
            let left = 0;
            if (i == 0) {
                count -= dateEnd.getDay();
                left += dateEnd.getDay()
                let marginTop = cellMargin + cellWidth * (numberRowLimited - 1);
                $row.css({
                    'margin-top': marginTop + 'px',
                })
            } else {
                $row.css({
                    'margin-top': cellMargin * 2 + 'px',
                })
            }
            if (i == loop_count - 1) {
                count -= (7 - dateLastOfMonth.getDay() - 1);
            }
            let width = cellWidth * count;
            let marginLeft = cellWidth * left;
            if (isCircleStyle && count > 0) {
                marginLeft += 10
                width -= 20;
            }
            $row.css({
                'width': width + 'px',
                'height': cellHeight + 'px',
                'margin-left': marginLeft + 'px',
            })
            $ulDisabledFollowing.append($row);
        }
    }
    if ($parent && $parent.onRefreshed && typeof $parent.onRefreshed == 'function') {
        $parent.onRefreshed($parent, year, month + 1);
    }
}
