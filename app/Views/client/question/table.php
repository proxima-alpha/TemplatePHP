<?php

use App\Helpers\HtmlHelper;

HtmlHelper::setTranslations(['inquiry', 'request_comment']);
$is_admin_page = isset($is_admin) && $is_admin;
?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.inquiry') ?>
        </h3>
        <div class="table-box">
            <div class="table-wrap">
                <?php if ($is_login) { ?>
                    <div class="control-button-wrap">
                        <a href="javascript:openQuestionPopupRequest(<?= $board['id'] ?>);"
                           class="button under-line create">
                            <img src="/asset/images/icon/plus_white.png"/>
                            <span><?= lang('Service.create') ?></span>
                        </a>
                    </div>
                <?php }
                if (HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column questioner"><?= lang('Service.questioner') ?></span>
                            <span class="column status"><?= lang('Service.status') ?></span>
                            <span class="column created-at"><?= lang('Service.created_at') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row">
                                <a href="/question/<?= $item['id'] ?>"
                                   class="button row-button">
                                    <span class="column questioner">
                                        <?= $item['questioner_name'] ??
                                            $item['temp_name'] ??
                                            '<img src="/asset/images/icon/none.png"/>' ?>
                                    </span>
                                    <span class="column status">
                                        <span>
                                            <?= lang('Client.' . $item['status']) ?>
                                        </span>
                                    </span>
                                    <span class="column created-at">
                                        <?= $item['created_at'] ?>
                                    </span>
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

