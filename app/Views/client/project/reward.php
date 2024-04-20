<?php
\App\Helpers\ServerLogger::log(array_merge(['Service' => [1, 2, 3]], ['Service' => [4, 5, 6]]));
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
], 'Client');
?>
<script type="text/javascript">
    <?php if (isset($reward_requests)) {
    foreach ($reward_requests as $index => $item) { ?>
    rewardRequests[`<?=$item['id']?>`] = {
        'id': `<?=$item['id']?>`,
        'code': `<?=$item['code']?>`,
        'name': `<?=$item['name']?>`,
    };
    <?php }
    }?>
</script>
<div class="container-inner">
    <div class="container-wrap">
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
                <div class="select-payment-box">
                    <div class="limited-count-wrap">
                        <span class="title"><?= lang('구매가능한 수량') ?> </span>
                        <span class="content"><?= $reward['limited_count'] ?></span>
                    </div>
                    <input type="number" name="count" class="editable" value="1"
                           onchange="onCountChange(this, <?= $reward['price'] ?>)"/>
                    <div class="total-price">
                        <input type="number" name="price" value="<?= $reward['price'] ?>" readonly/>
                        <p>KRW</p>
                    </div>
                </div>
            </div>
            <div class="page" id="page-2" style="display: none">
                <h4 class="page-sub-title">
                    <?= lang('리워드 선택') ?>
                </h4>
                <div class="purchase-item-wrap">
                    <form class="form-wrap">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Client.reward_purchase_item_01') ?></p>
                            <select class="editable" name="code_reward_request_id" value="1">
                                <option value="1"><?= lang('생일') ?></option>
                                <option value="2"><?= lang('기념일') ?></option>
                                <option value="3"><?= lang('응원') ?></option>
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
                                   placeholder="<?= lang('Client.reward_purchase_item_03_1') ?>"/>
                            <input class="editable" type="email" name="inquirer_email"
                                   placeholder="<?= lang('Client.reward_purchase_item_03_2') ?>"/>
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
                        <?= lang('선택한 상품') ?>
                    </h4>
                    <?php if (isset($project)) { ?>
                        <div class="project-wrap">
                            <div class="image-wrap"
                                 style="background: url('/file/<?= $project['project_image_id'] ?> ?>') no-repeat center; background-size: cover; font-size: 0;">
                            </div>
                            <div class="content-wrap">
                                <p class="title"><?= $project['title'] ?></p>
                                <div class="line"></div>
                                <p class="content"><?= \App\Helpers\HtmlHelper::covertNewline($project['content']) ?></p>
                            </div>
                        </div>
                    <?php }
                    if (isset($reward)) { ?>
                        <div class="reward-wrap">
                            <p class="title"><?= $reward['title'] ?></p>
                            <p class="content"><?= \App\Helpers\HtmlHelper::covertNewline($reward['content']) ?></p>
                            <p class="total-count"><?= $reward['total_count'] ?><?= lang('개 한정') ?></p>
                            <div class="line"></div>
                            <p class="price"><?= $reward['price'] ?> KRW</p>
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
                        <a class="button more button-line" href="javascript:openPurchaseItemWrap()"><?= lang('Client.show_more') ?></a>
                    </div>
                </div>
                <div class="payment-box">
                    <div class="form-wrap">
                        <h4 class="page-sub-title"><?= lang('주문자 정보') ?></h4>
                        <div class="input-wrap inquirer">
                            <p class="input-info"><?= lang('주문자 정보로 결제관련 정보가 제공됩니다. 정확한 정보로 입력되어 있는지 확인해 주세요.') ?></p>
                            <input type="text" name="inquirer_name" placeholder="<?= lang('구매하는 사람의 이름') ?>"/>
                            <input type="email" name="inquirer_email" placeholder="<?= lang('구매하는 사람의 이메일') ?>"/>
                        </div>
                        <h4 class="page-sub-title"><?= lang('결제 수단 선택') ?></h4>
                        <div class="input-wrap">
                            <p class="input-info"><?= lang('결제 도중 결제창을 닫거나 브라우저를 종료하는 경우, 결제가 정상적으로 완료되지 않을 수 있습니다. 해외 발급 카드로 결제시에는 언어 설정을 외국어로 변경하여 결제를 진행해 주세요.') ?></p>
                            <select class="editable" name="payment_method" value="1">`
                                <option value="1"><?= lang('나이스페이') ?></option>
                            </select>
                        </div>
                        <h4 class="page-sub-title"><?= lang('결제 예정 금액') ?></h4>
                        <div class="total-price">
                            <input type="number" name="price" value="<?= $reward['price'] ?>" readonly/>
                            <p>KRW</p>
                        </div>
                        <div class="terms">
                            <ul>
                                <li>
                                    <p><?= sprintf(lang("%s에 동의합니다"), '<a href="#">' . lang('주문정보 및 서비스 이용약관') . '</a>') ?></p>
                                </li>
                                <li><p><?= lang("모든 금액은 원화로 결제되며, 환율에 따라 주문 금액과 결제 금액의 차이가 발생할 수 있습니다.") ?></p></li>
                            </ul>
                        </div>
                    </div>
                    <div class="button-wrap">
                        <a class="button payment button-fill" href="#"><?= lang('결제하기') ?></a>
                    </div>
                </div>
            </div>
            <div class="button-wrap">
                <a class="button prev button-line disabled"
                   href="javascript:onClickPrev();"><?= lang('이전') ?></a>
                <a class="button next button-fill" href="javascript:onClickNext();"><?= lang('다음') ?></a>
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
