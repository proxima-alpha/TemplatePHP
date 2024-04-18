<?= \App\Helpers\HtmlHelper::setTranslationsClient(['message_popup_page']) ?>
<div class="section " id="page-start">
    <div class="main-slider-wrap">
        <div class="slider-box">
            <div class="slick">
                <?php foreach ($data['main'] as $index => $image) { ?>
                    <div class="slick-item"
                         style="background: url('/file/<?= $image['id'] ?>') no-repeat center; background-size: cover; font-size: 0;">
                        Slider #<?= $index ?>
                    </div>
                <?php } ?>
            </div>
            <div class="overlap-text-box">
                <div class="overlap-text-wrap">
                    <div class="text-wrap">
                        <p><?= $settings['main-content-text'] ?? '' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="section" id="page-media">
    <div class="page-inner">
        <div class="content-box relation">
            <h4 class="page-sub-title">
                BECLE FAN RELATION
            </h4>
            <div class="content-wrap slider-box">
                <?php if (\App\Helpers\HtmlHelper::showDataEmpty($data['relation'] ?? null)) { ?>
                    <?= \App\Helpers\HtmlHelper::getGrpahicSettingSlick($data['relation']); ?>
                <?php } ?>
            </div>
        </div>
        <?php if ($data_settings['main-show-project'] == 1) { ?>
            <div class="content-box project">
                <h4 class="page-sub-title">
                    인기 프로젝트
                </h4>
                <div class="content-wrap slider-box">
                    <?php if (\App\Helpers\HtmlHelper::showDataEmpty($data['project'] ?? null, 390)) { ?>
                        <?= \App\Helpers\HtmlHelper::getGraphicSettingProjectItemSlick($data['project'], 'project_image_id'); ?>
                    <?php } ?>
                </div>
            </div>
        <?php }
        foreach ($data['artists'] as $code => $items) {
            if ($data_settings['main-show-' . $code] == 1) { ?>
                <div class="content-box artists <?= $code ?>">
                    <h4 class="page-sub-title">
                        <?= $code ?>
                    </h4>
                    <div class="content-wrap slider-box">
                        <?php if (\App\Helpers\HtmlHelper::showDataEmpty($items ?? null, 370)) { ?>
                            <?= \App\Helpers\HtmlHelper::getGraphicSettingItemSlick($items, 'profile_id'); ?>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
        }
        if ($data_settings['main-show-previous-project'] == 1) { ?>
            <div class="content-box previous-project">
                <h4 class="page-sub-title">
                    지난 프로젝트
                </h4>
                <div class="content-wrap slider-box">
                    <?php if (\App\Helpers\HtmlHelper::showDataEmpty($data['previous-project'] ?? null, 390)) { ?>
                        <?= \App\Helpers\HtmlHelper::getProjectItem($data['previous-project'], 'project_image_id'); ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="section" id="page-intro">
    <div class="page-inner">
        <h3 class="page-title">
            <?= lang('Service.main_info_title') ?>
        </h3>
        <h4 class="page-sub-title">
            <?= lang('Service.main_info_sub_title') ?>
        </h4>
        <div class="content-box">
            <div class="content-wrap image">
                <img src="/asset/images/custom/device.svg"/>
            </div>
            <div class="content-wrap list">
                <ul>
                    <li class="wrap-fill">
                        <p class="title"><?= lang('Service.main_info_title_01') ?></p>
                    </li>
                    <li class="wrap-line">
                        <p class="title"><?= lang('Service.main_info_title_02') ?></p>
                        <p class="content"><?= lang('Service.main_info_content_02') ?></p>
                    </li>
                    <li class="wrap-line">
                        <p class="title"><?= lang('Service.main_info_title_03') ?></p>
                        <p class="content"><?= lang('Service.main_info_content_03') ?></p>
                    </li>
                    <li class="wrap-fill">
                        <p class="title"><?= lang('Service.main_info_title_04') ?></p>
                        <p class="content"><?= lang('Service.main_info_content_04') ?></p>
                    </li>
                    <li class="wrap-line">
                        <p class="title"><?= lang('Service.main_info_title_05') ?></p>
                        <p class="content"><?= lang('Service.main_info_content_05') ?></p>
                    </li>
                </ul>
            </div>
            <div class="content-box right">
            </div>

        </div>
    </div>
</div>

