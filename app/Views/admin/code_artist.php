<?php

?>
<div class="container-inner code-artist code">
    <div class="container-wrap">
        <h4 class="page-sub-title">
            <?= lang('아티스트 분류 설정') ?>
        </h4>
        <div class="info-text-wrap">
            <?= lang('Service.message_info_drag') ?>
        </div>
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
                if (\App\Helpers\HtmlHelper::showDataEmpty($array)) { ?>
                    <div class="row-title">
                        <div class="row">
                            <span class="column code"><?= lang('Service.code') ?></span>
                            <span class="column name"><?= lang('Service.name') ?></span>
                            <span class="column name-en"><?= lang('Service.name_en') ?></span>
                        </div>
                    </div>
                    <ul class="code-artist">
                        <?php foreach ($array as $index => $item) { ?>
                            <li class="row draggable-item" draggable="true">
                                <input hidden type="text" value="<?= $item['id'] ?>"/>
                                <a href="javascript:openInputPopup('<?= $item['id'] ?>')" class="button row-button">
                                    <span class="column code"><?= $item['code'] ?></span>
                                    <span class="column name"><?= $item['name'] ?></span>
                                    <span class="column name-en"><?= $item['name_en'] ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    /**
     * admin/popup_input
     */
    initializeInputPopup({
        getGetUrl: function (id) {
            return `/api/code/artist/get/${id}`
        },
        getCreateUrl: function () {
            return `/api/code/artist/create`
        },
        getUpdateUrl: function (id) {
            return `/api/code/artist/update/${id}`
        },
        getDeleteUrl: function (id) {
            return `/api/code/artist/delete/${id}`
        },
        getHtml: function (data) {
            const typeSet = {
                code: {
                    type: 'text',
                    name: `<?=lang('Service.code')?>`,
                    editable: false,
                },
                name: {
                    type: 'text',
                    name: `<?=lang('Service.name')?>`,
                },
                name_en: {
                    type: 'text',
                    name: `<?=lang('Service.name_en')?>`,
                },
            }
            let keys = Object.keys(typeSet);
            let html = ``;

            for (let i in keys) {
                let key = keys[i];
                let extracted = fromDataToHtml(key, data, typeSet);
                if (extracted) {
                    html += extracted;
                }
            }
            return html;
        },
        deleteMessage: `<?=lang('Service.message_popup_delete_topic')?>`,
    })

    $(document).ready(function () {
        $(`.container-inner .table-wrap ul.code-artist`).initDraggable({
            onDragFinished: async function (from, to) {
                if (from.getElementsByTagName("input").length == 0 || to.getElementsByTagName("input") == 0) return;
                let fromValue = from.getElementsByTagName("input")[0].value;
                let toValue = to.getElementsByTagName("input")[0].value;
                let isSuccess = false;
                await apiRequest({
                    type: 'GET',
                    url: `/api/code/artist/exchange-priority/${fromValue}/${toValue}`,
                    dataType: 'json',
                    success: function (response, status, request) {
                        if (!response.success) return;
                        isSuccess = true;
                    },
                    error: function (response, status, error) {
                    },
                });
                return isSuccess;
            }
        })
    })
</script>
