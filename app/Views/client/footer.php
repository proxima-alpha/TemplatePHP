<?php
$footer_logo_url = isset($logos['footer_logo']) ? "/file/{$logos['footer_logo']['id']}" : '/asset/images/custom/logo.svg';
?>
</div>

<footer id="footer">
    <div class="footer-inner">
        <a href="/" class="logo"><img src="<?= $footer_logo_url ?>" alt=" footer logo"></a>
        <div class="text-wrap">
            <div class="button-wrap">
                <ul class="cf">
                    <li><a href="#"><?= lang('Service.artist_registration') ?></a></li>
                    <li><a href="#"><?= lang('Service.label_registration') ?></a></li>
                    <li><a href="#"><?= lang('Service.guide_qna') ?></a></li>
                    <li><a href="#"><?= lang('Service.request_demo') ?></a></li>
                    <li><a href="#"><?= lang('Service.agreement_service') ?></a></li>
                    <li><a href="#"><?= lang('Service.agreement_personal') ?></a></li>
                </ul>
            </div>
            <div class="company-info">
                <p class="name"><?= $company_info['name'] ?></p>
                <ul class="cf">
                    <?php foreach ($company_info as $key => $value) {
                        if ($key != 'name') { ?>
                            <li>
                                <p class="title"><?= lang('Service.' . $key) ?></p>
                                <p class="value"><?= $value ?></p>
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>
            <ul class="cf">
                <?php if (isset($settings['footer-text'])) {
                    $texts = preg_split("/\r\n|\n|\r/", $settings['footer-text']);
                    foreach ($texts as $text) { ?>
                        <li><p><?= $text ?></p></li>
                    <?php }
                } ?>
            </ul>
            <div class="terms">
                <?php foreach ($terms as $index => $value) { ?>
                    <p><?= $value ?><?= $index == sizeof($terms) - 1 ? '<a href="#">[' . lang('Service.show_information') . ']</a>' : '' ?></p>
                <?php } ?>
            </div>
        </div>
    </div>
</footer>
</div>

</body>
</html>
