<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.artist') ?>
        </h3>
        <div class="table-box">
            <div class="table-wrap">
                <?php if ($is_login) { ?>
                    <div class="control-button-wrap">
                        <a href="/admin/artist/create"
                           class="button under-line create">
                            <img src="/asset/images/icon/plus.png"/>
                            <span><?= lang('Service.create') ?></span>
                        </a>
                    </div>
                <?php }
                if (\App\Helpers\HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column name"><?= lang('Service.name') ?></span>
                            <span class="column job"><?= lang('Service.job') ?></span>
                            <span class="column created-at"><?= lang('Service.created_at') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row">
                                <a href="/admin/artist/<?= $item['id'] ?>/view" class="button row-button">
                                    <span class="column name"><?= $lang == 'ko' ? $item['name'] : $item['name_en'] ?></span>
                                    <span class="column job"><?= $lang == 'ko' ? $item['job'] : $item['job_en'] ?></span>
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
