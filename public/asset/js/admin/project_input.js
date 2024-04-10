async function openCalendarPopup(target, expect_date = null, expect_time = null) {
    let className = 'popup-calendar';
    let html = `
    <h3 class="popup-title">
        ${lang('날짜 선택')}
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

function confirmCalendarSelect(className, target) {
    closePopup(className);
}

function cancelCalendarSelect(className, target) {
    closePopup(className);
}

function deleteUploadedArtistFile(target = 'topic', id) {
    let index = files.get(target).indexOf(id.toString());
    if (index < 0) return;
    const $uploader = $(`.row-uploader.${target}`)
    const $inputs = $uploader.find('input[name="id"]');
    for(let i= 0; i< $inputs.length; ++i) {
        if($inputs.eq(i).val() == id) {
            files.splice(target, index);
            $inputs.eq(i).parent().remove();
        }
    }
}
