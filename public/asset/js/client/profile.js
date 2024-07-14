/**
 * @file profile.php
 * 한 파일에서 처리하지만 query parameter 를 이용해
 * location 을 바꿔가며 뒤로가기 버튼으로 각 step 을 탐색가능하게 구현함
 */

/**
 * [profile] step#01
 * 입력 가능 페이지로 전환하는 기능
 */
function editProfile() {
    $('.form-wrap .editable').removeAttr('readonly')
    $('.form-wrap .editable').removeAttr('disabled')
    $('.form-wrap .button-wrap').remove();
    $('.form-box').append(`
    <div class="button-wrap controls">
        <a href="javascript:cancelEditProfile()" class="button cancel button-line">${lang('cancel')}</a>
        <a href="javascript:confirmEditProfile()" class="button confirm button-fill">${lang('confirm')}</a>
    </div>`);
}

/**
 * [profile] -
 * 뒤로가기 기능
 */
function cancelEditProfile() {
    history.back()
}

/**
 * [profile] step#02
 * 프로필 변경 완료 기능
 */
function confirmEditProfile() {
    clearErrors();
    let data = parseInputToData($(`#container .form-wrap .editable`))

    apiRequest({
        type: 'POST',
        url: `/api/user/update/profile`,
        data: data,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                showErrors(response, status, request);
                return;
            }
            history.back();
        },
        error: function (response, status, error) {
            showErrors(response, status, error);
        },
    });
}

/**
 * [change password] step#01
 * password 변경 완료 기능
 */
function confirmChangePassword() {
    clearErrors();

    let data = parseInputToData($(`#container .form-wrap .editable`))

    // let $wrapErrorMessage = $('#container .form-box .error-message-wrap');
    // let validations = [
    //     {
    //         key: 'current_password',
    //         name: 'Current Password'
    //     },
    //     {
    //         key: 'new_password',
    //         name: 'New Password'
    //     },
    //     {
    //         key: 'confirm_new_password',
    //         name: 'Confirm New Password'
    //     },
    // ];
    //
    // for (let i in validations) {
    //     let validation = validations[i];
    //     if (isEmpty(data[validation.key])) {
    //         $wrapErrorMessage.append(`<p>${validation.name} field is empty.</p>`)
    //     }
    // }
    //
    // if (data['current_password'] == data['new_password']) {
    //     $wrapErrorMessage.append(`<p>please enter different password with original.</p>`)
    // }
    // if (data['new_password'] != data['confirm_new_password']) {
    //     $wrapErrorMessage.append(`<p>please check two fields for password is same.</p>`)
    // }

    apiRequest({
        type: 'POST',
        url: `/api/user/password-change`,
        dataType: 'json',
        data: data,
        success: function (response, textStatus, request) {
            if (!response.success) {
                showErrors(response, textStatus, request);
                return;
            }
            history.back();
        },
        error: function (response, textStatus, error) {
            showErrors(response, textStatus, error);
        },
    })
}

/**
 * 에러 출력 기능
 */
function showErrors(response, status, requestOrError) {
    let $wrapErrorMessage = $('#container .form-box .error-message-wrap');
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

/**
 * 출력한 에러 지우는 기능
 */
function clearErrors() {
    let $wrapErrorMessage = $('#container .form-box .error-message-wrap');
    $wrapErrorMessage.empty();
}

/**
 * 자동 로그인 내용 업데이트
 * 기존의 /api/user/update/profile API 를 사용하지만
 * validation 을 위해 함수 분리
 * @param channel
 * @param channel_id
 */
function updateAutoLogin(channel = 'kakao', channel_id) {
    clearErrors();

    apiRequest({
        type: 'POST',
        url: `/api/user/update/profile`,
        data: {
            channel : channel,
            channel_id : channel_id
        },
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                showErrors(response, status, request);
                return;
            }
            window.location.reload();
        },
        error: function (response, status, error) {
            showErrors(response, status, error);
        },
    });
}
