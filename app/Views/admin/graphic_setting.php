<?php

use Crisu83\ShortId\ShortId;

$shortid = ShortId::create();
$identifier = $shortid->generate();

\App\Helpers\HtmlHelper::setTranslations([
    'message_info_drag',
    'category',
    'title',
    'status',
    'message_item_already_selected',
    'message_item_select',
    'search_artist',
    'assigned',
]);
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    let projectCodes = []

    <?php if (isset($data)) {
        foreach($data['project_code'] as $code) {?>
            projectCodes.push(`<?=$code['code']?>`)
    <?php }
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
            </div>
            <div class="control-button-wrap">
                <a href="javascript:editSetting('main');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
        <div class="content-box main_mobile">
            <h4 class="page-sub-title">
                <?= lang('Service.main_image') ?>
                (<?= lang('Service.mobile') ?>)
            </h4>
            <div class="content-wrap slider-box">
            </div>
            <div class="control-button-wrap">
                <a href="javascript:editSetting('main_mobile');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
        <div class="content-box relation">
            <h4 class="page-sub-title">
                <?= lang('Service.relation') ?>
            </h4>
            <div class="content-wrap slider-box">
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
                <?= lang('Service.project') ?>
            </h4>
            <div class="input-wrap inline">
                <input type="checkbox"
                       name="main-show-project" <?= $data_settings['main-show-project'] ?? null == '1' ? 'checked' : '' ?>
                       onchange="onSettingChanged(this, 'project')"/>
                <p class="input-title"><?= lang('Service.show_main') ?></p>
            </div>
            <div class="content-wrap slider-box">
            </div>
            <div class="control-button-wrap">
                <a href="javascript:editSetting('project');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
        <div class="content-box item-selector previous-project">
            <h4 class="page-sub-title">
                <?= lang('Service.previous_project') ?>
            </h4>
            <div class="input-wrap inline">
                <input type="checkbox"
                       name="main-show-project" <?= $data_settings['main-show-previous-project'] ?? null == '1' ? 'checked' : '' ?>
                       onchange="onSettingChanged(this, 'previous-project')"/>
                <p class="input-title"><?= lang('Service.show_main') ?></p>
            </div>
        </div>
        <?php foreach ($data['project_code'] as $code) { ?>
            <div class="content-box item-selector <?= $code['code'] ?>">
                <h4 class="page-sub-title">
                    <?= $lang == 'ko' ? $code['name'] : $code['name_en'] ?>
                </h4>
                <div class="input-wrap inline">
                    <input type="checkbox"
                           name="main-show-<?= $code['code'] ?>" <?= $data_settings['main-show-' . $code['code']] ?? null == '1' ? 'checked' : '' ?>
                           onchange="onSettingChanged(this, `<?= $code['code'] ?>`)"/>
                    <p class="input-title"><?= lang('Service.show_main') ?></p>
                </div>
                <div class="content-wrap slider-box">
                </div>
                <div class="control-button-wrap">
                    <a href="javascript:editSetting('<?= $code['code'] ?>');"
                       class="button under-line edit">
                        <img src="/asset/images/icon/edit.png"/>
                        <span><?= lang('Service.edit') ?></span>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    /**
     * admin/popup_input
     */
    initializeInputPopup({
        getGetUrl: function (id) {
            return `/api/file/get/${id}`
        },
        getUpdateUrl: function (id) {
            return `/api/file/update/${id}`
        },
        getHtml: function (data) {
            let typeSet = {
                url: {
                    type: 'text',
                    name: 'url',
                },
            };
            let keys = Object.keys(typeSet);
            let html = ``;

            for (let i in keys) {
                let key = keys[i];
                let extracted = fromDataToHtml(key, data, typeSet);
                if (extracted) {
                    html += extracted;
                }
            }
            return html;
        },
        getControlHtml: function (key, data) {
            const isAdmin = getCookie('is_admin')
            let html = ``;
            if (isAdmin) {
                html += `
            <a href="javascript:editInputPopup('${key}', ${data['id']});"
               class="button under-line edit">
                <img src="/asset/images/icon/edit.png"/>
                <span>${lang('edit')}</span>
            </a>`;
            }
            return html;
        }
    })
</script>
