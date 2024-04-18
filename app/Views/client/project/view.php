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
                <div class="guide"><?= \App\Helpers\HtmlHelper::covertNewline($data['guide']) ?></div>
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
                            <p class="total-count"><?= $reward['total_count'] ?><?= lang('개 한정') ?></p>
                            <div class="line"></div>
                            <p class="price"><?= $reward['price'] ?> KRW</p>
                            <div class="button-wrap">
                                <a class="button button-fill"
                                   href="/project/reward/<?= $reward['id'] ?>"><?= lang('결제하기') ?></a>
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
