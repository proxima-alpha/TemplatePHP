/**
 * @file 데이터 보기/입력을 위한 popup-input 을 공통처리하기 위한 스크립트
 */

const initContainer = {};

/**
 * 데이터 입력에 따른 input 용 html form 출력 기능
 * @param {string}key       data object 에서 어떤 데이터를 가져올 것인지 결정
 * @param {Object}data      실제 key-value 를 담고 있는 object
 * @param {Object}typeSet   각 input 에 대한 세부 설정을 실제 호출하는 부분에서 설정하도록 object 를 입력 받도록 함
 * @returns {string|null}
 */
function fromDataToHtml(key, data, typeSet) {
    let set = typeSet[key];
    let type = undefined;
    let editable = false;
    let readonly = false;
    let integer = false;
    let name = key;
    if (set) {
        type = set['type'];
        if (set['editable'] != undefined) {
            editable = set['editable'] == true || set['editable'] == 1;
        } else {
            editable = true;
        }
        if (set['readonly'] != undefined) {
            readonly = set['readonly'] == true || set['readonly'] == 1;
        } else {
            readonly = false;
        }
        if (set['name']) {
            name = set['name'];
        } else {
            if (name.startsWith('is_')) {
                name = name.replace('is_', '');
            }
            name = name.charAt(0).toUpperCase() + name.slice(1);
        }
        if (set['integer'] == true || set['integer'] == 1) {
            integer = true;
        }
    }
    let value = undefined;
    let isReadOnly = true;
    if (data) {
        value = data[key];
    } else {
        isReadOnly = readonly;
    }

    // create 에서 editable 속성이 아닌경우는 표기할 필요 없음
    // TODO 다른 케이스에서 테스트 좀 더 필요, 테스트 후 data 없는 경우가 create 뿐이면 변수명 바꿀 필요가 있음
    if (!data && !editable) return false;

    function checkClasses(classes = []) {
        if (editable) {
            classes.push('editable');
        }
        if (readonly) {
            classes.push('readonly')
        }
        if (classes.length == 0) return '';
        let prefix = '';
        let result = `class="`
        for (let i in classes) {
            result += prefix + classes[i];
            prefix = ' ';
        }
        result += `"`;
        return result;
    }

    switch (type) {
        case 'select': {
            let option = `${checkClasses()} ${isReadOnly ? `disabled` : ``}`
            let html = `
            <div class="input-wrap inline">
                <p class="input-title">${name}</p>
                <select name="${key}" value="${value ?? ''}" ${option}>`
            if (set && set['options']) {
                try {
                    for (let i in set['options']) {
                        let optionValue = set['options'][i]['value'];
                        html += `<option value="${optionValue}" ${value == optionValue ? 'selected' : ''}>${set['options'][i]['name']}</option>`
                    }
                } catch (e) {
                    // do nothing
                }
            }
            html += `</select>`
            if (set['description']) {
                html += `<div class="info-text-wrap" style="display: none;">${set['description']}</div>`
            }
            html += `</div>`
            return html;
        }
        case 'bool': {
            let option = `${checkClasses()} ${isReadOnly ? `disabled` : ``}
             ${value && value == 1 ? 'checked' : ''}`
            let html = `
            <div class="input-wrap inline">
                <p class="input-title">${name}</p>
                <input type="checkbox" name="${key}" ${option}/>`
            if (set['description']) {
                html += `<div class="info-text-wrap" style="display: none;">${set['description']}</div>`
            }
            html += `</div>`
            return html
        }
        case 'long-text': {
            let option = `${checkClasses(['under-line'])} ${isReadOnly ? `readonly` : ``}`
            let html = `
                <div class="input-wrap inline">
                    <p class="input-title">${name}</p>
                    <textarea name="${key}" onkeydown="resizeInputPopupTextarea(this)" onkeyup="resizeInputPopupTextarea(this)" ${option}>${value ? value.toTextareaString() : ''}</textarea>`
            if (set['description']) {
                html += `<div class="info-text-wrap" style="display: none;">${set['description']}</div>`
            }
            html += `</div>`
            return html
        }
        default: {
            let option = `${checkClasses(['under-line'])} ${isReadOnly ? `readonly` : ``} ${integer ? `oninput="this.value=this.value.replace(/[^0-9]/g,'');"` : ``}`
            let html = `
                <div class="input-wrap inline">
                    <p class="input-title">${name}</p>
                    <input type="${type}" name="${key}" ${option} value="${value ?? ''}"/>`
            if (set['description']) {
                html += `<div class="info-text-wrap" style="display: none;">${set['description']}</div>`
            }
            html += `</div>`
            return html
        }
    }
}

/**
 * popup-input 사용을 위한 initialize 기능
 * 각 string 을 유동적으로 받기 위해 함수로 전달받음
 * @param {{
 *     getGetUrl: (id) => string,           //get API route
 *     getCreateUrl: () => string,          //create API route
 *     getUpdateUrl: (id) => string,        //update API route
 *     getDeleteUrl: (id) => string,        //delete API route
 *     getHtml: (data) => string,           //본체 html
 *     getControlHtml: (data) => string,    //특정 케이스에서 하단 버튼을 나타낼지 말지 정할 수 있도록 control 부분을 html 과 따로 받음
 *     deleteMessage: string,               //delete popup 메세지가 선택적으로 나올 수 있어 추가
 * }} input
 */
function initializeInputPopup(input) {
    let key = 'default'
    if (input['key']) {
        key = input['key'];
    }
    initContainer[key] = {
        ...input,
        className: `${key}-input-popup`
    };
}

/**
 * popup 컨트롤 버튼 추가 기능
 * - 공통된 코드가 반복되어 함수 추가
 * @param data
 */
function addInputPopupControlWrap(key, data) {
    const className = initContainer[key].className;
    let controlHtml = initContainer[key].getControlHtml !== undefined &&
    initContainer[key].getControlHtml !== null ?
        initContainer[key].getControlHtml(key, data) :
        initContainer[key].getControlHtml;

    if (typeof controlHtml == 'string' && controlHtml.length == 0) {
        return;
    }
    if (initContainer[key].getControlHtml === null) return;
    let $parent = $(`.${className}`);
    if (controlHtml != undefined) {
        $parent.find(`.popup-inner`).append(`
        <div class="control-button-wrap absolute line-before">
            <div class="control-button-box">
                ${controlHtml}
            </div>
        </div>`
        );
    } else {
        $parent.find(`.popup-inner`).append(`
        <div class="control-button-wrap absolute line-before">
            <div class="control-button-box">
                <a href="javascript:editInputPopup('${key}', ${data['id']});"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span>${lang('edit')}</span>
                </a>
                <a href="javascript:openInputPopupDelete('${key}', ${data['id']});"
                    class="button under-line delete">
                    <img src="/asset/images/icon/delete.png"/>
                    <span>${lang('delete')}</span>
                </a>
            </div>
        </div>`);
    }
}

/**
 * 미리 설정 한 getGetUrl 로 정보를 읽어 입력한 getHtml 규칙에 따라
 * openPopup 을 이용해 input 용 popup 을 여는 기능
 * @requires openPopup
 * @param {string}id            API 에 전달 할 id
 * @returns {Promise<void>}
 * @throws {Response}           fetch 로 파일읽기에 실패했을 경우 결과를 throw
 * @todo(log) throw 에 잡힌 경우 log 로 남길 필요 있음
 */
async function openInputPopup(id, key = 'default') {
    if (!initContainer[key].getGetUrl || !initContainer[key].getHtml) return;
    try {
        apiRequest({
            type: 'GET',
            url: initContainer[key].getGetUrl(id),
            dataType: 'json',
            success: async function (response, status, request) {
                if (!response.success)
                    return;
                const className = initContainer[key].className;
                let data = response.data;
                let css = ``;
                try {
                    css += await loadStyleFile('/asset/css/common/input.css', "." + className);
                    css += await loadStyleFile('/asset/css/common/popup/input.css', "." + className);
                    if (initContainer[key].getCssFiles) {
                        const files = initContainer[key].getCssFiles();
                        for (let file of files) {
                            css += await loadStyleFile(file, "." + className);
                        }
                    }
                } catch (e) {
                    console.error(e)
                }
                let style = `
                <style>
                ${css}
                </style>`
                let html = `
                <div class="form-wrap">
                    ${initContainer[key].getHtml(data)}
                    <div class="error-message-wrap"></div>
                </div>`
                openPopup({
                    className: className,
                    style: style,
                    html: html,
                }, ($parent) => {
                    addInputPopupControlWrap(key, data);
                    let $textarea = $parent.find(`textarea`);
                    for (let i = 0; i < $textarea.length; ++i) {
                        resizeInputPopupTextarea($textarea.get(i));
                    }
                });
            },
            error: function (response, status, error) {
            },
        });
    } catch (e) {
        // do nothing
    }
}

/**
 * 열려있는 popup 을 다시 처음 상태로 refresh 하는 기능
 * 데이터는 화면이 다시 로드될 시 재확인 해 줄 필요가 있기 때문에
 * getGetUrl 을 이용하여 데이터를 다시 읽어온다
 * cancel 버튼 누를 때 호출
 * @param {string}id            API 에 전달 할 id
 */
function refreshInputPopup(key, id) {
    const className = initContainer[key].className;
    apiRequest({
        type: 'GET',
        url: initContainer[key].getGetUrl(id),
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success)
                return;
            let data = response.data;
            $(`.${className} .popup-inner`).empty();
            let html = `
            <div class="form-wrap">
                ${initContainer[key].getHtml(data)}
                <div class="error-message-wrap"></div>
            </div>`
            $(`.${className} .popup-inner`).append(html)
            addInputPopupControlWrap(key, data);
            let $textarea = $(`.${className} textarea`);
            for (let i = 0; i < $textarea.length; ++i) {
                resizeInputPopupTextarea($textarea.get(i));
            }
        },
        error: function (response, status, error) {
        },
    });
}

/**
 * 빈 데이터를 getHtml 규칙에 따라
 * openPopup 을 이용해 create 용 popup 을 여는 기능
 * @requires openPopup
 * @requires closePopup
 * @returns {Promise<void>}
 */
async function openInputPopupCreate(key = 'default') {
    const className = `${key}-popup-create`;
    if (!initContainer[key].getCreateUrl || !initContainer[key].getHtml) return;
    try {
        let css = ``;
        try {
            css += await loadStyleFile('/asset/css/common/input.css', "." + className);
            css += await loadStyleFile('/asset/css/common/popup/input.css', "." + className);
            if (initContainer[key].getCssFiles) {
                const files = initContainer[key].getCssFiles();
                for (let file of files) {
                    css += await loadStyleFile(file, "." + className);
                }
            }
        } catch (e) {
            console.error(e)
        }
        let style = `
        <style>
        ${css}
        </style>`
        let html = `
        <div class="form-wrap">
            ${initContainer[key].getHtml()}
            <div class="error-message-wrap"></div>
        </div>`
        openPopup({
            className: className,
            style: style,
            html: html,
        }, ($parent) => {
            $parent.find(`.form-wrap .info-text-wrap`).css({
                'display': 'inline-block'
            })
            $parent.find(`.popup-box`).addClass('has-control-button');
            $parent.find(`.popup-inner`).append(`
            <div class="control-button-wrap absolute line-before">
                <div class="control-button-box">
                    <a href="javascript:closePopup('${className}');"
                        class="button under-line cancel">
                        <img src="/asset/images/icon/cancel.png"/>
                        <span>${lang('cancel')}</span>
                    </a>
                    <a href="javascript:confirmInputPopupCreate('${key}');"
                        class="button under-line confirm">
                        <img src="/asset/images/icon/check.png"/>
                        <span>${lang('confirm')}</span>
                    </a>
                </div>
            </div>`);
        })
    } catch (e) {
        // do nothing
    }
}

/**
 * input 에 입력불가 옵션을 해제 하고 control 부분의 버튼을 변경하는 기능
 * @param {string}id
 */
function editInputPopup(key, id) {
    const className = initContainer[key].className;
    let $parent = $(`.${className}`);
    $parent.find(`.form-wrap .editable`).not(`.readonly`).removeAttr('readonly')
    $parent.find(`.form-wrap .editable`).not(`.readonly`).removeAttr('disabled')
    $parent.find(`.form-wrap .button-wrap`).remove();
    $parent.find(`.popup-inner .control-button-wrap`).remove();

    $parent.find(`.form-wrap .info-text-wrap`).css({
        'display': 'inline-block'
    })

    $parent.find(`.popup-inner`).append(`
    <div class="control-button-wrap absolute line-before">
        <div class="control-button-box">
            <a href="javascript:refreshInputPopup('${key}', ${id});"
                class="button under-line cancel">
                <img src="/asset/images/icon/cancel.png"/>
                <span>${lang('cancel')}</span>
            </a>
            <a href="javascript:confirmInputPopupEdit('${key}', ${id});"
                class="button under-line confirm">
                <img src="/asset/images/icon/check.png"/>
                <span>${lang('confirm')}</span>
            </a>
        </div>
    </div>`);

    // popup 내부의 tab 처리
    let $tabbable = $parent.find("input:not([type='hidden']), select, textarea, [href]");
    let $tabbableFirst = $tabbable && $tabbable.first();
    if ($tabbableFirst) $tabbableFirst.focus();
}

/**
 * 제거 시 다시한번 묻는 popup 을 여는 기능
 * 독립된 새 popup 을 연다
 * @requires openPopup
 * @requires closePopup
 * @param id
 * @returns {Promise<void>}
 */
async function openInputPopupDelete(key, id) {
    let className = `${key}-popup-delete`;
    let css = await loadStyleFile('/asset/css/common/popup/delete.css', "." + className);
    let html = `
    <div class="text-wrap">
        ${initContainer[key].deleteMessage ?? lang('message_popup_delete')}
    </div>
    <div class="error-message-wrap"></div>
    <div class="button-wrap controls">
        <a href="javascript:closePopup('${className}')" class="button cancel white">${lang('cancel')}</a>
        <a href="javascript:confirmInputPopupDelete('${key}', ${id})" class="button confirm black">${lang('delete')}</a>
    </div>`;
    openPopup({
        className: className,
        style: `<style>${css}</style>`,
        html: html,
    })
}

/**
 * API 호출 시 성공 처리 기능
 * 성공이어도 메세지가 있는 경우 출력
 * @param className
 * @returns {(function(*, *, *): void)|*}
 */
function getSuccessCallback(className) {
    return function (response, status, request) {
        if (response.success) {
            location.reload();
        } else {
            openPopupErrors('popup-error', response, status, request);
        }
    }
}

/**
 * API 호출 시 실패 처리 기능
 * 에러 메세지 출력
 * @param className
 * @returns {(function(*, *, *): void)|*}
 */
function getErrorCallback(className) {
    return function (response, status, error) {
        let message = error;
        try {
            let errorObject = JSON.parse(response.responseText);
            if (errorObject.message) {
                message = errorObject.message
            }
        } catch (e) {
            // do nothing
        }
        if (message) {
            $(`.${className} .error-message-wrap`).append(`<div>${message}</div>`)
        }
    }
}

/**
 * API 호출
 * 데이터 수정을 완료 기능
 * @param {string}id
 */
function confirmInputPopupEdit(key, id) {
    const className = initContainer[key].className;
    if (!initContainer[key].getUpdateUrl) return;
    let data = parseInputToData($(`.${className} .editable`))
    $(`.${className} .error-message-wrap`).empty();
    apiRequest({
        type: 'POST',
        url: initContainer[key].getUpdateUrl(id),
        data: data,
        dataType: 'json',
        success: getSuccessCallback(className),
        error: getErrorCallback(className),
    });
}

/**
 * API 호출
 * 데이터 생성을 완료 기능
 * @param {string}id
 */
function confirmInputPopupCreate(key = 'default') {
    const className = `${key}-popup-create`;
    if (!initContainer[key].getCreateUrl) return;
    let data = parseInputToData($(`.${className} .editable`))

    $(`.${className} .error-message-wrap`).empty();
    apiRequest({
        type: 'POST',
        url: initContainer[key].getCreateUrl(),
        data: data,
        dataType: 'json',
        success: getSuccessCallback(className),
        error: getErrorCallback(className),
    });
}

/**
 * API 호출
 * 데이터 삭제 기능
 * @param {string}id
 */
function confirmInputPopupDelete(key, id) {
    const className = `${key}-popup-delete`;
    if (!initContainer[key].getDeleteUrl) return;
    apiRequest({
        type: 'DELETE',
        url: initContainer[key].getDeleteUrl(id),
        dataType: 'json',
        success: getSuccessCallback(className),
        error: getErrorCallback(className),
    });
}
