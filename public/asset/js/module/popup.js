/**
 * @file popup 용 공통 기능 스크립트
 */

/**
 * popup 열기 기능
 * selector 를 공통으로 주면 함수가 전체 같은 동작이 적용되므로
 * 1. event 가 들어온 element 의 제시된 조건에 해당하는 모든 parent 를 검사해서 해당 element 제어
 *   (ex. close 시 해당 root element 제거)
 * 2. 생성될 때 추가 selector 를 설정
 * 2안에서 스타일 독립성도 유지할 수 있고 recursive 를 사용하지 않아도 때문에 2안으로 결정
 * 현재는 popup 종류가 동일한게 동시에 뜰 필요가 없지만, 필요한 경우 selector 를 class 가 아닌 id 로 바꾸어주어야한다
 * @param input
 */
function openPopup(input, callback) {
    // 각 팝업을 각각 제어하기 위해 주는 selector name
    let className = input.className;
    let commonClassName = input.commonClassName;
    let style = input.style;
    let html = input.html;
    // 팝업 세팅 완료 후 수행되어져야 할 기능 callback
    let onEnterKeydown = input.onEnterKeydown;

    $('body').append(`
    <div class="popup-wrap ${className} ${commonClassName ?? ''}">
        ${style ?? ''}
        <div class="popup">
            <div class="interface">
                <a href="javascript:closePopup('${className}')" class="button close">
                    <img src="/asset/images/icon/cancel_white.png"/>
                </a>
            </div>
            <div class="popup-box">
                <div class="popup-inner">
                    <div class="popup-inner-wrap">
                        ${html ?? ''}
                    </div>
                </div>
            </div>
        </div>
    </div>`)
    onPopupResizeWindow();
    let $parent = $(`.${className}`);
    if (callback && typeof callback == 'function') callback($parent);

    if ($parent.find(`.control-button-wrap`).length > 0) {
        $parent.find(`.popup-box`).addClass('has-control-button');
    }

    // popup 내부의 tab 처리
    let $tabbable = $parent.find("input:not([type='hidden']), select, textarea, [href]");
    let $tabbableFirst = $tabbable && $tabbable.first();

    let elementBody = document.querySelector('body');
    elementBody.addEventListener("scroll touchmove mousewheel", onPopupScroll);

    for (let i = 0; i < $tabbable.length; ++i) {
        $tabbable.eq(i).attr('index', i);
    }
    $tabbable.on("keydown", function (event) {
        // 레이어 열리자마자 초점 받을 수 있는 첫번째 요소로 초점 이동
        let tabIndex = this.getAttribute('index');
        if ((event.keyCode || event.which) === 9) {
            event.preventDefault();
            tabIndex++;
            if (tabIndex >= $tabbable.length) {
                tabIndex = 0;
            }
            $tabbable.get(tabIndex).focus();
        }
    })

    $('#container').find("button, input:not([type='hidden']), select, iframe, textarea, [href], [tabindex]:not([tabindex='-1']), .slick-item").attr("tabindex", "-1"); // 레이어 바깥 영역을 스크린리더가 읽지 않게
    if ($tabbableFirst) $tabbableFirst.focus();

    let element = $parent.get(0);
    if (element) {
        element.addEventListener('click', function (event) {
            if (event.target.className && event.target.className.includes && event.target.className.includes("popup-wrap")) {
                closePopup(className)
            }
        })

        element.addEventListener('keydown', function (event) {
            switch (event.key) {
                case 'Escape':
                    closePopup(className);
                    break;
                case 'Enter':
                    if (onEnterKeydown && typeof onEnterKeydown == 'function') onEnterKeydown();
                    break;
            }
        })
    }

    addEventListener("resize", onPopupResizeWindow);
}

/**
 * popup 끄기 기능
 * @param className
 */
function closePopup(className) {
    let $parent;
    if (!className) {
        $parent = $(`.popup-wrap`);
    } else {
        $parent = $(`.${className}`)
        $parent.find('.popup').css({
            'animation-duration': '0.2s',
            'animation-name': 'popupFadeOut',
        })
    }

    removeEventListener('resize', onPopupResizeWindow);

    // popup 내부의 tab 처리를 위해 설정했던 것 되돌림
    let elementBody = document.querySelector('body');
    elementBody.removeEventListener("scroll touchmove mousewheel", onPopupScroll);

    $('#container').find("button, input:not([type='hidden']), select, iframe, textarea, [href], [tabindex]:not([tabindex='-1']), .slick-item").attr("tabindex", ""); // 레이어 바깥 영역을 스크린리더가 읽지 않게

    let popupTimeoutId = setTimeout(function () {
        let element = $parent.get(0);
        if (element) {
            element.removeEventListener('click', closePopup);
        }
        $parent.remove()
        clearTimeout(popupTimeoutId)
    }, 150)
}

/**
 * resize 시 호출 되는 기능
 * popup 의 세로 가운데 정렬을 위해 popup 의 root 영역에
 * line-height 를 화면에 비례해 조정해 준다
 * @param event
 */
function onPopupResizeWindow(event) {
    $('.popup-wrap').css({
        'line-height': `${window.innerHeight}px`,
    })
    let popup_height = window.innerHeight - 400 < 200 ? 200 : window.innerHeight - 400
    $('.popup-wrap .popup-inner').not('.popup-wrap.popup-image-detail .popup-inner').css({
        'max-height': `${popup_height}px`,
    })
    popup_height = window.innerHeight - 200 < 200 ? 200 : window.innerHeight - 200
    $('.popup-wrap.popup-image-detail .popup-inner').css({
        'max-height': `${popup_height}px`,
    })
}

function onPopupScroll(event) {
    event.preventDefault(); // iOS 레이어 열린 상태에서 body 스크롤되는 문제 방지
}

/**
 * error popup 열기 기능
 * @param className
 * @param response
 * @param status
 * @param requestOrError
 */
function openPopupErrors(className, response, status, requestOrError) {
    let style = `
        <style>
        .${className} .popup {
            width: 500px;
        }

        .${className} .popup-inner .error-message-wrap {
            padding: 20px 0;
        }

        .${className} .popup-inner .button-wrap {
            margin-top: 20px;
        }

        .${className} .popup-inner .button-wrap .button {
            min-width: 100px;
            padding: 10px 20px;
            margin: 0 10px;
        }
        
        @media (max-width: 840px) {
            .${className} .popup {
                width: calc(100% - 20px);
            }
        }
        </style>`
    let hasMessage = false;
    let html = `
        <div class="error-message-wrap">`
    if (status == 'success' || status >= 200 && status < 300) {
        if (response.messages) {
            for (let key in response.messages) {
                let message = response.messages[key];
                html += `<div>${message}</div>`
                hasMessage = true;
            }
        }
        if (response.message) {
            html += `<div>${response.message}</div>`
            hasMessage = true;
        }
    } else {
        let message = requestOrError;
        try {
            let errorObject = JSON.parse(response.responseText);
            if (errorObject.message) {
                message = errorObject.message
            }
        } catch (e) {
            // do nothing
        }
        if (message) {
            html += `<div>${message}</div>`
            hasMessage = true;
        }
    }
    html += `
        </div>
        <div class="button-wrap controls">
            <a href="javascript:closePopup('${className}')" class="button ok white">OK</a>
        </div>`;
    if (hasMessage) {
        openPopup({
            className: className,
            style: style,
            html: html,
        });
    }
}

/**
 * user 상세 popup 을 여는 기능
 * @param id
 */
function openUserPopup(user_id) {
    apiRequest({
        type: 'GET',
        url: `/api/user/get/${user_id}`,
        dataType: 'json',
        success: async function (response, status, request) {
            if (!response.success)
                return;
            let data = response.data;
            let className = 'popup-user-detail';
            let css = await loadStyleFile('/asset/css/common/input.css', "." + className);
            css += await loadStyleFile('/asset/css/common/popup/user.css', "." + className);

            let typeSet = {
                type: {
                    type: 'text',
                    name: lang('type'),
                },
                username: {
                    type: 'text',
                    name: lang('username'),
                },
                name: {
                    type: 'text',
                    name: lang('name'),
                },
                email: {
                    type: 'text',
                    name: lang('email'),
                },
            }
            let keys = Object.keys(typeSet);
            let html = ``;

            for (let i in keys) {
                let key = keys[i];
                let name = key;
                let set = typeSet[key];
                if (set) {
                    if (set['name']) {
                        name = set['name'];
                    } else {
                        if (name.startsWith('is_')) {
                            name = name.replace('is_', '');
                        }
                        name = name.charAt(0).toUpperCase() + name.slice(1);
                    }
                }
                let value = data[key];
                html += `
                    <div class="text-wrap">
                        <p class="title">${name}</p>
                        <p class="value">${value}</p>
                    </div>`
            }
            openPopup({
                className: className,
                style: `<style>${css}</style>`,
                html: html,
            })
        },
        error: function (response, status, error) {
        },
    });
}

function openImagePopup(id) {
    let className = 'popup-image-detail';
    let style = `
    <style>
    .${className} .popup-inner .image-wrap * {
        width: 100%;
    }
    </style>`
    let html = `<div class="image-wrap">
            <img src='/file/${id}'/>
        </div>`;
    openPopup({
        className: className,
        style: style,
        html: html,
    })
}


function openVideoPopup(id, type = 'image', mime_type) {
    let className = 'popup-image-detail';
    let style = `
    <style>
    .${className} .popup-inner .image-wrap * {
        width: 100%;
    }
    </style>`
    let html = `<div class="image-wrap">
        <video controls>
            <source src="/file/${id}" type="${mime_type}">
        </video>
        </div>`;
    openPopup({
        className: className,
        style: style,
        html: html,
    })
}

async function openPopupMessage(message) {
    let className = `popup-message`;
    let style = `
        <style>
        .${className} .popup {
            width: 500px;
        }

        .${className} .popup-inner .error-message-wrap {
            padding: 20px 0;
        }

        .${className} .popup-inner .button-wrap {
            margin-top: 20px;
        }

        .${className} .popup-inner .button-wrap .button {
            min-width: 100px;
            padding: 10px 20px;
            margin: 0 10px;
        }
        
        @media (max-width: 840px) {
            .${className} .popup {
                width: calc(100% - 20px);
            }
        }
        </style>`;
    let html = `
    <div class="error-message-wrap">
        <div>${message}</div>
    </div>
    <div class="button-wrap controls">
        <a href="javascript:closePopup('${className}')" class="button cancel white">${lang('확인')}</a>
    </div>`;
    openPopup({
        className: className,
        style: style,
        html: html,
    })
}
