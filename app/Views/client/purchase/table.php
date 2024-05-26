<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Client.purchased_item_list') ?>
        </h3>
        <div class="reward-box">
            <?php if (\App\Helpers\HtmlHelper::showDataEmpty($array)) { ?>
                <ul>
                    <?php foreach ($array as $index => $item) { ?>
                        <li class="reward-wrap">
                            <div class="image-wrap"
                                 style="background: url('/file/<?= $item['project_image_id'] ?> ?>') no-repeat center; background-size: cover; font-size: 0;">
                            </div>
                            <div class="content-wrap">
                                <p class="project-title"><?= $lang == 'ko' ? $item['project_title'] : $item['project_title_en'] ?></p>
                                <p class="title"><?= $lang == 'ko' ? $item['title'] : $item['title_en'] ?></p>
                                <p class="price"><?= number_format($item['price']) ?> KRW</p>
                            </div>
                            <div class="line"></div>
                            <div class="button-wrap">
                                <?php if ($item['is_refunded'] == 1) { ?>
                                    <span class="status refunded"><?= lang('Client.refunded') ?>
                                    </span>
                                <?php } else {
                                    $status = $item['total_reward_count'] == 0 || $item['confirmed_reward_count'] < $item['total_reward_count'] ? 'waiting' : 'finished';?>
                                    <span class="status <?=$status?>"
                                    ><?= lang('Client.status_' . $status) ?>
                                    </span>
                                <?php } ?>
                                <a class="button button-line"
                                   href="/purchase/<?= $item['id'] ?>/view"><?= lang('Client.show_detail') ?> </a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <?= \App\Helpers\HtmlHelper::getPagination($pagination, $pagination_link); ?>
    </div>
</div>
