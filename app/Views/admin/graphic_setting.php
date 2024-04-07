<?php

use Crisu83\ShortId\ShortId;

$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    <?php
    if (isset($graphic_settings)) {
    foreach ($graphic_settings as $key => $graphic_setting) {
    foreach ($graphic_setting as $index => $item) { ?>
    files.push('<?=$key?>', '<?=$item['id']?>');
    <?php }
    }
    }?>
</script>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.main_page_setting') ?>
        </h3>
        <div class="content-box main">
            <h4 class="page-sub-title">
                <?= lang('Service.main_image') ?>
            </h4>
            <div class="content-wrap slider-box">
                <?php if (\App\Helpers\HtmlHelper::showDataEmpty($graphic_settings['main'] ?? null)) { ?>
                    <div class="content-wrap-inner slider-wrap lines-horizontal">
                        <div class="slick-wrap">
                            <div class="slick">
                                <?php foreach ($graphic_settings['main'] as $index => $item) { ?>
                                    <div class="slick-item button"
                                         style="background: url('/file/<?= $item['id'] ?>') no-repeat center; background-size: cover; font-size: 0;"
                                         onclick="openImagePopup(<?= $item['id'] ?>)">
                                        Slider #<?= $item['id'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="control-button-wrap">
                <a href="javascript:editSettingFile('main');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
        <!--        <div class="button-wrap">-->
        <!--            <a href="#"-->
        <!--               class="button confirm black">Confirm</a>-->
        <!--        </div>-->
    </div>
</div>
<?= \App\Helpers\HtmlHelper::setTranslations(['message_info_drag']) ?>
