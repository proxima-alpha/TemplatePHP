<?= \App\Helpers\HtmlHelper::setTranslations(['select_date']) ?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.dashboard') ?>
        </h3>
        <div class="filter-wrap">
            <div class="input-wrap column">
                <p class="input-title"><?= lang('Service.filter_start_date') ?></p>
                <a class="button" href="javascript:openCalendarPopup('start_date')">
                    <input class="editable" name="start_date"
                           value="<?= \App\Helpers\HtmlHelper::toDateString($start_date ?? null) ?>"
                           readonly>
                </a>
            </div>
            <div class="input-wrap column">
                <p class="input-title"><?= lang('Service.filter_end_date') ?></p>
                <a class="button" href="javascript:openCalendarPopup('end_date')">
                    <input class="editable" name="end_date"
                           value="<?= \App\Helpers\HtmlHelper::toDateString($end_date ?? null) ?>"
                           readonly>
                </a>
            </div>
        </div>
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
                <div class="content"><?= ($stock_count - $purchased_count) ?></div>
            </div>
        </div>
    </div>
</div>
