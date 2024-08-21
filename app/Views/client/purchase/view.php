<?php

use App\Helpers\HtmlHelper;

?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Client.purchased_item') ?>
        </h3>
        <div class="purchase-item-box">
            <div class="reward-wrap">
                <div class="image-wrap"
                     style="background: url('/file/<?= $project['image_id'] ?> ?>') no-repeat center; background-size: cover; font-size: 0;">
                </div>
                <div class="content-wrap">
                    <p class="project-title"><?= HtmlHelper::getLangItem($project, 'title', $lang) ?></p>
                    <p class="title"><?= HtmlHelper::getLangItem($reward, 'title', $lang) ?></p>
                    <p class="price"><?= number_format($purchase_item['price']) ?> KRW</p>
                </div>
            </div>
            <div class="line"></div>
            <div class="purchase-item-wrap">
                <div class="text-wrap">
                    <p class="title"><?= lang('Service.name') ?></p>
                    <p class="content"><?= $purchase_item['inquirer_name'] ?></p>
                </div>
                <div class="text-wrap">
                    <p class="title"><?= lang('Service.email') ?></p>
                    <p class="content"><?= $purchase_item['inquirer_email'] ?></p>
                </div>
                <div class="text-wrap">
                    <p class="title"><?= lang('Client.reward_reaction') ?></p>
                    <p class="content"><?= $code_reward_request['name'] ?></p>
                </div>
                <div class="text-wrap comment">
                    <p class="title"><?= lang('Client.reward_request') ?></p>
                    <p class="content"><?= HtmlHelper::covertNewline($purchase_item['inquirer_comment']) ?></p>
                </div>
            </div>
            <div class="line"></div>
            <div class="purchase-wrap">
                <div class="text-wrap">
                    <p class="title"><?= lang('Client.purchaser_name') ?></p>
                    <p class="content"><?= $purchase['purchaser_name'] ?></p>
                </div>
                <div class="text-wrap">
                    <p class="title"><?= lang('Client.purchaser_email') ?></p>
                    <p class="content"><?= $purchase['purchaser_email'] ?></p>
                </div>
            </div>
            <div class="line"></div>
            <?php if ($purchase_item['is_refunded'] == 1) { ?>
                <div class="button-wrap">
                    <a class="button button-fill refunded"><?= lang('Client.refunded') ?>
                    </a>
                </div>
            <?php } else if (isset($purchase_item_reward)) {
                foreach ($purchase_item_reward as $index => $item) {
                    $url = isset($item['image_id']) ? '/file/' . $item['image_id'] : '/asset/images/custom/object.svg'; ?>
                    <div class="artist-box">
                        <div class="image-item-wrap"
                             style="background: url('<?= $url ?>') no-repeat center; background-size: cover; font-size: 0;"></div>
                        <div class="text-wrap">
                            <p><?= HtmlHelper::getLangItem($item, 'name', $lang) ?></p>
                        </div>
                        <?php if ($item['status'] == 'waiting' || ($item['reward_file_id'])) { ?>
                            <a class="button button-fill <?= $item['status'] == 'waiting' ? 'disabled' : '' ?>"
                               href="/reward-file/<?= $item['reward_file_id'] ?>/download"><?= lang('Client.status_' . $item['status']) ?>
                            </a>
                        <?php } else { ?>
                            <a class="button button-fill disabled"><?= lang('Client.status_expired') ?></a>
                        <?php } ?>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>
