/**
 * @file 공통 javascript 함수 스크립트
 */

/**
 * textarea 에서 newline 표현을 위해 string 을 변환하는 기능
 * @returns {string}
 */
String.prototype.toTextareaString = function () {
    return this.replace(/(\\n|\n)/g, '&#10;')
}

/**
 * input 에서 값을 가져올 시 데이터베이스에 저장을 위해 string 을 변환하는 기능
 * @returns {string}
 */
String.prototype.toRawString = function () {
    return this.replace(/(\&\#10\;)/g, '\n')
}

function hash() {
    return Math.random().toString(36).substr(2, 11)
}

function isEmpty(value) {
    return !value || value.length == 0
}

function openWindow(url) {
    window.open(url)
}

function toFormatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}

function toDateString(dateString) {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        return `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`
    } catch (e) {
        return '';
    }
}

function secToString(sec) {
    if (!sec) return '0:00';
    try {
        const minutes = Math.floor(sec / 60);
        const seconds = sec % 60;
        return `${minutes}:${seconds.toString().padStart(2, '0')}`;
    } catch (e) {
        return '0:00';
    }
}

function getCookie(cookie_name) {
    let x, y;
    let val = decodeURIComponent(document.cookie).split(';');
    for (let i = 0; i < val.length; i++) {
        x = val[i].substring(0, val[i].indexOf('='));
        y = val[i].substring(val[i].indexOf('=') + 1);
        x = x.replace(/^\s+|\s+$/g, '');
        // 앞과 뒤의 공백 제거하기
        if (x == cookie_name) {
            return y;
        }
    }
}

function clearErrorsByClassName(className) {
    let $wrapErrorMessage = $(`.${className} .error-message-wrap`);
    $wrapErrorMessage.empty();
}

function showErrorsByClassName(className, response, status, requestOrError) {
    let $wrapErrorMessage = $(`.${className} .error-message-wrap`);
    $wrapErrorMessage.empty();
    if (status == 'success' || status >= 200 && status < 300) {
        if (response.messages) {
            for (let key in response.messages) {
                let message = response.messages[key];
                $wrapErrorMessage.append(`<div>${message}</div>`);
            }
        }
        if (response.message) {
            $wrapErrorMessage.append(`<div>${response.message}</div>`);
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
            $wrapErrorMessage.append(`<div>${message}</div>`);
        }
    }
}

function parseInputToData($inputs) {
    let data = {};
    for (let i = 0; i < $inputs.length; ++i) {
        let $input = $inputs.eq(i);
        if ($input.length > 0) {
            let domElement = $input[0]
            if (domElement.name) {
                if (domElement.tagName == 'textarea') {
                    data[domElement.name] = domElement.value.toRawString();
                } else if (domElement.type == 'checkbox') {
                    data[domElement.name] = domElement.checked ? 1 : 0;
                } else if (domElement.type == 'radio') {
                    if (domElement.checked) {
                        data[domElement.name] = domElement.value;
                    }
                } else {
                    data[domElement.name] = $input.val();
                }
            }
        }
    }
    return data;
}

/**
 * textarea 자동 높이 조절 기능
 * @param obj
 */
function resizeInputPopupTextarea(obj) {
    //todo check in mobile
    let maxHeight = 80;
    obj.style.overflow = 'hidden'
    if ((obj.innerHeight === undefined && obj.scrollHeight >= maxHeight) || obj.innerHeight < maxHeight) {
        obj.style.height = "1px";
        let height = 5 + obj.scrollHeight;
        obj.style.height = `${height}px`;
    } else {
        obj.style.height = `${maxHeight}px`;
    }
}

async function apiRequest(input) {
    let timeoutId = setTimeout(function () {
        $(`.loading-wrap`).css({
            display: 'block'
        })
        clearTimeout(timeoutId);
    }, 200);
    await $.ajax({
        type: input.type,
        data: input.data,
        dataType: input.dataType,
        url: input.url,
        processData: input.processData,
        contentType: input.contentType,
        cache: input.cache,
        headers: input.headers,
        success: function (response, status, request) {
            clearTimeout(timeoutId);
            if (input.success && typeof input.success == 'function') {
                input.success(response, status, request);
            }
            $(`.loading-wrap`).css({
                display: 'none'
            })
        },
        error: function (response, status, error) {
            clearTimeout(timeoutId);
            if (input.error && typeof input.error == 'function') {
                input.error(response, status, error);
            }
            $(`.loading-wrap`).css({
                display: 'none'
            })
        },
    });
}

/**
 * 동일한 내용의 css 파일을 특정 object 하위에서만 작동하게 하기 위해 자동으로 selector 를 붙여주는 기능
 * animation 은 내장된 css 에 따로 선언 필요
 *
 * 페이지 당 한 번 추가되면 좋겠지만, 다른 class 에 영향 줄 수 있고
 * 팝업이 생성과 제거를 반복할 것이기 때문에
 * 아예 팝업 당 고유 className을 주고 해당 class를 가진 element 내부에 추가해 생성과 소멸을 같이 할 수 있도록 함
 * @param path
 * @param selector
 * @returns {Promise<string>}
 */
async function loadStyleFile(path, selector) {
    if (isEmpty(path) || isEmpty(selector)) return '';
    let request = await fetch(path)
    if (!request.ok) throw request;
    let css = await request.text()

    function parseWithIndicator(css) {
        let blocks = css.split(/(?<=})\s+/);
        let result = '';
        blocks.forEach((value) => {
            if (!isEmpty(value)) {
                if (value.startsWith("@")) {
                    result += value + "\n";
                } else {
                    {
                        let regex = /((.|s+|\n)*)({(.|s+|\n)*})/.exec(value);
                        let indicator = regex[1];
                        let block = regex[3];
                        if (isEmpty(indicator)) {
                            result += selector + " " + block + "\n";
                        } else {
                            let indicators = indicator.split(",");
                            let prefix = "";
                            for (let i in indicators) {
                                let indicator_text = indicators[i].replaceAll('\n', '');
                                result += prefix + selector + " " + indicator_text;
                                prefix = ",\n";
                            }
                            result += " " + block + "\n";
                        }
                    }
                }
            }
        })
        return result;
    }

    let mediaResult = '';
    {
        let regex;
        do {
            try {
                regex = /(@media[^{]+\{([\s\S]+?})\s*})/.exec(css);
                if (regex) {
                    css = css.replace(regex[0], "");
                    mediaResult += regex[0].replace(regex[2], "\n" + parseWithIndicator(regex[2])) + "\n";
                }
            } catch (e) {
                regex = null;
            }
        } while (regex)
    }
    let result = parseWithIndicator(css);
    result += mediaResult;
    return result;
}


/**
 * 로그아웃 기능
 */
function logout() {
    apiRequest({
        type: 'POST', url: `/api/user/logout`, dataType: 'json', success: function (response, status, request) {
            if (!response.success) return;
            location.reload();
        }, error: function (response, status, error) {
        },
    });
}

/**
 * php 에서 시스템 설정에 맞는 번역 테스트 js 로 재등록, 호출하는 기능
 * @type {{}}
 */
let translations = {};

function lang(key) {
    if (isEmpty(translations[key])) return key;
    return translations[key];
}

function clickClientNavigation(element, link) {
    //todo 모바일인지 구분하는 더 나은 방법이 있는지 고민 필요
    if (!isMobile()) {
        window.location.href = link;
        return;
    }
    let $lnb = $(element).parent().find('.lnb');

    if (element.classList.contains('gnb-menu') && $lnb.length > 0 && $lnb.children().length > 0) {
        if ($lnb.hasClass('opened')) {
            $lnb.removeClass('opened');
        } else {
            $lnb.addClass('opened')
        }
    } else {
        window.location.href = link;
        closeNavigation(false);
    }
}

/**
 * mobile resolution 체크
 * 추후 기준너비 바꿀 때 한곳에서 관리하고자 추가
 * @returns {boolean}
 */
function isMobile() {
    return window.innerWidth < 840
}

/**
 * mobile resolution trigger 확인 기능
 */
let $resolutionChangeItems = [];
$(document).ready(function () {
    if (isMobile() && !$('body').hasClass('mobile')) {
        // mobile 로 전환
        // 첫 load 때 모바일인 경우 호출됨
        $('body').addClass('mobile');
    }
    setPosterForVideo($('video'));
});

/**
 * mobile resolution trigger 확인 기능
 * resize event listener 등록
 * 해당 함수를 1번만 호출하기 위해 body 에 class 를 설정하여 체크 후 호출
 */
addEventListener("resize", (event) => {
    if (isMobile() && !$('body').hasClass('mobile')) {
        // mobile 로 전환
        // 첫 load 때 모바일인 경우 호출됨
        $('body').addClass('mobile');
        dispatchOnDeviceResolutionChanged(true);
    }
    if (!isMobile() && $('body').hasClass('mobile')) {
        // pc 로 전환
        $('body').removeClass('mobile');
        dispatchOnDeviceResolutionChanged(false);
    }
});

/**
 * mobile resolution trigger 확인 기능
 * event listener 처리 등록
 */
jQuery.prototype.setOnResolutionChanged = function (callback) {
    $resolutionChangeItems.push(this)
    let $item = this.get(0);
    if ($item) {
        let timeoutId = setTimeout(() => {
            callback({
                detail: {
                    isMobile: isMobile()
                },
            });
            clearTimeout(timeoutId);
        }, 50);
        $item.addEventListener('onDeviceResolutionChanged', (event) => {
            callback(event);
        })
    }
}

/**
 * mobile resolution trigger 확인 기능
 * event listener 처리 호출
 */
function dispatchOnDeviceResolutionChanged(isMobile = false) {
    let event = new CustomEvent("onDeviceResolutionChanged", {
        cancelable: true, // cancelable를 true로 설정하지 않으면 preventDefault가 동작하지 않습니다.
        detail: {
            isMobile: isMobile
        },
    });

    $resolutionChangeItems = $.grep($resolutionChangeItems, n => n == 0 || n);

    for (let i = 0; i < $resolutionChangeItems.length; ++i) {
        let $item = $resolutionChangeItems[i].get(0);
        if ($item) {
            $item.dispatchEvent(event)
        }
    }
}

function onLanguageChanged(element) {
    apiRequest({
        type: 'GET',
        url: `/api/session/lang/${$(element).val()}`,
        dataType: 'json',
        success: async function (response, status, request) {
            location.reload();
        },
    });
}

function sprintf(template, ...values) {
    return template.replace(/%s/g, function () {
        return values.shift();
    });
}

function checkLogin(link) {
    if (getCookie('is_login') != 1) {
        openPopupMessage(lang('message_error_login'), '/login')
        return
    }
    window.location.href = link;
}

function setPosterForVideo($videos) {
    for (let i = 0; i < $videos.length; ++i) {
        const $video = $videos.eq(i);
        if (!$video.attr('poster') || $video.attr('poster') == 'null') {
            $video[0].onloadeddata = function () {
                // console.log(this)
                this.setAttribute("poster", createPoster(this));
                // alert("Browser has loaded the current frame");
            };
        }
    }
}

function createPoster(video, width = null, height = null) {
    //here you can set anytime you want
    video.currentTime = 0;
    const $video = $(video);
    const canvas = document.createElement("canvas");
    canvas.width = width ? width : $video.width();
    canvas.height = height ? height : $video.height();
    canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
    return canvas.toDataURL("image/jpeg");
}

function getLangItem(item, name, lang) {
    if (!item) return null;
    if (lang == 'en') {
        name += '_en';
    } else if (lang == 'jp') {
        name += '_jp';
    }
    return item[name] ?? "";
}
