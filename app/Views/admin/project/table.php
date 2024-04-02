<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('프로젝트') ?>
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
                            <span class="column status"><?= lang('상태') ?></span>
                            <span class="column title"><?= lang('제목') ?></span>
                            <span class="column start-date"><?= lang('시작일') ?></span>
                            <span class="column end-date"><?= lang('종료일') ?></span>
                            <span class="column created-at"><?= lang('Service.created_at') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row">
                                <a href="/admin/artist/<?= $item['id'] ?>/view" class="button row-button">
                                    <span class="column status"><?= $item['status'] ?></span>
                                    <span class="column title"><?= $item['title'] ?></span>
                                    <span class="column start-date"><?= $item['start_date'] ?></span>
                                    <span class="column end-date"><?= $item['end_date'] ?></span>
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
