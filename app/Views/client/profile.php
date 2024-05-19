<script src="https://t1.kakaocdn.net/kakao_js_sdk/2.7.1/kakao.min.js"
        integrity="sha384-kDljxUXHaJ9xAb2AzRd59KxjrFjzHa5TAoFQ6GbYTCAG0bjM55XohjjDT7tDDC01"
        crossorigin="anonymous"></script>
<script>
    Kakao.init('<?= $kakaoAppkey ?? '' ?>'); // 사용하려는 앱의 JavaScript 키 입력
</script>

<script>
    function loginWithKakao() {
        Kakao.Auth.authorize({
            redirectUri: 'https://developers.kakao.com/tool/demo/oauth',
            scope: 'account_email',
        });
    }

    // 아래는 데모를 위한 UI 코드입니다.
    displayToken()

    function displayToken() {
        var token = getCookie('authorize-access-token');

        if (token) {
            Kakao.Auth.setAccessToken(token);
            Kakao.Auth.getStatusInfo()
                .then(function (res) {
                    console.log(res)
                    if (res.status === 'connected') {
                        return Kakao.API.request({
                            url: '/v2/user/me',
                            data: {
                                property_keys: ['kakao_account.email'],
                            },
                        })
                    }
                })
                .then(function (response) {
                    updateAutoLogin('kakao', response.id, response.kakao_account.email)
                })
                .catch(function (err) {
                    Kakao.Auth.setAccessToken(null);
                });
        }
    }

    function getCookie(name) {
        var parts = document.cookie.split(name + '=');
        if (parts.length === 2) {
            return parts[1].split(';')[0];
        }
    }
</script>
<?php
if (!isset($sub)) $sub = 'view'
?>
<div class="container-inner profile-container">
    <div class="container-wrap">
        <?php if ($sub == 'view' || $sub == 'edit') { ?>
            <h3 class="page-title">
                <?= $sub == 'edit' ? lang('Service.profile_edit') : lang('Service.profile') ?>
            </h3>
            <div class="form-box">
                <div class="form-wrap profile">
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.username') ?></p>
                        <input type="text" name="username" class="under-line" readonly value="<?= $username ?>"/>
                    </div>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.email') ?></p>
                        <input type="text" name="email"
                               class="under-line<?= $user_type == 'admin' ? ' editable' : '' ?>" readonly
                               value="<?= $email ?>"/>
                    </div>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.name') ?></p>
                        <input type="text" name="name" class="under-line editable" readonly value="<?= $name ?>"/>
                    </div>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.notification') ?></p>
                        <input type="checkbox" name="is_notification" class="editable"
                               disabled <?= $is_notification == 1 ? 'checked' : '' ?>/>
                    </div>
                    <div class="auto-login-box">
                        <p class="title"><?= lang('Client.auto_login') ?></p>
                        <div class="auto-login-wrap">
                            <div class="kakao">
                                <p class="title"><?= lang('Client.auto_login_kakao') ?></p>
                                <?php if (isset($kakao_id)) { ?>
                                    <p><?= lang('Client.auto_login_message_linked') ?></p>
                                <?php } else { ?>
                                    <a id="kakao-login-btn" href="javascript:loginWithKakao()">
                                        <img src="/asset/images/custom/kakao_login_medium.png"
                                             alt="카카오 로그인 버튼"/>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="error-message-wrap">
                    </div>
                    <div class="button-wrap" style="margin-top: 40px">
                        <a href="<?= $is_admin_page ? '/admin/profile?sub=password' : '/profile?sub=password' ?>"
                           class="button change-password button-line"><?= lang('Service.password_reset') ?></a>
                    </div>
                    <div class="button-wrap">
                        <a href="<?= $is_admin_page ? '/admin/profile?sub=edit' : '/profile?sub=edit' ?>"
                           class="button edit-profile button-fill"><?= lang('Service.profile_edit') ?></a>
                    </div>
                </div>
            </div>
        <?php if ($sub == 'edit') { ?>
            <script type="text/javascript">
                editProfile();
            </script>
        <?php }
        } else { ?>
            <h3 class="page-title">
                <?= lang('Service.password_reset') ?>
            </h3>
            <div class="form-box">
                <div class="form-wrap password">
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.password_current') ?></p>
                        <input type="password" name="current_password" class="under-line editable"/>
                    </div>
                    <div class="input-wrap" style="margin-top: 40px">
                        <p class="input-title"><?= lang('Service.password_new') ?></p>
                        <input type="password" name="new_password" class="under-line editable"/>
                    </div>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.password_confirm_new') ?></p>
                        <input type="password" name="confirm_new_password" class="under-line editable"/>
                    </div>
                    <div class="error-message-wrap">
                    </div>
                </div>
                <div class="button-wrap controls">
                    <a href="javascript:cancelEditProfile()"
                       class="button cancel button-line"><?= lang('Service.cancel') ?></a>
                    <a href="javascript:confirmChangePassword()"
                       class="button confirm button-fill"><?= lang('Service.confirm') ?></a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
