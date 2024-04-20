<div class="container-inner">
    <div class="container-wrap">
        <div class="content-box">
            <div class="page page-1" style="display: none;">
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
            <div class="page page-2">
                <div class="request-wrap">
                    <h4 class="page-sub-title">
                        <?= lang('리워드 선택') ?>
                    </h4>
                    <div class="form-wrap">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('어떤 유형의 영상을 원하시나요?') ?></p>
                            <select class="editable" name="code_reward_request" value="1">`
                                <option value="1"><?=lang('생일')?></option>
                                <option value="2"><?=lang('기념일')?></option>
                                <option value="3"><?=lang('응원')?></option>
                            </select>
                        </div>
                        <div class="input-wrap mine">
                            <p class="input-title"><?= lang('이 메세지의 대상이 누구인가요?') ?></p>
                            <div class="input-item-wrap">
                                <input type="radio" id="is-mine-true" name="is_mine" value="1" checked>
                                <label for="is-mine-true"><?= lang('나를 위한 영상이에요!') ?></label>
                            </div>
                            <div class="input-item-wrap">
                                <input type="radio" id="is-mine-false" name="is_mine" value="0">
                                <label for="is-mine-false"><?= lang('다른 사람을 위한 영상이예요!') ?></label>
                            </div>
                        </div>
                        <div class="input-wrap inquirer">
                            <p class="input-title"><?= lang('영상을 받는 사람이 누구인가요?') ?></p>
                            <input type="text" name="inquirer_name" placeholder="<?=lang('받는 사람의 이름 또는 별명')?>"/>
                            <input type="email" name="inquirer_email" placeholder="<?=lang('받는 사람의 이메일')?>"/>
                        </div>
                        <div class="input-wrap comment">
                            <p class="input-title"><?= lang('요청메세지를 작성하세요!') ?></p>
                            <textarea class="editable" name="inquirer_comment"></textarea>
                        </div>
                        <div class="input-wrap inline agree">
                            <input type="checkbox" id="is-agree" name="is_agreed"/>
                            <label for="is-agree" class="input-title"><?= lang('영상 비공개 (상세페이지에 노출 되지 않습니다)') ?></label>
                        </div>
                    </div>
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
                        <img src="/asset/images/custom/check.svg"/>
                    </div>
                    <div class="text-wrap">
                        <?= lang('Service.reward_step_01') ?>
                    </div>
                </div>
                <div class="step-wrap">
                    <div class="circle">
                        <img src="/asset/images/custom/check.svg"/>
                    </div>
                    <div class="text-wrap">
                        <?= lang('Service.reward_step_02') ?>
                    </div>
                </div>
                <div class="step-wrap">
                    <div class="circle">
                        <img src="/asset/images/custom/check.svg"/>
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
