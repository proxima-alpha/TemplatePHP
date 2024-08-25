<?php

use App\Helpers\HtmlHelper;
use Crisu83\ShortId\ShortId;

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

if ($type == 'create') {
    $data['title'] = '';
    $data['title_en'] = '';
    $data['title_jp'] = '';
    $data['content'] = '';
    $data['content_en'] = '';
    $data['content_jp'] = '';
}
$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    <?php
    if (isset($data['image_id'])) {?>
    uploadData.push('image', '<?=$data['image_id']?>');
    <?php }
    if (isset($data['background_id'])) {?>
    uploadData.push('background', '<?=$data['background_id']?>');
    <?php }
    if (isset($data['mobile_background_id'])) {?>
    uploadData.push('background_mobile', '<?=$data['mobile_background_id']?>');
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
                                    <?= HtmlHelper::getLangItem($item, 'name', $lang) ?>
                                </option>
                            <?php }
                        } ?>
                    </select>
                </div>
                <div class="tab-box">
                    <div class="tab-button-wrap">
                        <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
                        <a class="button en" onclick="clickTab(this,'en')">English</a>
                        <a class="button jp" onclick="clickTab(this,'jp')">日本語</a>
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
                            <div id="editor-ko" class="quill-editor"></div>
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
                            <div id="editor-en" class="quill-editor"></div>
                        </div>
                    </div>
                    <div class="tab-wrap jp">
                        <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.title') ?></p>
                            <input type="text" name="title_jp" class="editable under-line"
                                   value="<?= $data['title_jp'] ?>"/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.content') ?></p>
                            <div id="editor-jp" class="quill-editor"></div>
                        </div>
                    </div>
                </div>
                <div class="line"></div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('Service.project_image') ?> (268 X 318)</p>
                    <?= HtmlHelper::getSingleMediaUploader('image', $data['image_file'] ?? null) ?>
                </div>
                <div class="input-uploader-wrap">
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.project_background') ?> (1500 X 300)</p>
                        <?= HtmlHelper::getSingleMediaUploader('background', $data['background_file'] ?? null) ?>
                    </div>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.project_background') ?> (<?= lang('Service.mobile') ?>) (840 X 300)</p>
                        <?= HtmlHelper::getSingleMediaUploader('background_mobile', $data['background_mobile_file'] ?? null) ?>
                    </div>
                </div>
                <div class="line"></div>
                <p class="input-description"><?= lang('Service.start_date') ?></p>
                <div class="input-wrap column calendar">
                    <p class="input-title"><?= lang('Service.date') ?></p>
                    <a class="button" href="javascript:openCalendarPopup('start_date')">
                        <input class="editable" name="start_date"
                               value="<?= HtmlHelper::toDateString($data['start_date'] ?? null) ?>"
                               readonly>
                    </a>
                </div>
                <div class="input-wrap column calendar-time">
                    <p class="input-title"><?= lang('Service.hour') ?></p>
                    <select class="editable" name="start_hour"
                            value="<?= $data['start_hour'] ?? '' ?>">
                        <?= HtmlHelper::getHourOptions($data['start_hour'] ?? null) ?>
                    </select>
                </div>
                <div class="input-wrap column calendar-time">
                    <p class="input-title"><?= lang('Service.minute') ?></p>
                    <select class="editable" name="start_minute"
                            value="<?= $data['start_minute'] ?? '' ?>">
                        <?= HtmlHelper::getMinuteOptions($data['start_minute'] ?? null) ?>
                    </select>
                </div>
                <p class="input-description"><?= lang('Service.end_date') ?></p>
                <div class="input-wrap column calendar">
                    <p class="input-title"><?= lang('Service.date') ?></p>
                    <a class="button" href="javascript:openCalendarPopup('end_date')">
                        <input class="editable" name="end_date"
                               value="<?= HtmlHelper::toDateString($data['end_date'] ?? null) ?>"
                               readonly>
                    </a>
                </div>
                <div class="input-wrap column calendar-time">
                    <p class="input-title"><?= lang('Service.hour') ?></p>
                    <select class="editable" name="end_hour"
                            value="<?= $data['end_hour'] ?? '' ?>">
                        <?= HtmlHelper::getHourOptions($data['end_hour'] ?? null) ?>
                    </select>
                </div>
                <div class="input-wrap column calendar-time">
                    <p class="input-title"><?= lang('Service.minute') ?></p>
                    <select class="editable" name="end_minute"
                            value="<?= $data['end_minute'] ?? '' ?>">
                        <?= HtmlHelper::getMinuteOptions($data['end_minute'] ?? null) ?>
                    </select>
                </div>
                <div class="line"></div>
                <div class="input-wrap inline status">
                    <p class="input-title"><?= lang('Service.status') ?></p>
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
        let quillKo, quillEn, quillJp
        $(document).ready(function () {
            <?php if($type != 'create') {?>
            loadArtist(<?=$data['id']?>)
            loadReward(<?=$data['id']?>)
            <?php } ?>
            const quillOption = {
                modules: {
                    toolbar: [
                        [{header: [1, 2, false]}],
                        ['bold', 'italic', 'underline'],
                        ['image'],
                        [{'list': 'ordered'}, {'list': 'bullet'}],
                        [{'indent': '-1'}, {'indent': '+1'}],
                        [{'color': []}, {'background': []},],
                        [{'align': []}],
                    ],
                },
                theme: 'snow', // or 'bubble'

            };
            quillKo = new Quill('#editor-ko', quillOption);
            quillKo.setContents(quillKo.clipboard.convert({html: `<?= $data['content']?>`}));
            quillEn = new Quill('#editor-en', quillOption);
            quillEn.setContents(quillKo.clipboard.convert({html: `<?= $data['content_en']?>`}));
            quillJp = new Quill('#editor-jp', quillOption);
            quillJp.setContents(quillKo.clipboard.convert({html: `<?= $data['content_jp']?>`}));
        });
    </script>
