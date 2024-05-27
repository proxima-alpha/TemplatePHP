<?php

use Crisu83\ShortId\ShortId;

\App\Helpers\HtmlHelper::setTranslations([
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

if ($type == 'create') {
    $data['title'] = '';
    $data['title_en'] = '';
    $data['content'] = '';
    $data['content_en'] = '';
}
$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    <?php if (isset($data['artists'])) {
    foreach ($data['artists'] as $index => $item) { ?>
    uploadData.push('artist', '<?=$item['id']?>');
    <?php }
    }
    if (isset($data['project_image_id'])) {?>
    uploadData.push('project', '<?=$data['project_image_id']?>');
    <?php }?>
</script>
<div class="container-inner">
    <div class="container-wrap">
        <div class="project-wrap">
            <div class="form-wrap project">
                <div class="input-wrap inline">
                    <p class="input-title"><?= lang('Service.category') ?></p>
                    <select class="editable" name="code_project_id" value="<?= $code_project_id ?? '' ?>">
                        <?php
                        if (isset($code_project)) {
                            foreach ($code_project as $item) { ?>
                                <option value="<?= $item['id'] ?>"
                                    <?= isset($code_project_id) && $item['id'] == $code_project_id ? 'selected' : '' ?>>
                                    <?= $lang == 'ko' ? $item['name'] : $item['name_en'] ?>
                                </option>
                            <?php }
                        } ?>
                    </select>
                </div>
                <div class="tab-box">
                    <div class="tab-button-wrap">
                        <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
                        <a class="button en" onclick="clickTab(this,'en')">English</a>
                    </div>
                    <div class="tab-wrap ko active">
                        <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.title') ?></p>
                            <input type="text" name="title" class="editable under-line"
                                   value="<?= $data['title'] ?>"/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.content') ?></p>
                            <textarea class="editable" name="content" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)"><?= $data['content'] ?></textarea>
                        </div>
                    </div>
                    <div class="tab-wrap en">
                        <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.title') ?></p>
                            <input type="text" name="title_en" class="editable under-line"
                                   value="<?= $data['title_en'] ?>"/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.content') ?></p>
                            <textarea class="editable" name="content_en" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)"><?= $data['content_en'] ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="line"></div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('Service.project_image') ?></p>
                    <?= \App\Helpers\HtmlHelper::getSingleMediaUploader('project', $data['project_image_id'] ?? null) ?>
                </div>
                <div class="line"></div>
                <div class="input-wrap column calendar">
                    <p class="input-title"><?= lang('Service.start_date') ?></p>
                    <a class="button" href="javascript:openCalendarPopup('start_date')">
                        <input class="editable" name="start_date"
                               value="<?= \App\Helpers\HtmlHelper::toDateString($data['start_date'] ?? null) ?>"
                               readonly>
                    </a>
                </div>
                <div class="input-wrap column calendar">
                    <p class="input-title"><?= lang('Service.end_date') ?></p>
                    <a class="button" href="javascript:openCalendarPopup('end_date')">
                        <input class="editable" name="end_date"
                               value="<?= \App\Helpers\HtmlHelper::toDateString($data['end_date'] ?? null) ?>"
                               readonly>
                    </a>
                </div>
                <div class="line"></div>
                <div class="input-wrap inline status">
                    <p class="input-title"><?= lang('service.status') ?></p>
                    <select class="editable" name="status" value="<?= $data['status'] ?? '' ?>">`
                        <option
                            value="open" <?= isset($data['status']) && $data['status'] == 'open' ? 'selected' : '' ?>><?= lang('Service.opened') ?></option>
                        <option
                            value="close" <?= isset($data['status']) && $data['status'] == 'close' ? 'selected' : '' ?>><?= lang('Service.closed') ?></option>
                    </select>
                </div>
            </div>
            <div class="form-wrap extra">
                <div class="line black"></div>
                <div class="input-wrap artist">
                    <p class="input-title"><?= lang('Service.artist') ?></p>
                    <div class="row-uploader artist"></div>
                    <div class="button-wrap">
                        <a class="button" href="javascript:searchArtist('artist')">
                        </a>
                    </div>
                </div>
                <div class="line black"></div>
                <div class="input-wrap reward">
                    <p class="input-title"><?= lang('가격 및 리워드') ?></p>
                    <div class="row-uploader reward">
                    </div>
                    <div class="button-wrap">
                        <a class="button" href="javascript:addRewardForm('reward')">
                        </a>
                    </div>
                </div>
            </div>
            <div class="button-wrap">
                <a href="<?= $type == 'create' ? 'javascript:confirmCreateProject()' : 'javascript:confirmEditProject(' . $data['id'] . ')' ?>"
                   class="button confirm black"><?= lang('Service.confirm') ?></a>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        <?php if($type != 'create') {?>
        $(document).ready(function () {
            loadArtist(<?=$data['id']?>)
            loadReward(<?=$data['id']?>)
        });
        <?php } ?>

    </script>
