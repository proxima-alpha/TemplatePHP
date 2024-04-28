<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.manage_reward') ?>
        </h3>
        <div class="reward-wrap">
            <p class="title"><?= $lang == 'ko' ? $reward['title'] : $reward['title_en'] ?></p>
            <p class="content"><?= \App\Helpers\HtmlHelper::covertNewline($lang == 'ko' ? $reward['content'] : $reward['content_en']) ?></p>
        </div>
        <div class="purchase-item-box">
            <ul>
                <?php foreach ($array as $index => $item) { ?>
                    <li class="purchase-item-wrap">
                        <a class="button info" href="javascript:openInputPopup('<?= $item['id'] ?>')">
                            <div class="text-wrap">
                                <p class="title"><?= lang('Client.reward_reaction') ?></p>
                                <p class="content"><?= $lang == 'ko' ? $item['reward_request_name'] : $item['reward_request_name_en'] ?></p>
                            </div>
                            <div class="text-wrap">
                                <p class="title"><?= lang('Service.is_agreed') ?></p>
                                <p class="content"><?= $item['is_agreed'] == 1 ? lang('Service.opened') : lang('Service.closed') ?></p>
                            </div>
                            <div class="text-wrap">
                                <p class="title"><?= lang('Service.name') ?></p>
                                <p class="content"><?= $item['inquirer_name'] ?></p>
                            </div>
                            <div class="text-wrap">
                                <p class="title"><?= lang('Service.email') ?></p>
                                <p class="content"><?= $item['inquirer_email'] ?></p>
                            </div>
                            <div class="text-wrap comment">
                                <p class="title"><?= lang('Client.reward_request') ?></p>
                                <p class="content"><?= $item['inquirer_comment'] ?></p>
                            </div>
                        </a>
                        <div class="uploader">
                            <div class="upload-item-add button"
                                 style="background: url('/asset/images/icon/upload.png') no-repeat center; font-size: 0;">
                                <label for="purchase-<?= $item['id'] ?>-file" class="button"></label>
                                <input hidden type="file" name="file" id="purchase-<?= $item['id'] ?>-file"
                                       onchange="onFileUpload(this,'purchase-<?= $item['id'] ?>');"
                                       accept="video/mp4"/>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <?= \App\Helpers\HtmlHelper::getPagination($pagination, $pagination_link); ?>
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

            html += `
            <div class="input-wrap inline" style="position: relative;">
                <p class="input-title"><?=lang('Client.reward_reaction')?></p>
                <input type="text" name="link" class="under-line" readonly value="<?= $lang == 'ko' ? $item['reward_request_name'] : $item['reward_request_name_en'] ?>">
            </div>`

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
</script>
