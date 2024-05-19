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
                    autoLogin('login-container', 'kakao', response.id, response.kakao_account.email)
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

<div class="container-inner login-container">
    <style>
        .login-container {
            line-height: 600px;
        }

        .login-container .container-wrap {
            width: 600px;
            display: inline-block;
            vertical-align: middle;
        }

        @media (max-width: 840px) {
            .login-container .container-wrap {
                width: 100%;
            }
        }
    </style>
    <div class="container-wrap">
        <h3 class="title">
            <?= lang('Service.login') ?>
        </h3>
        <div class="form-wrap">
            <div class="input-wrap">
                <p class="input-title"><?= lang('Service.username') ?></p>
                <input type="text" name="username" class="under-line"/>
            </div>
            <div class="input-wrap">
                <p class="input-title"><?= lang('Service.password') ?></p>
                <input type="password" name="password" class="under-line"/>
            </div>
        </div>
        <div class="control-button-wrap">
            <a href="/registration"
               class="button under-line register">
                <span><?= lang('Service.register') ?></span>
            </a>
        </div>
        <div class="control-button-wrap">
            <a href="/reset-password"
               class="button under-line forgot-password">
                <span><?= lang('Service.password_forget') ?></span>
            </a>
        </div>
        <div class="error-message-wrap">
        </div>
        <div class="button-wrap controls">
            <a href="javascript:login('login-container')"
               class="button confirm"><?= lang('Service.login') ?></a>
        </div>
        <div class="auto-login-box">
            <div class="divider"><span>OR</span></div>
            <div class="auto-login-wrap">
                <div class="kakao">
                    <a id="kakao-login-btn" href="javascript:loginWithKakao()">
                        <img src="/asset/images/custom/kakao_login_medium.png"
                             alt="카카오 로그인 버튼"/>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
