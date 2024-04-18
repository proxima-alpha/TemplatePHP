<div class="container-inner">
    <div class="container-wrap">
        <div class="content-box">
            <div class="page page-1">
                <?php if (isset($reward)) { ?>
                    <div class="reward-box">
                        <h4 class="page-sub-title">
                            <?= lang('리워드 선택') ?>
                        </h4>
                        <div class="reward-wrap">
                            <p class="title"><?= $reward['title'] ?></p>
                            <p class="content"><?= \App\Helpers\HtmlHelper::covertNewline($reward['content']) ?></p>
                            <p class="total-count"><?= $reward['total_count'] ?><?= lang('개 한정') ?></p>
                            <div class="line"></div>
                            <p class="price"><?= $reward['price'] ?> KRW</p>
                        </div>
                    </div>
                <?php } ?>
                <div class="payment-box">
                    <div class="limited-count-wrap">
                        <span class="title"><?= lang('구매가능한 수량') ?> </span>
                        <span class="content"><?= $reward['limited_count'] ?></span>
                    </div>
                    <input type="number" name="count" class="editable" value="1"
                           onchange="onCountChange(this, <?= $reward['price'] ?>)"/>
                    <p class="total-price"><?= $reward['price'] ?> KRW</p>
                </div>
            </div>
            <div class="button-wrap">
                <a class="button prev button-line disabled"
                   href="javascript:onClickPrev();"><?= lang('이전') ?></a>
                <a class="button next button-fill" href="javascript:onClickNext();"><?= lang('다음') ?></a>
            </div>
        </div>
        <div class="side-content-box">
            <div class="date-box">
                <h4 class="page-sub-title">
                    <?= lang('일시') ?>
                </h4>
                <p><?= \App\Helpers\HtmlHelper::toDateString($project['start_date']) . ' ~ ' . \App\Helpers\HtmlHelper::toDateString($project['end_date']) ?></p>
            </div>
            <div class="stage-box">
                <div class="step-wrap selected">
                    <div class="circle">
                        <img src="/asset/images/icon/check.png"/>
                    </div>
                    <div class="text-wrap">
                        <?= lang('Service.reward_step_01') ?>
                    </div>
                </div>
                <div class="step-wrap">
                    <div class="circle">
                        <img src="/asset/images/icon/check.png"/>
                    </div>
                    <div class="text-wrap">
                        <?= lang('Service.reward_step_02') ?>
                    </div>
                </div>
                <div class="step-wrap">
                    <div class="circle">
                        <img src="/asset/images/icon/check.png"/>
                    </div>
                    <div class="text-wrap">
                        <?= lang('Service.reward_step_03') ?>
                    </div>
                </div>
            </div>
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
