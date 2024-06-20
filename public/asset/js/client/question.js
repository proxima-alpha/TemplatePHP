
async function openQuestionPopupRequest(board_id) {
    let className = 'popup-reservation-request';
    let css = await loadStyleFile('/asset/css/common/popup/question/request.css', "." + className);
    let html = `
    <h3 class="popup-title">
        ${lang('inquiry')}
    </h3>
    <div class="form-wrap">`
    let minHeight = 150;
    html += `
        <div class="input-wrap line-before">
            <textarea placeholder="${lang('request_comment')}" name="question_comment" class="comment" style="min-height: ${minHeight}px" onkeydown="resizeInputPopupTextarea(this)"></textarea>
        </div>
        <input hidden type="text" name="question_board_id" class="editable" value="${board_id}"/>
        <input hidden type="text" name="questioner_id" class="editable" value="${getCookie('user_id')}"/>
    </div>
    <div class="control-button-wrap absolute line-before">
        <div class="control-button-box">
            <a href="javascript:closePopup('${className}');"
                class="button under-line cancel">
                <img src="/asset/images/icon/cancel_white.png"/>
                <span>${lang('cancel')}</span>
            </a>
            <a href="javascript:confirmQuestionRequest('${className}')" class="button confirm">
                <img src="/asset/images/icon/check_white.png"/>
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
        })
        $parent.find(`.time-selector`).initTimeSelector();
    })
}

/**
 * API 호출
 * 예약 요청 기능
 * @param className
 */
function confirmQuestionRequest(className) {
    clearErrorsByClassName(className);
    let data = parseInputToData($(`.${className} input, .${className} textarea`))

    apiRequest({
        type: 'POST',
        url: `/api/question/request`,
        data: {
            ...data,
            question_board_code: 'inquiry',
        },
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
