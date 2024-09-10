<?php

use App\Helpers\HtmlHelper;

HtmlHelper::setTranslations(['refund', 'message_popup_refund', 'select_date'])
?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.purchase') ?>
        </h3>
        <div class="filter-wrap">
            <div class="control-button-wrap">
                <a href="/purchase-item-file<?= strlen($_SERVER['QUERY_STRING']) > 0 ? '?' . $_SERVER['QUERY_STRING'] : '' ?>"
                   class="button under-line create">
                    <img src="/asset/images/icon/download.png"/>
                    <span><?= lang('Service.filter_download_excel') ?></span>
                </a>
            </div>
            <div class="input-wrap column">
                <p class="input-title"><?= lang('Service.filter_start_date') ?></p>
                <a class="button" href="javascript:openCalendarPopup('start_date')">
                    <input class="editable" name="start_date"
                           value="<?= HtmlHelper::toDateString($start_date ?? null) ?>"
                           readonly>
                </a>
            </div>
            <div class="input-wrap column">
                <p class="input-title"><?= lang('Service.filter_end_date') ?></p>
                <a class="button" href="javascript:openCalendarPopup('end_date')">
                    <input class="editable" name="end_date"
                           value="<?= HtmlHelper::toDateString($end_date ?? null) ?>"
                           readonly>
                </a>
            </div>
        </div>
        <div class="table-box">
            <div class="table-wrap">
                <?php if (HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column purchase-number"><?= lang('Service.purchase_number') ?></span>
                            <span class="column name"><?= lang('Service.name') ?></span>
                            <span class="column reward"><?= lang('Client.reward') ?></span>
                            <span class="column price"><?= lang('Service.price') ?></span>
                            <span class="column status"><?= lang('Service.status') ?></span>
                            <span class="column channel"><?= lang('Service.channel') ?></span>
                            <span class="column language"><?= lang('Service.lang') ?></span>
                            <span class="column created-at"><?= lang('Service.created_at') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row">
                                <a href="javascript:openInputPopup('<?= $item['id'] ?>')" class="button row-button">
                                    <span class="column purchase-number"><?= $item['id'] ?></span>
                                    <span class="column name"><?= $item['user_name'] ?></span>
                                    <span
                                        class="column reward"><?= HtmlHelper::getLangItem($item, 'title', $lang) ?></span>
                                    <span class="column price"><?= number_format($item['price']) ?> KRW</span>
                                    <span class="column status"
                                          style="<?= $item['is_refunded'] == 1 ? 'color:red;' : 'color:green;' ?>"><?= $item['is_refunded'] == 1 ? lang('Service.refunded') : lang('Service.paid') ?></span>
                                    <span class="column channel"><?= HtmlHelper::getPaymentChannel($item['channel']) ?></span>
                                    <span class="column language"><?= $item['lang'] ?></span>
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

<script type="text/javascript">
    /**
     * admin/popup_input
     */
    initializeInputPopup({
        getGetUrl: function (id) {
            return `/api/purchase-item/get/${id}`
        },
        getUpdateUrl: function (id) {
            return `/api/purchase-item/update/${id}`
        },
        getDeleteUrl: function (id) {
            return `/api/purchase-item/refund/${id}`
        },
        getHtml: function (data) {
            let typeSet = {
                is_mine: {
                    type: 'bool',
                    name: `<?=lang('Service.is_mine')?>`,
                    editable: false,
                },
                is_agreed: {
                    type: 'bool',
                    name: `<?=lang('Service.is_agreed')?>`,
                    editable: false,
                },
                inquirer_name: {
                    type: 'text',
                    name: `<?=lang('Service.name')?>`,
                    editable: false,
                },
                inquirer_email: {
                    type: 'text',
                    name: `<?=lang('Service.email')?>`,
                    editable: false,
                },
                lang: {
                    type: 'text',
                    name: `<?=lang('Service.lang')?>`,
                },
                inquirer_comment: {
                    type: 'long-text',
                    name: `<?=lang('Client.reward_request')?>`,
                    editable: false,
                },
                memo: {
                    type: 'long-text',
                    name: `<?=lang('Service.memo')?>`,
                },
            };
            let keys = Object.keys(typeSet);
            let html = ``;

            <?php if(isset($item)) {?>
            html += `
            <div class="input-wrap inline" style="position: relative;">
                <p class="input-title"><?=lang('Client.reward_reaction')?></p>
                <input type="text" name="link" class="under-line" readonly value="<?= HtmlHelper::getLangItem($item, 'reward_request_name', $lang) ?>">
            </div>`
            <?php } ?>

            for (let i in keys) {
                let key = keys[i];
                let extracted = fromDataToHtml(key, data, typeSet);
                if (extracted) {
                    html += extracted;
                }
            }
            return html;
        },
        getControlHtml: function (key, data) {
            const isAdmin = getCookie('is_admin')
            let html = ``;
            if (isAdmin) {
                if (data['is_refunded'] == 0) {
                    html += `
                    <a href="javascript:openInputPopupDelete('${key}', ${data['id']});"
                       class="button under-line delete">
                        <img src="/asset/images/icon/cancel.png"/>
                        <span>${lang('refund')}</span>
                    </a>`
                }
                html += `
                <a href="javascript:editInputPopup('${key}', ${data['id']});"
                   class="button under-line edit">
                    <img src="/asset/images/icon/edit.png"/>
                    <span>${lang('edit')}</span>
                </a>`;
            }
            return html;
        }
    })

    /**
     * override
     * @param key
     * @param id
     * @returns {Promise<void>}
     */
    async function openInputPopupDelete(key, id) {
        let className = `${key}-popup-delete`;
        let css = await loadStyleFile('/asset/css/common/popup/delete.css', "." + className);
        let html = `
    <div class="text-wrap">
        ${initContainer[key].deleteMessage ?? lang('message_popup_refund')}
    </div>
    <div class="error-message-wrap"></div>
    <div class="button-wrap controls">
        <a href="javascript:closePopup('${className}')" class="button cancel white">${lang('cancel')}</a>
        <a href="javascript:confirmInputPopupDelete('${key}', ${id})" class="button confirm black">${lang('refund')}</a>
    </div>`;
        openPopup({
            className: className,
            style: `<style>${css}</style>`,
            html: html,
        })
    }
</script>
