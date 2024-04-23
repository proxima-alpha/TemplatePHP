<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Client.purchased_item') ?>
        </h3>
        <div class="purchase-item-box">
            <div class="reward-wrap">
                <div class="image-wrap"
                     style="background: url('/file/<?= $project['project_image_id'] ?> ?>') no-repeat center; background-size: cover; font-size: 0;">
                </div>
                <div class="content-wrap">
                    <p class="project-title"><?= $project['title'] ?></p>
                    <p class="title"><?= $reward['title'] ?></p>
                    <p class="price"><?= $purchase_item['price'] ?> KRW</p>
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
                    <p class="content"><?= $purchase_item['inquirer_comment'] ?></p>
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
            <div class="button-wrap">
                <a class="button button-fill <?= $purchase_item['status'] == 'confirmed' ? '' : 'disabled' ?>"
                   onclick="onClickDownload()}"><?= lang('Client.status_' . $purchase_item['status']) ?>
                </a>
            </div>
        </div>
    </div>
</div>
