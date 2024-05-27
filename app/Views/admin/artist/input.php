<?php

use Crisu83\ShortId\ShortId;

if ($type == 'create') {
    $data['name'] = '';
    $data['name_en'] = '';
    $data['job'] = '';
    $data['job_en'] = '';
    $data['introduction'] = '';
    $data['introduction_en'] = '';
}
$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    <?php if (isset($data['previews'])) {
    foreach ($data['previews'] as $index => $item) { ?>
    uploadData.push('artist_preview', '<?=$item['id']?>');
    <?php }
    }
    if (isset($data['profile_id'])) {?>
    uploadData.push('artist_profile', '<?=$data['profile_id']?>');
    <?php }?>
</script>
<div class="container-inner">
    <div class="container-wrap">
        <div class="artist-wrap">
            <div class="form-wrap line-after">
                <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
                <div class="tab-box">
                    <div class="tab-button-wrap">
                        <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
                        <a class="button en" onclick="clickTab(this,'en')">English</a>
                    </div>
                    <div class="tab-wrap ko active">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.name') ?></p>
                            <input type="text" name="name" class="editable under-line" value="<?= $data['name'] ?>"/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.job') ?></p>
                            <input type="text" name="job" class="editable under-line" value="<?= $data['job'] ?>"/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.introduction') ?></p>
                            <textarea class="editable" name="introduction" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)"><?= $data['introduction'] ?></textarea>
                        </div>
                    </div>
                    <div class="tab-wrap en">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.name') ?></p>
                            <input type="text" name="name_en" class="editable under-line" value="<?= $data['name_en'] ?>"/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.job') ?></p>
                            <input type="text" name="job_en" class="editable under-line" value="<?= $data['job_en'] ?>"/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.introduction') ?></p>
                            <textarea class="editable" name="introduction_en" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)"><?= $data['introduction_en'] ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('Service.profile_image') ?></p>
                    <?= \App\Helpers\HtmlHelper::getSingleMediaUploader('artist_profile', $data['profile_id'] ?? null) ?>
                </div>
            </div>
            <div class="slider-box">
                <p class="title"><?= lang('Service.sample_video') ?></p>
                <?= \App\Helpers\HtmlHelper::getMultiMediaUploader('artist_preview', $data['previews'] ?? null, 'input', 'video/mp4') ?>
                <div class="info-text-wrap">
                    <?= lang('Service.message_info_drag') ?>
                </div>
            </div>
            <div class="button-wrap">
                <a href="<?= $type == 'create' ? 'javascript:confirmCreateArtist()' : 'javascript:confirmEditArtist(' . $data['id'] . ')' ?>"
                   class="button confirm black"><?= lang('Service.confirm') ?></a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function confirmEditArtist(id) {
        let data = parseInputToData($(`.artist-wrap .form-wrap .editable`))
        data['previews'] = uploadData.get('artist_preview');
        data['profile_id'] = uploadData.get('artist_profile');

        apiRequest({
            type: 'POST',
            url: `/api/artist/update/${id}`,
            data: data,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) {
                    openPopupErrors('popup-error', response, status, request);
                    return;
                }
                history.back();
            },
            error: function (response, status, error) {
                openPopupErrors('popup-error', response, status, error);
            },
        });
    }

    function confirmCreateArtist() {
        let data = parseInputToData($(`.artist-wrap .form-wrap .editable`))
        data['previews'] = uploadData.get('artist_preview');
        data['profile_id'] = uploadData.get('artist_profile');

        apiRequest({
            type: 'POST',
            url: `/api/artist/create`,
            data: data,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) {
                    openPopupErrors('popup-error', response, status, request);
                    return;
                }
                history.back();
            },
            error: function (response, status, error) {
                openPopupErrors('popup-error', response, status, error);
            },
        });
    }
</script>
