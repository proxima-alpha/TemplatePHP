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
    foreach ($graphic_setting as $index => $item) {
    if($key == 'artist') {?>
    files.push('<?=$key?>', '<?=$item['id']?>', {
        profile_id: <?=$item['profile_id']?>,
        name: '<?=$item['name']?>',
        job: '<?=$item['job']?>',
    });
    <?php } else {?>
    files.push('<?=$key?>', '<?=$item['id']?>', '<?=$item['type']?>');
    <?php }
    }
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
                    <?= \App\Helpers\HtmlHelper::getGrpahicSettingSlick($graphic_settings['main']); ?>
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
        <div class="content-box relation">
            <h4 class="page-sub-title">
                <?= lang('릴레이션') ?>
            </h4>
            <div class="content-wrap slider-box">
                <?php if (\App\Helpers\HtmlHelper::showDataEmpty($graphic_settings['relation'] ?? null)) { ?>
                    <?= \App\Helpers\HtmlHelper::getGrpahicSettingSlick($graphic_settings['relation']); ?>
                <?php } ?>
            </div>
            <div class="control-button-wrap">
                <a href="javascript:editSettingFile('relation');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
        <div class="content-box artist">
            <h4 class="page-sub-title">
                <?= lang('아티스트') ?>
            </h4>
            <div class="content-wrap slider-box">
                <?php if (\App\Helpers\HtmlHelper::showDataEmpty($graphic_settings['artist'] ?? null)) { ?>
                    <?= \App\Helpers\HtmlHelper::getGraphicSettingItemSlick($graphic_settings['artist'], 'profile_id'); ?>
                <?php } ?>
            </div>
            <div class="control-button-wrap">
                <a href="javascript:editSettingFile('artist');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
    </div>
</div>
<?= \App\Helpers\HtmlHelper::setTranslations(['message_info_drag']) ?>
