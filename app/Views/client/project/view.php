<?php if (isset($data['project_image_id'])) { ?>
    <div class="section " id="image"
         style="background: url('/file/<?= $data['project_image_id'] ?>') no-repeat center;font-size: 0;background-size: cover;">
        <div class="overlap-text-box">
            <div class="overlap-text-wrap">
                <div class="text-wrap">
                    <p><?= $data['title'] ?? '' ?></p>
                </div>
            </div>
        </div>
    </div>
<?php }
if (isset($data['artists'])) { ?>
    <div class="section" id="artists">
        <div class="page-inner">
            <div class="content-wrap slider-box">
                <?= \App\Helpers\HtmlHelper::getGraphicSettingItemSlick($data['artists'], 'profile_id'); ?>
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
                    <?= lang('이벤트') ?>
                </h4>
                <div class="content"><?= \App\Helpers\HtmlHelper::covertNewline($data['content']) ?></div>
                <h4 class="page-sub-title">
                    <?= lang('이용방법') ?>
                </h4>
                <div class="guide-wrap">
                    <p class="title">
                        <?= lang('Client.guide_title') ?>
                    </p>
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
                    <?= lang('일시') ?>
                </h4>
                <p><?= \App\Helpers\HtmlHelper::toDateString($data['start_date']) . ' ~ ' . \App\Helpers\HtmlHelper::toDateString($data['end_date']) ?></p>
            </div>
            <?php if (isset($data['rewards'])) { ?>
                <div class="reward-box">
                    <h4 class="page-sub-title">
                        <?= lang('리워드') ?>
                    </h4>
                    <?php foreach ($data['rewards'] as $reward) { ?>
                        <div class="reward-wrap">
                            <p class="title"><?= $reward['title'] ?></p>
                            <p class="content"><?= \App\Helpers\HtmlHelper::covertNewline($reward['content']) ?></p>
                            <p class="remaining-count"><?= sprintf(lang("현재 재고 %s"), ($reward['total_count'] - $reward['purchased_count'])) ?></p>
                            <p class="limited-count"><?= sprintf(lang("%s 개 한정"), $reward['limited_count']) ?></p>
                            <p class="available-count"><?= sprintf(lang("%s 개 구매가능"), $reward['available_count']) ?></p>
                            <div class="line"></div>
                            <p class="price"><?= $reward['price'] ?> KRW</p>
                            <div class="button-wrap">
                                <a class="button button-fill"
                                   href="javascript:purchaseReward(<?= $reward['id'] ?>, <?= $reward['available_count'] ?>);"><?= lang('결제하기') ?></a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php if (isset($data['artists']) && sizeof($data['artists']) > 0) { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            setArtist(<?=$data['artists'][0]['id']?>)
        });
    </script>
<?php } ?>
