
/**
 * 예약 현황 popup 여는 기능
 * @param id
 */
function openQuestionBoardPopup(id) {
    apiRequest({
        type: 'GET',
        url: `/api/question/get/${id}`,
        dataType: 'json',
        success: async function (response, status, request) {
            if (!response.success)
                return;
            let data = response.data;
            let className = 'popup-question';
            let css = await loadStyleFile('/asset/css/common/popup/question/view.css', "." + className);
            let html = ``
            if (!isEmpty(data['questioner_name']) && !isEmpty(data['questioner_id'])) {
                html += `
                <div class= "row-box line-after black">
                    <div class="row bold user link">
                        <a href="javascript:openUserPopup(${data['questioner_id']});" class="button out-line">
                            <img src="/asset/images/icon/user.png"/>
                            <span>${data['questioner_name']}</span>
                        </a>
                    </div>
                </div>`
            } else if (!isEmpty(data['temp_name'])) {
                html += `
                <div class= "row-box line-after black">
                    <div class="row bold user link">
                        <img src="/asset/images/icon/user.png"/>
                        <span>${data['temp_name']}</span>
                    </div>
                </div>`
            }
            if (!isEmpty(data['temp_phone_number'])) {
                html += `
                    <div class="line-after">
                        <div class="row-text-wrap">
                            <img src="/asset/images/icon/message.png"/>
                            <span>${data['temp_phone_number']}</span>
                        </div>
                    </div>`
            }
            if (data['question_comment'] && data['question_comment'].length > 0) {
                html += `
                    <div class="text-wrap">
                        <div class="comment">${data['question_comment']}</div>
                    </div>`
            }

            if (data['status'] && data['status'] != 'requested') {

                if (!isEmpty(data['respondent_id']) && !isEmpty(data['respondent_name'])) {
                    html += `
                        <div class= "row-box line-after black" style="margin-top: 20px;">
                            <div class="row bold user link">
                                <a href="javascript:openUserPopup(${data['respondent_id']});" class="button out-line">
                                    <img src="/asset/images/icon/user.png"/>
                                    <span>${data['respondent_name']}</span>
                                </a>
                            </div>
                        </div>`
                }
                if (data['respond_comment'] && data['respond_comment'].length > 0) {
                    html += `
                    <div class="text-wrap">
                        <div class="comment">${data['respond_comment']}</div>
                    </div>`
                }
            } else {
                if (getCookie('is_admin') == 1) {
                    html += `
                    <div class="control-button-wrap absolute line-before">
                        <div class="control-button-box">
                            <a href="javascript:closePopup('${className}');"
                                class="button under-line refuse">
                                <img src="/asset/images/icon/cancel.png"/>
                                <span>${lang('cancel')}</span>
                            </a>
                            <a href="javascript:openQuestionPopupAccept(${data['id']})"
                                class="button under-line accept">
                                <img src="/asset/images/icon/check.png"/>
                                <span>${lang('respond')}</span>
                            </a>
                        </div>
                    </div>`;
                }
            }
            openPopup({
                className: className,
                style: `<style>${css}</style>`,
                html: html,
            }, ($parent) => {
                if ($parent.find('.control-button-wrap').length > 0) {
                    $parent.find(`.popup-box`).addClass('has-control-button');
                }
            })
        },
        error: function (response, status, error) {
        },
    });
}

async function openQuestionPopupAccept(id) {
    let className = 'popup-question-accept';
    let css = await loadStyleFile('/asset/css/common/popup/question/respond.css', "." + className);
    let html = `
    <h3 class="popup-title">
        ${lang('respond')}
    </h3>
    <div class="form-wrap">`
    let minHeight = 150;
    html += `
        <div class="input-wrap line-before">
            <textarea name="respond_comment" class="comment" style="min-height: ${minHeight}px"></textarea>
        </div>
    </div>
    <div class="control-button-wrap absolute line-before">
        <div class="control-button-box">
            <a href="javascript:closePopup('${className}');"
                class="button under-line cancel">
                <img src="/asset/images/icon/cancel.png"/>
                <span>${lang('cancel')}</span>
            </a>
            <a href="javascript:confirmQuestionAccept('${className}', ${id})" class="button confirm">
                <img src="/asset/images/icon/check.png"/>
                <span>${lang('confirm')}</span>
            </a>
        </div>
    </div>`;
    openPopup({
        className: className,
        style: `<style>${css}</style>`,
        html: html,
    }, ($parent) => {
        $parent.find(`.calendar`).initCalendar({
            cellSize: 60,
            selectedDate: expect_date ?? null,
            standardDate: expect_date ?? null,
            limitStandard: false,
            limitPrevious: false,
        })
        $parent.find(`.time-selector`).initTimeSelector({
            selectedTime: expect_time ?? null,
        })
    })
}

/**
 * API 호출
 * 예약 수락 기능
 * @param className
 * @param id
 */
function confirmQuestionAccept(className, id) {
    let data = parseInputToData($(`.${className} input, .${className} textarea`))

    data['confirm_date'] = data['date'];
    data['confirm_time'] = data['time'];

    delete data['date'];
    delete data['time'];

    apiRequest({
        type: 'POST',
        url: `/api/question/accept/${id}`,
        data: data,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            location.reload();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}
