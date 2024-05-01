<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.manage_reward') ?>
        </h3>
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
        <div class="report-wrap">
            <div class="text-wrap">
                <div class="title"><?=lang('Service.total_reward_count')?></div>
                <div class="content"><?=$report['total_count']?></div>
            </div>
            <div class="text-wrap">
                <div class="title"><?=lang('Service.active_reward_count')?></div>
                <div class="content"><?=$report['active_count']?></div>
            </div>
            <div class="text-wrap">
                <div class="title"><?=lang('Service.inactive_reward_count')?></div>
                <div class="content"><?=($report['total_count'] - $report['active_count'])?></div>
            </div>
        </div>
        <div class="table-box">
            <div class="table-wrap">
                <?php if (\App\Helpers\HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column title"><?= lang('Service.title') ?></span>
                            <span class="column remaining-count"><?= lang('Service.remaining_count') ?></span>
                            <span class="column purchased-count"><?= lang('Service.purchased_count') ?></span>
                            <span class="column uploaded-count"><?= lang('Service.uploaded_count') ?></span>
                            <span class="column price"><?= lang('Service.price') ?></span>
                            <span class="column is-deleted"><?= lang('Service.is_deleted') ?></span>
                            <span class="column created-at"><?= lang('Service.created_at') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row">
                                <a href="/admin/project/<?= $project['access_hash'] ?>/reward/get/<?=$item['id']?>" class="button row-button">
                                    <span class="column title"><?= $lang == 'ko' ? $item['title'] : $item['title_en'] ?></span>
                                    <span class="column remaining-count"><?= ($item['total_count'] - $item['purchased_count']) ?></span>
                                    <span class="column purchased-count"><?= $item['total_paid_count'] ?></span>
                                    <span class="column uploaded-count"><?= $item['uploaded_count'] ?></span>
                                    <span class="column price"><?= number_format($item['price']) ?> KRW</span>
                                    <span class="column is-deleted">
                                    <img
                                        src="/asset/images/icon/<?= $item['is_deleted'] == 0 ? 'none.png' : 'check.png' ?>"/>
                                    </span>
                                    <span class="column created-at"><?= $item['created_at'] ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
        <?= \App\Helpers\HtmlHelper::getPagination($pagination, $pagination_link); ?>
    </div>
</div>
