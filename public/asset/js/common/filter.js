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
            limitStandard: false,
            limitPrevious: false,
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
    let prefix = newSearches.length > 0 ? "&" : "";
    let search = "?" + newSearches.join("&") + prefix
    console.log(window.location.pathname + search + `${target}=${data['date']}`)
    window.location.replace(window.location.pathname + search + `${target}=${data['date']}`)
    closePopup(className);
}
