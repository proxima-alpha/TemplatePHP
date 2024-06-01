<?php
\App\Helpers\HtmlHelper::setTranslations([
    'message_error_login',
    'message_error_exceed',
    'message_error_expired',
    'message_error_not_started',
    'reward_type_all',
    'reward_type_random',
    'reward_now_stock_string',
    'reward_limited_count_string',
    'reward_available_count_string',
    'purchase',
], 'Client');
if (isset($data['background_id'])) {
    $hasMobile = isset($data['mobile_background_id']);?>
    <div class="section background <?=$hasMobile ? 'pc-only' : ''?>"
         style="background: url('/file/<?= $data['background_id'] ?>') no-repeat center;font-size: 0;background-size: cover;">
        <div class="overlap-text-box">
            <div class="overlap-text-wrap">
                <div class="text-wrap">
                    <p><?= ($lang == 'ko' ? $data['title'] : $data['title_en']) ?? '' ?></p>
                </div>
            </div>
        </div>
    </div>
<?php }
if (isset($data['mobile_background_id'])) {
    $hasPC = isset($data['background_id']);?>
    <div class="section background <?=$hasPC ? 'mobile-only' : ''?>"
         style="background: url('/file/<?= $data['mobile_background_id'] ?>') no-repeat center;font-size: 0;background-size: cover;">
        <div class="overlap-text-box">
            <div class="overlap-text-wrap">
                <div class="text-wrap">
                    <p><?= ($lang == 'ko' ? $data['title'] : $data['title_en']) ?? '' ?></p>
                </div>
            </div>
        </div>
    </div>
<?php }
if (isset($data['artists'])) { ?>
    <div class="section" id="artists">
        <div class="page-inner">
            <div class="scroll-control-button-wrap">
                <a href="javascript:;" onclick="onClickScrollLeft(this)" class="button left">
                    <img src="/asset/images/icon/button_left.png"/>
                </a>
                <a href="javascript:;" onclick="onClickScrollRight(this)" class="button right">
                    <img src="/asset/images/icon/button_right.png"/>
                </a>
            </div>
            <div class="content-wrap scroll-horizontal-wrap">
                <div class="content-wrap-inner" style="width: <?= (sizeof($data['artists']) * 180) ?>px;">
                    <?php foreach ($data['artists'] as $index => $item) {
                        $url = isset($item['image_id']) ? '/file/' . $item['image_id'] : '/asset/images/custom/object.svg'; ?>
                        <div class="content-item button" id="artist-<?= $item['id'] ?>"
                             onclick="setArtist(<?= $item['id'] ?>)">
                            <div class="image-item-wrap">
                                <div class="image-item"
                                     style="background: url(' <?= $url ?> ') no-repeat center; background-size: cover; font-size: 0;"></div>
                            </div>
                            <div class="text-item-wrap">
                                <p class="item-title"><?= $lang == 'ko' ? $item['name'] :
                                        $item['name_en'] ?></p>
                                <p class="item-content"><?= $lang == 'ko' ? $item['job'] :
                                        $item['job_en'] ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="line"></div>
        </div>
    </div>
<?php } ?>
<div class="container-inner">
    <div class="container-wrap">
        <div class="content-box">
            <div class="artist-box">
                <div class="title-text-wrap">
                    <h3 class="name">-</h3>
                    <h4 class="job">-</h4>
                </div>
                <p class="content-text-wrap">
                    -
                </p>
            </div>
            <div class="project-wrap">
                <h4 class="page-sub-title">
                    <?= lang('Client.project') ?>
                </h4>
                <div
                    class="content"><?= \App\Helpers\HtmlHelper::covertNewline($lang == 'ko' ? $data['content'] : $data['content_en']) ?></div>
                <h4 class="page-sub-title">
                    <?= lang('Client.guide_title') ?>
                </h4>
                <div class="guide-wrap">
                    <div class="guide-content-wrap">
                        <?php foreach ($guide as $item) { ?>
                            <p class="sub-title">
                                <?= $item['title'] ?>
                            </p>
                            <div class="content">
                                <p>
                                    <?= $item['content'] ?>
                                </p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="side-content-box">
            <div class="date-box">
                <h4 class="page-sub-title">
                    <?= lang('Client.datetime') ?>
                </h4>
                <p><?= \App\Helpers\HtmlHelper::toDateString($data['start_date']) . ' ~ ' . \App\Helpers\HtmlHelper::toDateString($data['end_date']) ?></p>
            </div>
            <div class="reward-box">
                <h4 class="page-sub-title">
                    <?= lang('Client.reward') ?>
                </h4>
            </div>
            <div class="button-wrap">
                <a class="button button-fill"
                   href="javascript:purchase(<?= $data['id'] ?>, '<?= $data['start_date'] ?>', '<?= $data['end_date'] ?>');"><?= lang('Client.purchase') ?></a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        <?php if (isset($data['artists']) && sizeof($data['artists']) > 0) { ?>
        setArtist(<?=$data['artists'][0]['id']?>)
        <?php } ?>
        loadReward(<?=$data['id']?>, false)
    });
</script>
