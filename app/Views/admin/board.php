<?php

use App\Helpers\HtmlHelper;

?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.board') ?>
        </h3>
        <div class="table-box">
            <div class="table-wrap">
                <?php if ($is_login) { ?>
                    <div class="control-button-wrap">
                        <a href="javascript:openInputPopupCreate();"
                           class="button under-line create">
                            <img src="/asset/images/icon/plus.png"/>
                            <span><?= lang('Service.create') ?></span>
                        </a>
                    </div>
                <?php }
                if (HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column code"><?= lang('Service.code') ?></span>
                            <span class="column type"><?= lang('Service.type') ?></span>
                            <span class="column alias"><?= lang('Service.alias') ?></span>
                            <span class="column public"><?= lang('Service.public') ?></span>
                        </div>
                    </div>
                    <ul>
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row">
                                <a href="javascript:openInputPopup('<?= $item['id'] ?>')" class="button row-button">
                                    <span class="column code"><?= $item['code'] ?></span>
                                    <span class="column type"><?= $item['type'] ?></span>
                                    <span class="column alias"><?= $item['alias'] ?></span>
                                    <span class="column public">
                                    <img
                                        src="/asset/images/icon/<?= $item['is_public'] == 0 ? 'none.png' : 'check.png' ?>"/>
                                </span>
                                </a>
                                <a href="/admin/board/<?= $item['code'] ?>" class="button detail">
                                    <img src="/asset/images/icon/detail@2x.png"/>
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
            return `/api/board/get/${id}`
        },
        getCreateUrl: function () {
            return `/api/board/create`
        },
        getUpdateUrl: function (id) {
            return `/api/board/update/${id}`
        },
        getDeleteUrl: function (id) {
            return `/api/board/delete/${id}`
        },
        getHtml: function (data) {
            let is_editable = !data || data['is_editable'] == 1;
            let typeSet = {
                code: {
                    type: 'text',
                    name: `<?=lang('Service.code')?>`,
                    editable: is_editable,
                    description: is_editable ? `<?=lang('Service.message_info_english')?>` : undefined
                },
                alias: {
                    type: 'text',
                    name: `<?=lang('Service.alias')?>`,
                },
            };
            if (data && data['type'] == 'static') {
                typeSet = {
                    ...typeSet,
                    type: {
                        type: 'select',
                        options: [
                            {
                                value: 'static',
                                name: 'Static',
                            },
                        ],
                        name: `<?=lang('Service.type')?>`,
                        editable: false,
                    },
                };
            } else {
                typeSet = {
                    ...typeSet,
                    type: {
                        type: 'select',
                        options: [
                            {
                                value: 'grid',
                                name: 'Grid',
                            },
                            {
                                value: 'table',
                                name: 'Table',
                            },
                        ],
                        name: `<?=lang('Service.type')?>`,
                        editable: is_editable,
                        description: is_editable ? `<?=lang('Service.message_info_table_style')?>` : undefined
                    },
                }
            }
            typeSet = {
                ...typeSet,
                is_reply: {
                    type: 'bool',
                    name: `<?=lang('Service.reply')?>`,
                    editable: is_editable,
                },
                is_public: {
                    type: 'bool',
                    name: `<?=lang('Service.public')?>`,
                    editable: is_editable,
                    description: is_editable ? `<?=lang('Service.message_info_public')?>` : undefined
                },
                description: {
                    type: 'long-text',
                    name: `<?=lang('Service.description')?>`,
                    editable: is_editable,
                },
            }
            let keys = Object.keys(typeSet);
            let html = ``;

            // copy text button
            if (data) {
                html += `
            <div class="input-wrap inline" style="position: relative;">
                <p class="input-title">Link</p>
                <input type="text" name="link" class="under-line" readonly value="/board/${data['code']}">
                <span class="button float" onclick="window.navigator.clipboard.writeText('/board/${data['code']}')">
                    <img src="/asset/images/icon/copy@2x.png"/>
                </span>
            </div>`
            }
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
            let html = ``;
            html += `
            <a href="javascript:editInputPopup('${key}', ${data['id']});"
               class="button under-line edit">
                <img src="/asset/images/icon/edit.png"/>
                <span>${lang('edit')}</span>
            </a>`;
            if (data['is_deletable'] == 1) {
                html += `
                <a href="javascript:openInputPopupDelete('${key}', ${data['id']});"
                class="button under-line delete">
                    <img src="/asset/images/icon/delete.png"/>
                    <span>${lang('delete')}</span>
                </a>`;
            }
            return html;
        }
    })
</script>
