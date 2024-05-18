if (window.performance) {
    if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
        window.location.replace(window.location.pathname)
    } else {
    }
}
async function openCalendarPopup(target, expect_date = null, expect_time = null) {
    let className = 'popup-calendar';
    let html = `
    <h3 class="popup-title">
        ${lang('select_date')}
    </h3>
    <div class="form-wrap">
        <div class= "calendar-wrap">
            <div class= "calendar"></div>
        </div>
    </div>
    <div class="control-button-wrap absolute line-before">
        <div class="control-button-box">
            <a href="javascript:cancelCalendarSelect('${className}', '${target}');"
                class="button under-line cancel">
                <img src="/asset/images/icon/cancel.png"/>
                <span>${lang('cancel')}</span>
            </a>
            <a href="javascript:confirmCalendarSelect('${className}', '${target}')" class="button confirm">
                <img src="/asset/images/icon/check.png"/>
                <span>${lang('confirm')}</span>
            </a>
        </div>
    </div>`;
    openPopup({
        className: className,
        style: `
        <style>
            .popup {
                width: 400px;
            }
        </style>
        `,
        html: html,
    }, ($parent) => {
        $parent.find(`.calendar`).initCalendar({
            cellSize: 60,
            selectedDate: expect_date ?? null,
            standardDate: expect_date ?? null,
            limitStandard: true,
            limitPrevious: false,
            endDate: new Date()
        })
    })
}

function cancelCalendarSelect(className, target) {
    closePopup(className);
}

function confirmCalendarSelect(className, target) {
    let data = parseInputToData($(`.${className} input, .${className} textarea`))
    if (!data['date']) closePopup(className);

    $(`.filter-wrap input[name=${target}]`).val(data['date'])
    // window.location.replace()
    let searches = window.location.search.replaceAll("?", "").split("&");
    let newSearches = [];
    for (let item of searches) {
        let strs = item.split("=")
        if (item.length > 0 && strs[0] != target) {
            newSearches.push(item)
        }
    }

    let start_date = $(`.filter-wrap input[name=start_date]`).val();
    let end_date = $(`.filter-wrap input[name=end_date]`).val();
    if (target == 'end_date') {
        end_date = data['date'];
    } else if (target == 'start_date') {
        start_date = data['date']
    }

    if (target != 'start_date') {
        if (start_date && end_date) {
            const rawTimeStart = new Date(start_date).getTime()
            const rawTimeEnd = new Date(end_date).getTime()
            if (Math.floor((rawTimeEnd - rawTimeStart) / 86400000) > 31) {
                openPopupMessage("날짜는 최대 한달 동안 선택 가능합니다")
                return;
            }
        }
        // let prefix = newSearches.length > 0 ? "&" : "";
        // let search = "?" + newSearches.join("&") + prefix
        let search = `?start_date=${start_date}&end_date=${end_date}`
        window.location.replace(window.location.pathname + search)
    } else {
        closePopup(className);
    }
}
