<?php

use Crisu83\ShortId\ShortId;

if ($type == 'create') {
    $data['name'] = '';
    $data['job'] = '';
    $data['introduction'] = '';
}
$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    <?php if (isset($data['previews'])) {
    foreach ($data['previews'] as $index => $item) { ?>
    files.push('artist_preview', '<?=$item['id']?>');
    <?php }
    }
    if (isset($data['profile_id'])) {?>
    files.push('artist_profile', '<?=$data['profile_id']?>');
    <?php }?>
</script>
<div class="container-inner">
    <div class="container-wrap">
        <div class="artist-wrap">
            <div class="form-wrap line-after">
                <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
                <div class="input-wrap inline">
                    <p class="input-title"><?= lang('아티스트 타입') ?></p>
                    <select class="editable" name="code_artist_id" value="<?= $code_artist_id ?? '' ?>">`
                        <?php
                        if (isset($code_artists)) {
                            foreach ($code_artists as $item) { ?>
                                <option value="<?= $item['id'] ?>"
                                    <?= isset($code_artist_id) && $item['id'] == $code_artist_id ? 'selected' : '' ?>><?= $item['name'] ?></option>
                            <?php }
                        } ?>
                    </select>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('Service.name') ?></p>
                    <input type="text" name="name" class="editable under-line" value="<?= $data['name'] ?>"/>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('직업') ?></p>
                    <input type="text" name="job" class="editable under-line" value="<?= $data['job'] ?>"/>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('소개') ?></p>
                    <textarea class="editable" name="introduction" onkeydown="resizeInputPopupTextarea(this)"
                              onkeyup="resizeInputPopupTextarea(this)"><?= $data['introduction'] ?></textarea>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('프로필 이미지') ?></p>
                    <?= \App\Helpers\HtmlHelper::getImageUploader('artist_profile', $data['profile_id'] ?? null) ?>
                </div>
            </div>
            <div class="slider-box">
                <p class="title"><?= lang('샘플 영상') ?></p>
                <?= \App\Helpers\HtmlHelper::getSlickUploader('artist_preview', $data['previews'] ?? null) ?>
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
        data['previews'] = files.get('artist_preview');
        data['profile_id'] = files.get('artist_profile');

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
        data['previews'] = files.get('artist_preview');
        data['profile_id'] = files.get('artist_profile');

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
