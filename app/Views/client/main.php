<?php

use App\Helpers\HtmlHelper;

?>
<?= HtmlHelper::setTranslations(['message_popup_page']) ?>
<div class="section " id="page-start">
    <div class="main-slider-wrap">
        <?php if (isset($data['main']) && sizeof($data['main']) > 0) {
            $hasMobile = isset($data['main_mobile']) && sizeof($data['main_mobile']) > 0; ?>
            <div class="slider-box <?= $hasMobile ? 'pc-only' : '' ?>">
                <div class="slick">
                    <?php foreach ($data['main'] as $index => $file) { ?>
                        <a class="slick-item button"
                           href="<?= $file['url'] ?? '' ?>"
                           style="background: url('<?= $file['relative_path'] ?>') no-repeat center; background-size: cover; font-size: 0;">
                            Slider #<?= $index ?>
                        </a>
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
        <?php }
        if (isset($data['main_mobile']) && sizeof($data['main_mobile']) > 0)  {
            $hasPC = isset($data['main']) && sizeof($data['main']) > 0; ?>
            <div class="slider-box <?= $hasPC ? 'mobile-only' : '' ?>">
                <div class="slick">
                    <?php foreach ($data['main_mobile'] as $index => $file) { ?>
                        <a class="slick-item button"
                           href="<?= $file['url'] ?? '' ?>"
                           style="background: url('<?= $file['relative_path'] ?>') no-repeat center; background-size: cover; font-size: 0;">
                            Slider #<?= $index ?>
                        </a>
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
        <?php } ?>
    </div>
</div>
<div class="section" id="page-media">
    <div class="page-inner">
        <div class="content-box relation">
            <h4 class="page-sub-title">
                <?= lang("Client.relation") ?>
            </h4>
            <?php if (HtmlHelper::showDataEmpty($data['relation'] ?? null)) { ?>
                <div class="scroll-control-button-wrap">
                    <a href="javascript:;" onclick="onClickScrollLeft(this)" class="button left">
                        <img src="/asset/images/icon/button_left.png"/>
                    </a>
                    <a href="javascript:;" onclick="onClickScrollRight(this)" class="button right">
                        <img src="/asset/images/icon/button_right.png"/>
                    </a>
                </div>
                <div class="content-wrap scroll-horizontal-wrap">
                    <div class="content-wrap-inner" style="width: <?= (sizeof($data['relation']) * 205) ?>px;">
                        <?php foreach ($data['relation'] as $index => $file) { ?>
                            <div class="content-media-item">
                                <video preload="metadata" muted>
                                    <source src="<?= $file['relative_path'] ?>">
                                </video>
                                <p class="time-string"><?= HtmlHelper::secToString($file['time']) ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if ($data_settings['main-show-project'] == 1) { ?>
            <div class="content-box project-popular">
                <h4 class="page-sub-title">
                    <?= lang("Client.popular_project") ?>
                </h4>
                <?php if (HtmlHelper::showDataEmpty($data['project_popular'] ?? null, 368)) { ?>
                    <div class="scroll-control-button-wrap">
                        <a href="javascript:;" onclick="onClickScrollLeft(this)" class="button left">
                            <img src="/asset/images/icon/button_left.png"/>
                        </a>
                        <a href="javascript:;" onclick="onClickScrollRight(this)" class="button right">
                            <img src="/asset/images/icon/button_right.png"/>
                        </a>
                    </div>
                    <div class="content-wrap scroll-horizontal-wrap">
                        <div class="content-wrap-inner" style="width: <?= (sizeof($data['project_popular']) * 240) ?>px;">
                            <?php foreach ($data['project_popular'] as $index => $item) { ?>
                                <?= HtmlHelper::getProjectItem($item, $lang)?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php }
        foreach ($data['project_by_code'] as $code => $codeItem) {
            if ($data_settings['main-show-' . $code] == 1) { ?>
                <div class="content-box artists <?= $code ?>">
                    <h4 class="page-sub-title">
                        <?= $lang == 'ko' ? $codeItem['code']['name'] : $codeItem['code']['name_en'] ?>
                    </h4>
                    <?php if (HtmlHelper::showDataEmpty($codeItem['array'] ?? null, 368)) { ?>
                        <div class="scroll-control-button-wrap">
                            <a href="javascript:;" onclick="onClickScrollLeft(this)" class="button left">
                                <img src="/asset/images/icon/button_left.png"/>
                            </a>
                            <a href="javascript:;" onclick="onClickScrollRight(this)" class="button right">
                                <img src="/asset/images/icon/button_right.png"/>
                            </a>
                        </div>
                        <div class="content-wrap scroll-horizontal-wrap">
                            <div class="content-wrap-inner"
                                 style="width: <?= (sizeof($codeItem['array']) * 240) ?>px;">
                                <?php foreach ($codeItem['array'] as $index => $item) {
                                    $url = isset($item['image_id']) ? '/file/' . $item['image_id'] : '/asset/images/custom/object.svg'; ?>
                                    <div class="content-item">
                                        <a href="/project/<?= $item['id'] ?>/view">
                                            <div class="image-item-wrap">
                                                <div class="image-item"
                                                     style="background: url(' <?= $url ?> ') no-repeat center; background-size: cover; font-size: 0;"></div>
                                            </div>
                                            <div class="text-item-wrap">
                                                <p class="item-title"><?= $lang == 'ko' ? $item['title'] :
                                                        $item['title_en'] ?></p>
                                                <p class="item-date"><?= (HtmlHelper::toDateString($item['start_date'])
                                                        . ' ~'
                                                        . HtmlHelper::toDateString($item['end_date'])) ?></p>
                                                <p class="item-content"><?= $lang == 'ko' ? $item['content'] :
                                                        $item['content_en'] ?></p>
                                            </div>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php
            }
        }
        if ($data_settings['main-show-previous-project'] == 1) { ?>
            <div class="content-box previous-project">
                <h4 class="page-sub-title">
                    <?= lang("Client.previous_project") ?>
                </h4>
                <div class="content-wrap">
                    <?php if (HtmlHelper::showDataEmpty($data['previous_project'] ?? null, 368)) { ?>
                        <?= HtmlHelper::getProjectContent($data['previous_project'], $lang); ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="section" id="page-intro">
    <div class="page-inner">
        <h3 class="page-title">
            <?= lang('Client.main_info_title') ?>
        </h3>
        <h4 class="page-sub-title">
            <?= lang('Client.main_info_sub_title') ?>
        </h4>
        <div class="content-box">
            <div class="content-wrap image">
                <img src="/asset/images/custom/device.svg"/>
            </div>
            <div class="content-wrap list">
                <ul>
                    <li class="wrap-fill">
                        <p class="title"><?= lang('Client.main_info_title_01') ?></p>
                    </li>
                    <li class="wrap-line">
                        <p class="title"><?= lang('Client.main_info_title_02') ?></p>
                        <p class="content"><?= lang('Client.main_info_content_02') ?></p>
                    </li>
                    <li class="wrap-line">
                        <p class="title"><?= lang('Client.main_info_title_03') ?></p>
                        <p class="content"><?= lang('Client.main_info_content_03') ?></p>
                    </li>
                    <li class="wrap-fill">
                        <p class="title"><?= lang('Client.main_info_title_04') ?></p>
                        <p class="content"><?= lang('Client.main_info_content_04') ?></p>
                    </li>
                    <li class="wrap-line">
                        <p class="title"><?= lang('Client.main_info_title_05') ?></p>
                        <p class="content"><?= lang('Client.main_info_content_05') ?></p>
                    </li>
                </ul>
            </div>
            <div class="content-box right">
            </div>

        </div>
    </div>
</div>

