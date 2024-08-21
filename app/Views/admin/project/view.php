<?php

use App\Helpers\HtmlHelper;

HtmlHelper::setTranslations([
    'title',
    'content',
    'price',
    'stock_count',
    'available_count',
    'select_date',
    'category',
    'job',
    'artist',
    'reward_type_all',
    'reward_type_random',
]);
?>
<div class="container-inner">
    <div class="container-wrap">
        <div class="project-wrap">
            <div class="form-wrap project">
                <div class="input-wrap inline">
                    <p class="input-title"><?= lang('Service.category') ?></p>
                    <select class="editable" name="code_project_id" value="1" disabled>`
                        <option value="1">
                            <?= HtmlHelper::getLangItem($data, 'code_project', $lang) ?>
                        </option>
                    </select>
                </div>
                <div class="tab-box">
                    <div class="tab-button-wrap">
                        <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
                        <a class="button en" onclick="clickTab(this,'en')">English</a>
                        <a class="button jp" onclick="clickTab(this,'jp')">日本語</a>
                    </div>
                    <div class="tab-wrap ko active">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.title') ?></p>
                            <input type="text" name="title" class="editable under-line" value="<?= $data['title'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.content') ?></p>
                            <div class="quill-html ql-container"><?= $data['content'] ?></div>
                        </div>
                    </div>
                    <div class="tab-wrap en">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.title') ?></p>
                            <input type="text" name="title_en" class="editable under-line"
                                   value="<?= $data['title_en'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.content') ?></p>
                            <div class="quill-html ql-container"><?= $data['content_en'] ?></div>
                        </div>
                    </div>
                    <div class="tab-wrap jp">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.title') ?></p>
                            <input type="text" name="title_jp" class="editable under-line"
                                   value="<?= $data['title_jp'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.content') ?></p>
                            <div class="quill-html ql-container"><?= $data['content_jp'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="line"></div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('Service.project_image') ?> (268 X 318)</p>
                    <?= HtmlHelper::getSingleMediaUploader('image', $data['image_file'] ?? null, 'view') ?>
                </div>
                <div class="input-uploader-wrap">
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.project_background') ?> (1500 X 300)</p>
                        <?= HtmlHelper::getSingleMediaUploader('background', $data['background_file'] ?? null, 'view') ?>
                    </div>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.project_background') ?> (<?= lang('Service.mobile') ?>) (840 X 300)</p>
                        <?= HtmlHelper::getSingleMediaUploader('background_mobile', $data['background_mobile_file'] ?? null, 'view') ?>
                    </div>
                </div>
                <div class="line"></div>
                <p class="input-description"><?= lang('Service.start_date') ?></p>
                <div class="input-wrap column calendar">
                    <p class="input-title"><?= lang('Service.date') ?></p>
                    <input class="editable" name="start_date"
                           value="<?= HtmlHelper::toDateString($data['start_date'] ?? null) ?>"
                           readonly>
                </div>
                <div class="input-wrap column calendar-time">
                    <p class="input-title"><?= lang('Service.hour') ?></p>
                    <input class="editable" name="end_minute" readonly value="<?= $data['start_hour'] ?? '00' ?>"/>
                </div>
                <div class="input-wrap column calendar-time">
                    <p class="input-title"><?= lang('Service.minute') ?></p>
                    <input class="editable" name="end_minute" readonly value="<?= $data['start_minute'] ?? '00' ?>"/>
                </div>
                <p class="input-description"><?= lang('Service.end_date') ?></p>
                <div class="input-wrap column calendar">
                    <p class="input-title"><?= lang('Service.date') ?></p>
                    <input class="editable" name="end_date"
                           value="<?= HtmlHelper::toDateString($data['end_date'] ?? null) ?>"
                           readonly>
                </div>
                <div class="input-wrap column calendar-time">
                    <p class="input-title"><?= lang('Service.hour') ?></p>
                    <input class="editable" name="end_hour" readonly value="<?= $data['end_hour'] ?? '00' ?>"/>
                </div>
                <div class="input-wrap column calendar-time">
                    <p class="input-title"><?= lang('Service.minute') ?></p>
                    <input class="editable" name="end_minute" readonly value="<?= $data['end_minute'] ?? '00' ?>"/>
                </div>
                <div class="line"></div>
                <div class="input-wrap inline status">
                    <p class="input-title"><?= lang('Service.status') ?></p>
                    <select class="editable" name="status" disabled>`
                        <option
                            value="open" <?= isset($data['status']) && $data['status'] == 'open' ? 'selected' : '' ?>><?= lang('Service.opened') ?></option>
                        <option
                            value="close" <?= isset($data['status']) && $data['status'] == 'close' ? 'selected' : '' ?>><?= lang('Service.closed') ?></option>
                    </select>
                </div>
                <div class="line"></div>
                <div class="input-wrap hash">
                    <p class="input-title"><?= lang('Service.access_hash') ?></p>
                    <a class="button out-line"
                       href="javascript:regenerateHash(<?= $data['id'] ?>)">
                        <img src="/asset/images/icon/plus.png"/>
                        <span><?= lang('Service.regenerate_hash') ?></span>
                    </a>
                    <input type="text" name="title_en" class="editable under-line" value="<?= $data['access_hash'] ?>"
                           readonly/>
                </div>
            </div>
            <?php if (isset($data['artists']) || isset($data['rewards'])) { ?>
                <div class="form-wrap extra">
                    <?php if (isset($data['artists'])) { ?>
                        <div class="line black"></div>
                        <div class="input-wrap artist">
                            <p class="input-title"><?= lang('Service.artist') ?></p>
                            <div class="row-uploader artist"></div>
                            <?php //= HtmlHelper::getArtistRow('artist', $data['artists'], $lang, 'view') ?>
                        </div>
                    <?php }
                    if (isset($data['rewards'])) { ?>
                        <div class="line black"></div>
                        <div class="input-wrap reward">
                            <p class="input-title"><?= lang('Service.price_reward') ?></p>
                            <div class="row-uploader reward">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php }
            if ($is_login && $is_admin) { ?>
                <div class="line black"></div>
                <div class="control-button-wrap">
                    <a href="<?= '/admin/project/' . $data['access_hash'] . '/reward' ?>"
                       class="button under-line edit">
                        <img src="/asset/images/icon/detail.png"/>
                        <span><?= lang('Service.manage_reward') ?></span>
                    </a>
                    <a href="<?= $is_admin_page ? '/admin/project/' . $data['id'] . '/edit' : '/project/' . $data['id'] . '/edit' ?>"
                       class="button under-line edit">
                        <img src="/asset/images/icon/edit.png"/>
                        <span><?= lang('Service.edit') ?></span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        loadArtist(<?=$data['id']?>, false)
        loadReward(<?=$data['id']?>, false)
    });

    function regenerateHash(id) {
        apiRequest({
            type: 'POST',
            url: `/api/project/regenerate-hash/${id}`,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) {
                    openPopupErrors('popup-error', response, status, request);
                    return;
                }
                window.location.reload();
            },
            error: function (response, status, error) {
                openPopupErrors('popup-error', response, status, error);
            },
        });
    }
</script>
