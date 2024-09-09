/**
 * @file login.php 과 admin 용 페이지를 제외한 전체 페이지
 */

/**
 * login 가능한 popup 을 여는 기능
 * 모든 페이지에서 login 버튼을 이용해 호출할 수 있도록 함
 * @returns {Promise<void>}
 * @throws {Response}           fetch 로 파일읽기에 실패했을 경우 결과를 throw
 * todo(log) throw 에 잡힌 경우 log 로 남길 필요 있음
 */
async function openPopupLogin() {
    try {
        let className = 'popup-login';
        let css = '';
        css += await loadStyleFile('/asset/css/common/input.css', "." + className);
        css += await loadStyleFile('/asset/css/client/login.css', "." + className);
        let style = `
        <style>
            ${css}
            .${className} .popup {
                width: 600px;
            }
            .${className} .popup-inner {
                padding: 20px;
            }
            .${className} .login-container {
                line-height: normal;
            }
        </style>`;
        let html = `
        <div class="login-container">
            <h3 class="popup-title">
                ${lang('login')}
            </h3>
            <div class="form-wrap">
                <div class="input-wrap">
                    <p class="input-title">${lang('email')}</p>
                    <input type="text" name="email" class="under-line"/>
                </div>
                <div class="input-wrap">
                    <p class="input-title">${lang('password')}</p>
                    <input type="password" name="password" class="under-line"/>
                </div>
            </div>
            <div class="control-button-wrap">
                <a href="/registration"
                    class="button under-line register">
                    <img src="/asset/images/icon/plus_circle.png"/>
                    <span>${lang('register')}</span>
                </a>
            </div>
            <div class="control-button-wrap">
                <a href="/reset-password"
                    class="button under-line forgot-password">
                    <img src="/asset/images/icon/password.png"/>
                    <span>${lang('password_forget')}</span>
                </a>
            </div>
            <div class="error-message-wrap">
            </div>
            <div class="button-wrap controls">
                <a href="javascript:closePopup('${className}')"
                    class="button cancel white">${lang('cancel')}</a>
                <a href="javascript:login('${className}')"
                    class="button confirm black">${lang('login')}</a>
            </div>
        </div>`;
        openPopup({
            className: className,
            style: style,
            html: html,
            onEnterKeydown: function () {
                let button = $(`.${className} .button.confirm`).get(0);
                if (button) {
                    button.click();
                }
            },
        })
    } catch (e) {
        // do nothing
    }
}

/**
 * 로그인 기능
 * @param className
 */
function login(className) {
    clearErrorsByClassName(className);

    let data = parseInputToData($(`.${className} .form-wrap input`))

    apiRequest({
        type: 'POST',
        url: `/api/user/login`,
        data: data,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                showErrorsByClassName(className, response, status, request);
                return;
            }
            location.reload();
        },
        error: function (response, status, error) {
            showErrorsByClassName(className, response, status, error);
        },
    });
}

/**
 * 자동 로그인 기능
 * 연결 후 시스템 내에 id 없을 경우 register 화면으로 감
 * @param className
 * @param channel
 * @param channel_id
 * @param email
 */
function autoLogin(className, channel = 'kakao', channel_id, email, name) {
    clearErrorsByClassName(className);

    apiRequest({
        type: 'POST',
        url: `/api/user/auto-login`,
        data: {
            channel: channel,
            channel_id: channel_id,
            email: email,
            name: name
        },
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                if (response.message == 'user is not registered.') {
                    location.href = `/registration?email=${email}&name=${name??''}&channel=${channel}&channel_id=${channel_id}`
                } else {
                    showErrorsByClassName(className, response, status, request);
                }
                return;
            }
            location.reload();
        },
        error: function (response, status, error) {
            showErrorsByClassName(className, response, status, error);
        },
    });
}

/**
 * 로그아웃 기능
 */
function logout() {
    apiRequest({
        type: 'POST',
        url: `/api/user/logout`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) return;
            location.reload();
        },
        error: function (response, status, error) {
        },
    });
}

$(document).ready(function () {
    // enter 및 esc 키 처리
    let element = $(`.login-container`).get(0);
    if (element) {
        element.addEventListener('keydown', function (event) {
            switch (event.key) {
                case 'Escape':
                    closePopup('popup-login');
                    break;
                case 'Enter':
                    let button = $(`.login-container .button.confirm`).get(0);
                    if (button) {
                        button.click();
                    }
                    break;
            }
        })
    }
})
