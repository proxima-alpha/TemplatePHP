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
                <p class="input-title"><?= lang('Service.email') ?></p>
                <input type="text" name="email" class="under-line"/>
            </div>
            <div class="input-wrap">
                <p class="input-title"><?= lang('Service.password') ?></p>
                <input type="password" name="password" class="under-line"/>
            </div>
        </div>
        <div class="control-button-wrap">
            <a href="/admin/registration"
               class="button under-line register">
                <img src="/asset/images/icon/plus_circle.png"/>
                <span><?= lang('Service.register') ?></span>
            </a>
        </div>
        <div class="control-button-wrap">
            <a href="/admin/reset-password"
               class="button under-line forgot-password">
                <img src="/asset/images/icon/password.png"/>
                <span><?= lang('Service.password_forget') ?></span>
            </a>
        </div>
        <div class="error-message-wrap">
        </div>
        <div class="button-wrap controls">
            <a href="javascript:login('login-container')"
               class="button confirm black"><?= lang('Service.login') ?></a>
        </div>
    </div>
</div>
