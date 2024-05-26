<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.project') ?>
        </h3>
        <div class="table-box">
            <div class="table-wrap">
                <?php if ($is_login) { ?>
                    <div class="control-button-wrap">
                        <a href="/admin/project/create"
                           class="button under-line create">
                            <img src="/asset/images/icon/plus.png"/>
                            <span><?= lang('Service.create') ?></span>
                        </a>
                    </div>
                <?php }
                if (\App\Helpers\HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column category"><?= lang('Service.category') ?></span>
                            <span class="column status"><?= lang('Service.status') ?></span>
                            <span class="column title"><?= lang('Service.title') ?></span>
                            <span class="column start-date"><?= lang('Service.start_date') ?></span>
                            <span class="column end-date"><?= lang('Service.end_date') ?></span>
                            <span class="column created-at"><?= lang('Service.created_at') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row">
                                <a href="/admin/project/<?= $item['id'] ?>/view" class="button row-button">
                                    <span class="column category"><?= $lang == 'ko' ? $item['code_project'] : $item['code_project_en'] ?></span>
                                    <span class="column status"><?= $item['status'] ?></span>
                                    <span class="column title"><?= $lang == 'ko' ? $item['title'] : $item['title_en'] ?></span>
                                    <span class="column start-date"><?= \App\Helpers\HtmlHelper::toDateString($item['start_date']) ?></span>
                                    <span class="column end-date"><?= \App\Helpers\HtmlHelper::toDateString($item['end_date']) ?></span>
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
