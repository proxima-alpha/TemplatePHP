<?php

use App\Helpers\HtmlHelper;
use Crisu83\ShortId\ShortId;

$shortid = ShortId::create();
$identifier = $shortid->generate();

HtmlHelper::setTranslations([
    'message_info_drag',
    'category',
    'title',
    'status',
    'message_item_already_selected',
    'message_item_select',
    'search_artist',
    'assigned',
]);
?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.guide') ?>
        </h3>
        <div class="content-box how_to_use">
            <h4 class="page-sub-title">
                <?= lang('Client.guide_title') ?>
            </h4>
            <div class="content-wrap">
            </div>
            <div class="line"></div>
            <div class="control-button-wrap">
                <a href="javascript:editSetting('how_to_use');"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span><?= lang('Service.edit') ?></span>
                </a>
            </div>
        </div>
    </div>
</div>
