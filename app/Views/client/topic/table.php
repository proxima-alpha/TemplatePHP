<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= $board['alias'] ?>
        </h3>
        <div class="table-box">
            <div class="table-wrap">
                <?php if ($is_login && ($board['is_public'] == 1 || $is_admin)) { ?>
                    <div class="control-button-wrap">
                        <a href="<?= $is_admin_page ? '/admin/board/' . $board['code'] . '/topic/create' : '/board/' . $board['code'] . '/topic/create' ?>"
                           class="button under-line create">
                            <img src="/asset/images/icon/plus.png"/>
                            <span><?= lang('Service.create') ?></span>
                        </a>
                    </div>
                <?php }
                if (\App\Helpers\HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column title"><?= lang('Service.title') ?></span>
                            <span class="column created-at"><?= lang('Service.created_at') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class=" row">
                                <a href="<?= $is_admin_page ? '/admin/topic/' . $item['id'] : '/topic/' . $item['id'] ?>"
                                   class="button row-button">
                                    <span class="column title"><?= $item['title'] ?></span>
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

<?php if (!$is_admin_page) { ?>
    <style>
        .list-box {
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
<?php } ?>
