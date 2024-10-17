<?php

use App\Helpers\HtmlHelper;

?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.manage_reward') ?>
        </h3>
        <div class="project-box">
            <div class="project-wrap">
                <div class="image-wrap"
                     style="background: url('/file/<?= $project['image_id'] ?> ?>') no-repeat center; background-size: cover; font-size: 0;">
                </div>
                <div class="content-wrap">
                    <p class="title"><?= HtmlHelper::getLangItem($project, 'title', $lang) ?></p>
                    <div class="line"></div>
                    <div class="access-url-wrap">
                        <p class="title"><?= lang('Service.access_url') ?></p>
                        <p class="access-url"><?= $_ENV['app.baseURL'] . '/admin/project/' . $project['access_hash'] . '/reward/1' ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="report-wrap">
            <div class="text-wrap">
                <div class="title"><?= lang('Service.total_reward_count') ?></div>
                <div class="content"><?= $report['total_count'] ?></div>
            </div>
            <div class="text-wrap">
                <div class="title"><?= lang('Service.active_reward_count') ?></div>
                <div class="content"><?= $report['active_count'] ?></div>
            </div>
            <div class="text-wrap">
                <div class="title"><?= lang('Service.inactive_reward_count') ?></div>
                <div class="content"><?= ($report['total_count'] - $report['active_count']) ?></div>
            </div>
        </div>
        <div class="table-box">
            <div class="table-wrap">
                <?php if (HtmlHelper::showDataEmpty($array)) { ?>
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
                                <a href="/admin/project/<?= $project['access_hash'] ?>/reward/get/<?= $item['id'] ?>"
                                   class="button row-button">
                                    <span
                                        class="column title"><?= HtmlHelper::getLangItem($item, 'title', $lang) ?></span>
                                    <span
                                        class="column remaining-count"><?= ($item['total_count'] - $item['purchased_count']) ?></span>
                                    <span class="column purchased-count"><?= $item['purchased_count'] ?></span>
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
        <?= HtmlHelper::getPagination($pagination, $pagination_link); ?>
    </div>
</div>
