<div class="container-inner form-container">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.registration') ?>
        </h3>
        <?php if (isset($email) && isset($code)) { ?>
            <div class="form-box">
                <div class="form-wrap">
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.email') ?></p>
                        <input type="email" name="email" class="under-line" readonly value="<?= $email ?>"/>
                    </div>
                    <div class="input-wrap disappear-at-next-step">
                        <p class="input-title"><?= lang('Service.verification_code') ?></p>
                        <input type="text" name="code" class="under-line" readonly value="<?= $code ?>"/>
                    </div>
                    <div class="error-message-wrap disappear-at-next-step">
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                confirmVerificationCode();
            </script>
        <?php } else { ?>
            <div class="form-box">
                <div class="form-wrap">
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.email') ?></p>
                        <input type="email" name="email" class="under-line"/>
                    </div>
                    <div class="error-message-wrap disappear-at-next-step">
                    </div>
                    <div class="button-wrap controls disappear-at-next-step" style="margin-top: 40px">
                        <a href="javascript:sendVerificationCode()"
                           class="button confirm black">
                            <?= lang('Service.verification_verify_email') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?= \App\Helpers\HtmlHelper::setTranslations(['message_info_mail', 'password', 'password_confirm', 'verification_code', 'resend']) ?>
