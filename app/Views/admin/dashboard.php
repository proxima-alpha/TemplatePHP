<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.dashboard') ?>
        </h3>
        <div class="report-amount-wrap">
            <div class="text-wrap">
                <span class="title"><?= lang('Service.total_amount') ?></span>
                <span class="content"><?= number_format($total_amount) ?> KRW</span>
            </div>
        </div>
        <div class="report-wrap">
            <div class="text-wrap">
                <div class="title"><?= lang('Service.total_reward_count') ?></div>
                <div class="content"><?= $total_reward_count ?></div>
            </div>
            <div class="text-wrap">
                <div class="title"><?= lang('Service.total_purchased_count') ?></div>
                <div class="content"><?= ($purchased_count) ?></div>
            </div>
            <div class="text-wrap">
                <div class="title"><?= lang('Service.total_stock_count') ?></div>
                <div class="content"><?= ($stock_count) ?></div>
            </div>
        </div>
    </div>
</div>
