<?php
\App\Helpers\HtmlHelper::setTranslations([
    'reward_purchase_item_01',
    'reward_purchase_item_02',
    'reward_purchase_item_02_1',
    'reward_purchase_item_02_2',
    'reward_purchase_item_03',
    'reward_purchase_item_03_1',
    'reward_purchase_item_03_2',
    'reward_purchase_item_04',
    'reward_purchase_item_05',
    'reward_reaction',
    'reward_request',
    'show_more',
    'message_error_field_empty',
    'reward_type_all',
    'reward_type_random',
    'reward_now_stock_string',
    'reward_limited_count_string',
    'reward_available_count_string',
    'purchase',
], 'Client');
?>
<script src="https://cdn.iamport.kr/v1/iamport.js"></script>
<script type="text/javascript">
    IMP.init(`<?=$imp_shop_id?>`);
    rewardPrice = <?=$reward['price']?>;
    rewardName = `<?=$lang == 'ko' ? $reward['title'] : $reward['title_en']?>`;
    availableCount = <?=$reward['available_count']?>;
    <?php if (isset($reward_requests)) {
    foreach ($reward_requests as $index => $item) { ?>
    rewardRequests[`<?=$item['id']?>`] = {
        'id': `<?=$item['id']?>`,
        'code': `<?=$item['code']?>`,
        'name': `<?=$item['name']?>`,
        'name_en': `<?=$item['name_en']?>`,
    };
    <?php }
    }?>
</script>
<div class="container-inner">
    <div class="container-wrap">
        <div class="side-content-box">
            <div class="date-box">
                <h4 class="page-sub-title">
                    <?= lang('Client.datetime') ?>
                </h4>
                <p><?= \App\Helpers\HtmlHelper::toDateString($project['start_date']) . ' ~ ' . \App\Helpers\HtmlHelper::toDateString($project['end_date']) ?></p>
            </div>
            <div class="stage-box">
                <div class="step-wrap selected">
                    <div class="circle">
                        <img src="/asset/images/custom/check.svg"/>
                    </div>
                    <div class="text-wrap">
                        <?= lang('Client.reward_step_01') ?>
                    </div>
                </div>
                <div class="step-wrap">
                    <div class="circle">
                        <img src="/asset/images/custom/check.svg"/>
                    </div>
                    <div class="text-wrap">
                        <?= lang('Client.reward_step_02') ?>
                    </div>
                </div>
                <div class="step-wrap">
                    <div class="circle">
                        <img src="/asset/images/custom/check.svg"/>
                    </div>
                    <div class="text-wrap">
                        <?= lang('Client.reward_step_03') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-box">
            <div class="page" id="page-1">
                <div class="reward-box">
                    <h4 class="page-sub-title">
                        <?= lang('Client.reward_select') ?>
                    </h4>
                </div>
                <div class="select-payment-box">
                    <div class="limited-count-wrap">
                        <span class="title"><?= lang('Client.available_count') ?> </span>
                        <span class="content"><?= $reward['available_count'] ?></span>
                    </div>
                    <input type="number" name="count" class="editable" value="1"
                           onchange="onCountChange(this)"/>
                    <div class="total-price">
                        <input type="text" name="paid" value="<?= number_format($reward['price']) ?>" readonly/>
                        <p>KRW</p>
                    </div>
                </div>
            </div>
            <div class="page" id="page-2" style="display: none">
                <h4 class="page-sub-title">
                    <?= lang('Client.reward_select') ?>
                </h4>
                <div class="purchase-item-wrap">
                    <form class="form-wrap">
                        <input class="editable" hidden type="number" name="price"
                               value="<?= $reward['price'] ?>"/>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Client.reward_purchase_item_01') ?></p>
                            <select class="editable" name="code_reward_request_id" value="1">

                                <?php foreach ($reward_requests as $index => $item) { ?>
                                    <option
                                        value="<?= $item['id'] ?>"><?= $lang == 'ko' ? $item['name'] : $item['name_en'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="input-wrap mine">
                            <p class="input-title"><?= lang('Client.reward_purchase_item_02') ?></p>
                            <div class="input-item-wrap">
                                <input class="editable" type="radio" id="is-mine-true" name="is_mine" value="1" checked>
                                <label for="is-mine-true"><?= lang('Client.reward_purchase_item_02_1') ?></label>
                            </div>
                            <div class="input-item-wrap">
                                <input class="editable" type="radio" id="is-mine-false" name="is_mine" value="0">
                                <label for="is-mine-false"><?= lang('Client.reward_purchase_item_02_2') ?></label>
                            </div>
                        </div>
                        <div class="input-wrap inquirer">
                            <p class="input-title"><?= lang('Client.reward_purchase_item_03') ?></p>
                            <input class="editable" type="text" name="inquirer_name"
                                   placeholder="<?= lang('Client.reward_purchase_item_03_1') ?>"
                                   value="<?= $user_name ?? '' ?>"/>
                            <input class="editable" type="email" name="inquirer_email"
                                   placeholder="<?= lang('Client.reward_purchase_item_03_2') ?>"
                                   value="<?= $user_email ?? '' ?>"/>
                        </div>
                        <div class="input-wrap comment">
                            <p class="input-title"><?= lang('Client.reward_purchase_item_04') ?></p>
                            <textarea class="editable" name="inquirer_comment"></textarea>
                        </div>
                        <div class="input-wrap inline agree">
                            <input class="editable" type="checkbox" id="is-agree" name="is_agreed"/>
                            <label for="is-agree"
                                   class="input-title"><?= lang('Client.reward_purchase_item_05') ?></label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="page" id="page-3">
                <div class="purchase-item-box">
                    <h4 class="page-sub-title">
                        <?= lang('Client.purchase_selected_item') ?>
                    </h4>
                    <?php if (isset($project)) { ?>
                        <div class="project-wrap">
                            <div class="image-wrap"
                                 style="background: url('/file/<?= $project['project_image_id'] ?> ?>') no-repeat center; background-size: cover; font-size: 0;">
                            </div>
                            <div class="content-wrap">
                                <p class="title"><?= $lang == 'ko' ? $project['title'] : $project['title_en'] ?></p>
                                <div class="line"></div>
                                <p class="content"><?= \App\Helpers\HtmlHelper::covertNewline($lang == 'ko' ? $project['content'] : $project['content_en']) ?></p>
                            </div>
                        </div>
                    <?php }
                    if (isset($reward)) { ?>
                        <div class="reward-wrap">
                            <p class="title"><?= $lang == 'ko' ? $reward['title'] : $reward['title_en'] ?></p>
                            <p class="content"><?= \App\Helpers\HtmlHelper::covertNewline($lang == 'ko' ? $reward['content'] : $reward['content_en']) ?></p>
                            <p class="total-count"><?= sprintf(lang('Client.reward_limited_count_string'), $reward['total_count']) ?></p>
                            <div class="line"></div>
                            <p class="price"><?= number_format($reward['price']) ?> KRW</p>
                        </div>
                    <?php } ?>
                    <div class="purchase-item-wrap">
                        <div class="form-wrap">
                            <div class="input-wrap inline">
                                <p class="input-title"><?= lang('Service.name') ?></p>
                                <input type="name" name="inquirer_name" value="" readonly/>
                            </div>
                            <div class="input-wrap inline">
                                <p class="input-title"><?= lang('Service.email') ?></p>
                                <input type="email" name="inquirer_email" value="" readonly/>
                            </div>
                            <div class="input-wrap inline">
                                <p class="input-title"><?= lang('Client.reward_reaction') ?></p>
                                <input type="text" name="code_reaction_name" value="" readonly/>
                                <input hidden type="text" name="code_reaction_id" value="1" readonly/>
                            </div>
                            <div class="input-wrap inline">
                                <p class="input-title"><?= lang('Client.reward_request') ?></p>
                                <input type="text" name="inquirer_comment"
                                       value="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="button-wrap">
                        <a class="button more button-line"
                           href="javascript:openPurchaseItemWrap()"><?= lang('Client.show_more') ?></a>
                    </div>
                </div>
                <div class="payment-box">
                    <div class="form-wrap">
                        <input class="editable" hidden type="text" name="reward_id"
                               value="<?= $reward['id'] ?>"/>
                        <h4 class="page-sub-title"><?= lang('Client.purchaser_info') ?></h4>
                        <div class="input-wrap inquirer">
                            <p class="input-info"><?= lang('Client.purchaser_notice') ?></p>
                            <input class="editable" type="text" name="purchaser_name"
                                   placeholder="<?= lang('Client.purchaser_name') ?>"
                                   value="<?= $user_name ?? '' ?>"/>
                            <input class="editable" type="email" name="purchaser_email"
                                   placeholder="<?= lang('Client.purchaser_email') ?>"
                                   value="<?= $user_email ?? '' ?>"/>
                        </div>
                        <h4 class="page-sub-title"><?= lang('Client.payment_method_select') ?></h4>
                        <div class="input-wrap">
                            <p class="input-info"><?= lang('Client.payment_notice_01') ?></p>
                            <select class="editable" name="pg">`
                                <option value="nice" selected><?= lang('Client.payment_method_nice') ?></option>
                            </select>
                        </div>
                        <h4 class="page-sub-title"><?= lang('Client.payment_expected_price') ?></h4>
                        <div class="total-price">
                            <input class="editable" type="text" name="paid"
                                   value="<?= number_format($reward['price']) ?>" readonly/>
                            <p>KRW</p>
                        </div>
                        <div class="terms">
                            <ul>
                                <li>
                                    <p><?= sprintf(lang("Client.payment_agreement_string"), '<a href="#">' . lang('Client.payment_agreement') . '</a>') ?></p>
                                </li>
                                <li><p><?= lang("Client.payment_notice_02") ?></p></li>
                            </ul>
                        </div>
                    </div>
                    <div class="button-wrap">
                        <a class="button payment button-fill"
                           href="javascript:requestPayment();"><?= lang('Client.payment_proceed') ?></a>
                    </div>
                </div>
            </div>
            <div class="button-wrap">
                <a class="button prev button-line disabled"
                   href="javascript:onClickPrev();"><?= lang('Client.prev') ?></a>
                <a class="button next button-fill" href="javascript:onClickNext();"><?= lang('Client.next') ?></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        loadReward(<?=$project['id']?>)
    });
</script>
