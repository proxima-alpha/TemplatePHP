<script src="https://t1.kakaocdn.net/kakao_js_sdk/2.7.1/kakao.min.js"
        integrity="sha384-kDljxUXHaJ9xAb2AzRd59KxjrFjzHa5TAoFQ6GbYTCAG0bjM55XohjjDT7tDDC01"
        crossorigin="anonymous"></script>
<script type="text/javascript" src="https://static.nid.naver.com/js/naverLogin_implicit-1.0.3.js"
        charset="utf-8"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
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
                            <?php if (isset($kakao_id)) { ?>
                                <div>
                                    <p class="title"><?= lang('Client.auto_login_kakao') ?></p>
                                    <p><?= lang('Client.auto_login_message_linked') ?></p>
                                </div>
                            <?php } else if (isset($naver_id)) { ?>
                                <div>
                                    <p class="title"><?= lang('Client.auto_login_naver') ?></p>
                                    <p><?= lang('Client.auto_login_message_linked') ?></p>
                                </div>
                            <?php } else if (isset($google_id)) { ?>
                                <p class="title"><?= lang('Client.auto_login_google') ?></p>
                                <p><?= lang('Client.auto_login_message_linked') ?></p>
                            <?php } else { ?>
                                <p class="title"><?= lang('Client.auto_login_register') ?></p>
                                <div id="naver_id_login" class="auto-login-button"></div>
                                <a id="kakao-login-btn" class="auto-login-button"
                                   href="javascript:loginWithKakao()">
                                    <img src="/asset/images/custom/login_kakao.png"
                                         alt="카카오 로그인 버튼"/>
                                </a>
                                <a id="google-login-btn" class="auto-login-button" href="javascript:oauthSignIn()">
                                    <img src="/asset/images/custom/login_google.png"
                                         alt="구글 로그인 버튼"/>
                                </a>
                            <?php } ?>
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

<?php if (!isset($kakao_id) && !isset($naver_id) && !isset($google_id)) { ?>
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
                        const id = response.id;
                        updateAutoLogin('kakao', id)
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

    <script type="text/javascript">
        async function naverSignInCallback() {
            const id = naver_id_login.getProfileData('id');
            updateAutoLogin('naver', id)
        }

        var naver_id_login = new naver_id_login('<?= $naverClientId ?? '' ?>', "<?=$_ENV['app.baseURL']?>" + "profile");
        var state = naver_id_login.getUniqState();
        naver_id_login.setButton("white", 2, 40);
        naver_id_login.setDomain("<?=$_ENV['app.baseURL']?>");
        naver_id_login.setState(state);
        // naver_id_login.setPopup();
        naver_id_login.init_naver_id_login();
        $('#naver_id_login a').empty();
        $('#naver_id_login a').append(`
        <img src="/asset/images/custom/login_naver.png"
         alt="네이버 로그인 버튼"/>`);

        if (window.location.hash && window.location.hash.startsWith("#access_token")) {
            try {
                // 네이버 사용자 프로필 조회 이후 프로필 정보를 처리할 callback function
                naver_id_login.get_naver_userprofile("naverSignInCallback()");
            } catch (e) {
                // do nothing
            }
        }
    </script>

    <script type="text/javascript">
        /*
     * Create form to request access token from Google's OAuth 2.0 server.
     */
        function oauthSignIn() {
            // Google's OAuth 2.0 endpoint for requesting an access token
            var oauth2Endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';

            // Create <form> element to submit parameters to OAuth 2.0 endpoint.
            var form = document.createElement('form');
            form.setAttribute('method', 'GET'); // Send as a GET request.
            form.setAttribute('action', oauth2Endpoint);

            // Parameters to pass to OAuth 2.0 endpoint.
            var params = {
                'client_id': '<?=$googleClientId?>',
                'redirect_uri': "<?=$_ENV['app.baseURL']?>" + "profile",
                'response_type': 'code',
                'scope': 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
                'include_granted_scopes': 'true'
            };

            // Add form parameters as hidden input values.
            for (var p in params) {
                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', p);
                input.setAttribute('value', params[p]);
                form.appendChild(input);
            }

            // Add form to page and submit it to open the OAuth 2.0 endpoint.
            document.body.appendChild(form);
            form.submit();
        }
        <?php if(isset($code)) {?>
        function getProfile() {
            apiRequest({
                type: 'POST',
                url: `/api/google/profile`,
                data: {
                    code: '<?=$code?>',
                    redirect_uri: "<?=$_ENV['app.baseURL']?>" + "profile"
                },
                dataType: 'json',
                success: function (response, status, request) {
                    console.log(response)
                    if (!response.success) {
                        openPopupErrors('popup-error', response, status, request);
                        return;
                    }
                    const id = response.data['id'];
                    updateAutoLogin('google', id)
                },
                error: function (response, status, error) {
                    openPopupErrors('popup-error', response, status, error);
                },
            });
        }

        getProfile();
        <?php } ?>
    </script>
<?php } ?>
