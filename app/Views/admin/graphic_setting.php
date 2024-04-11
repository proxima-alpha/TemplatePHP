<?php

use Crisu83\ShortId\ShortId;

$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    let artist_codes = []
    <?php
    if (isset($graphic_settings)) {
    foreach ($graphic_settings as $key => $graphic_setting) {
    if($key == 'artists') {
    foreach ($graphic_settings['artists'] as $code => $items) { ?>
    artist_codes.push('<?=$code?>')
    files.checkEmpty('<?=$code?>')
    <?php foreach ($items as $index => $item) { ?>
    files.push('<?=$code?>', '<?=$item['id']?>', {
        profile_id: <?=$item['profile_id']?>,
        name: '<?=$item['name']?>',
        job: '<?=$item['job']?>',
    });
    <?php }
    }
    } else {?>
    files.checkEmpty('<?=$key?>')
    <?php foreach ($graphic_setting as $index => $item) {
    if($key == 'project') {?>
    files.push('<?=$key?>', '<?=$item['id']?>', {
        project_image_id: <?=$item['project_image_id']?>,
        title: '<?=$item['title']?>',
        start_date: '<?=$item['start_date']?>',
        end_date: '<?=$item['end_date']?>',
        content: '<?=$item['content']?>',
    });
    <?php } else {?>
    files.push('<?=$key?>', '<?=$item['id']?>', '<?=$item['type']?>');
    <?php }
    }
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
                <a href="javascript:editSetting('main');"
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
                <a href="javascript:editSetting('relation');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
        <div class="content-box item-selector project">
            <h4 class="page-sub-title">
                <?= lang('프로젝트') ?>
            </h4>
            <div class="content-wrap slider-box">
                <?php if (\App\Helpers\HtmlHelper::showDataEmpty($graphic_settings['project'] ?? null, 390)) { ?>
                    <?= \App\Helpers\HtmlHelper::getGraphicSettingProjectItemSlick($graphic_settings['project'], 'project_image_id'); ?>
                <?php } ?>
            </div>
            <div class="control-button-wrap">
                <a href="javascript:editSetting('project');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
        <?php foreach ($graphic_settings['artists'] as $code => $items) { ?>
            <div class="content-box item-selector <?= $code ?>">
                <h4 class="page-sub-title">
                    <?= lang($code) ?>
                </h4>
                <div class="content-wrap slider-box">
                    <?php if (\App\Helpers\HtmlHelper::showDataEmpty($items ?? null, 370)) { ?>
                        <?= \App\Helpers\HtmlHelper::getGraphicSettingItemSlick($items, 'profile_id'); ?>
                    <?php } ?>
                </div>
                <div class="control-button-wrap">
                    <a href="javascript:editSetting('<?= $code ?>');"
                       class="button under-line edit">
                        <img src="/asset/images/icon/edit.png"/>
                        <span><?= lang('Service.edit') ?></span>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?= \App\Helpers\HtmlHelper::setTranslations(['message_info_drag']) ?>
