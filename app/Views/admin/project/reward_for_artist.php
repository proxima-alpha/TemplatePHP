<?php

use App\Helpers\HtmlHelper;

?>
<div class="container-inner">
    <div class="container-wrap">
        <h3 class="page-title">
            <?= lang('Service.manage_reward') ?>
        </h3>
        <div class="reward-box">
            <div class="reward-wrap">
                <p class="title"><?= HtmlHelper::getLangItem($reward, 'title', $lang) ?>
                    (<?= $reward['type'] == 'random' ? lang('Service.reward_type_random') : lang('Service.reward_type_all') ?>
                    )</p>
                <p class="content"><?= HtmlHelper::covertNewline(HtmlHelper::getLangItem($reward, 'content', $lang)) ?></p>
            </div>
            <div class="count-wrap">
                <div class="count-inner-wrap">
                    <div class="text-wrap">
                        <p class="title"><?= lang('Service.remaining_count') ?></p>
                        <p class="content"><?= ($reward['total_count'] - $reward['purchased_count']) ?></p>
                    </div>
                    <div class="text-wrap">
                        <p class="title"><?= lang('Service.purchased_count') ?></p>
                        <p class="content"><?= ($reward['total_paid_count']) ?></p>
                    </div>
                    <div class="text-wrap">
                        <p class="title"><?= lang('Service.uploaded_count') ?></p>
                        <p class="content"><?= ($reward['uploaded_count']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-wrap">
            <p><?= lang('Service.artist') ?></p>
            <div class="artist-filter">
                <a class="button out-line <?= !isset($selected_artist_id) ? 'selected' : '' ?>"
                   href="javascript:selectFilterArtist()"><?= lang('Service.all') ?></a>
                <?php if (isset($artists)) {
                    foreach ($artists as $artist) { ?>
                        <a class="button out-line <?= (isset($selected_artist_id) && $selected_artist_id == $artist['id']) ? 'selected' : '' ?>"
                           href="javascript:selectFilterArtist(<?= $artist['id'] ?>)">
                            <?= HtmlHelper::getLangItem($artist, 'name', $lang) ?>
                        </a>
                    <?php }
                } ?>
            </div>
        </div>
        <?php if (HtmlHelper::showDataEmpty($array)) { ?>
            <div class="purchase-item-box">
                <ul>
                    <?php foreach ($array as $index => $item) { ?>
                        <li id="<?= $item['id'] ?>">
                            <div class="purchase-item-wrap">
                                <a class="button info" href="javascript:openInputPopup('<?= $item['id'] ?>')">
                                    <div class="text-wrap">
                                        <p class="title"><?= lang('Service.artist') ?></p>
                                        <p class="content"><?= HtmlHelper::getLangItem($item, 'artist_name', $lang) ?></p>
                                    </div>
                                    <div class="text-wrap">
                                        <p class="title"><?= lang('Client.reward_reaction') ?></p>
                                        <p class="content"><?= HtmlHelper::getLangItem($item, 'reward_request_name', $lang) ?></p>
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
                                        <p class="content"><?= HtmlHelper::covertNewline($item['inquirer_comment']) ?></p>
                                    </div>
                                </a>
                                <?php if ($item['status'] == 'waiting' && !isset($item['reward_file_id'])) { ?>
                                    <div class="uploader">
                                        <div class="upload-item-add button">
                                            <div
                                                style="background: url('/asset/images/icon/upload.svg') rgba(255, 255, 255, 0.7) no-repeat center / 60%; font-size: 0; background-position-y: 25%;">
                                                <label for="purchase-<?= $item['id'] ?>-file" class="button"></label>
                                                <input hidden type="file" name="file"
                                                       id="purchase-<?= $item['id'] ?>-file"
                                                       onchange="onRewardFileUpload(this,<?= $item['id'] ?>);"
                                                       accept="video/mp4"/>
                                                <p><?= lang('Service.upload_file') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php } else if ($item['status'] != 'received') { ?>
                                    <div class="uploader uploaded">
                                        <div class="upload-item-add button">
                                            <div
                                                style="background: url('/asset/images/icon/check.svg') rgba(255, 255, 255, 0.7) no-repeat center / 60%; font-size: 0; background-position-y: 25%;">
                                                <label for="purchase-<?= $item['id'] ?>-file" class="button"></label>
                                                <input hidden type="file" name="file"
                                                       id="purchase-<?= $item['id'] ?>-file"
                                                       onchange="onRewardFileUpload(this,<?= $item['id'] ?>);"
                                                       accept="video/mp4"/>
                                                <p><?= isset($item['reward_file_id']) ? lang('Service.reupload_file') :
                                                        (lang('Service.reupload_file') . '</br>' . lang('Service.file_deleted')) ?></p>
                                            </div>
                                            <?php if (isset($item['reward_file_poster'])) { ?>
                                                <span
                                                    style="background: url('<?= $item['reward_file_poster'] ?>') no-repeat center; background-size: cover; font-size: 0;"></span>
                                            <?php } ?>
                                        </div>
                                        <div class="button-wrap">
                                            <a href="/reward-file/<?= $item['reward_file_id'] ?>"
                                               class="button under-line download">
                                                <img src="/asset/images/icon/download.png"/>
                                                <span><?= lang('Service.download_file') ?></span>
                                            </a>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="uploader finished">
                                        <div class="upload-item-add">
                                            <div
                                                style="background: url('/asset/images/icon/check_circle.svg') rgba(255, 255, 255, 0.7) no-repeat center / 60%; font-size: 0; background-position-y: 25%;">
                                                <p><?= isset($item['reward_file_id']) ? lang('Service.downloaded') :
                                                        (lang('Service.downloaded') . '</br>' . lang('Service.file_deleted')) ?></p>
                                            </div>
                                            <?php if (isset($item['reward_file_poster'])) { ?>
                                                <span
                                                    style="background: url('<?= $item['reward_file_poster'] ?>') no-repeat center; background-size: cover; font-size: 0;"></span>
                                            <?php } ?>
                                        </div>
                                        <?php if (isset($item['reward_file_id'])) { ?>
                                            <div class="button-wrap">
                                                <a href="/reward-file/<?= $item['reward_file_id'] ?>"
                                                   class="button under-line download">
                                                    <img src="/asset/images/icon/download.png"/>
                                                    <span><?= lang('Service.download_file') ?></span>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if (isset($item['reward_file_id']) && $is_admin) { ?>
                                <div class="control-button-wrap">
                                    <?php if ($item['status'] == 'waiting') { ?>
                                        <a href="javascript:confirmReward(<?= $item['id'] ?>)"
                                           class="button under-line delete">
                                            <img src="/asset/images/icon/check.png"/>
                                            <span><?= lang('Service.reward_confirm') ?></span>
                                        </a>
                                    <?php }
                                    if (isset($item['reward_file_id'])) { ?>
                                        <a href="javascript:openPopupDelete('/api/reward-file/delete/<?= $item['reward_file_id'] ?>')"
                                           class="button under-line delete">
                                            <img src="/asset/images/icon/delete.png"/>
                                            <span><?= lang('Service.delete_file') ?></span>
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <?= HtmlHelper::getPagination($pagination, $pagination_link); ?>
    </div>
</div>

<script type="text/javascript">
    /**
     * admin/popup_input
     */
    initializeInputPopup({
        getGetUrl: function (id) {
            return `/api/purchase-item-reward/get/${id}`
        },
        getUpdateUrl: function (id) {
            return `/api/purchase-item-reward/update/${id}`
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

            const language = getCookie('lang')
            html += `
            <div class="input-wrap inline" style="position: relative;">
                <p class="input-title"><?=lang('Service.artist')?></p>
                <input type="text" name="artist" class="under-line" readonly value="${getLangItem(data, 'artist_name', language) ?? ""}">
            </div>`
            html += `
            <div class="input-wrap inline" style="position: relative;">
                <p class="input-title"><?=lang('Service.artist')?></p>
                <input type="text" name="reward_request" class="under-line" readonly value="${getLangItem(data, 'reward_request_name', language)}">
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

    function selectFilterArtist(id) {
        if (!id) return window.location.replace(window.location.pathname)
        window.location.replace(`${window.location.pathname}?artist_id=${id}`)
    }
</script>
