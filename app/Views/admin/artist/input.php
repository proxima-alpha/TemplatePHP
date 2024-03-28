<?php

use Crisu83\ShortId\ShortId;

if ($type == 'create') {
    $data['name'] = '';
    $data['introduction'] = '';
}
$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    <?php if (isset($data['files'])) {
    foreach ($data['files'] as $index => $item) { ?>
    files.push('preview', '<?=$item['id']?>');
    <?php }
    }
    if (isset($data['profile_id'])) {?>
    files.push('profile', '<?=$data['profile_id']?>');
    <?php }?>
</script>
<div class="container-inner topic-input-container">
    <div class="container-wrap">
        <div class="artist-wrap">
            <div class="form-wrap line-after">
                <div class="input-wrap">
                    <p class="input-title"><?= lang('Service.name') ?></p>
                    <input type="name" name="name" class="under-line" value="<?= $data['name'] ?>"/>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('소개') ?></p>
                    <textarea name="description" onkeydown="resizeInputPopupTextarea(this)"
                              onkeyup="resizeInputPopupTextarea(this)"><?= $data['introduction'] ?></textarea>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('프로필 이미지') ?></p>
                    <div class="uploader profile">
                        <?php if (isset($data['profile_id'])) { ?>
                            <div class="upload-item"
                                 style="background: url('/file/<?= $data['profile_id'] ?>') no-repeat center; background-size: cover; font-size: 0;">
                                Profile #<?= $data['profile_id'] ?>
                                <input hidden type="text" name="id" value="<?= $data['profile_id'] ?>">
                                <div class="upload-item-hover">
                                    <a href="javascript:deleteImageFile('<?= $data['profile_id'] ?>')"
                                       class="button delete-image black">
                                        <img src="/asset/images/icon/cancel_white.png"/>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="upload-item-add"
                             style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                            <label for="artist_profile-file" class="button"></label>
                            <input type="file" name="file" multiple id="artist_profile-file"
                                   onchange="onFileUpload(this, 'artist_profile', 'artist_profile', 'image',  generateOnFileUploaded('artist_profile'));"
                                   accept="video/*;image/png;image/jpg"/>
                        </div>
                    </div>
                </div>
                <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
            </div>
            <div class="slider-box">
                <p class="title"><?= lang('샘플 영상') ?></p>
                <div class="slider-wrap">
                    <div class="slick uploader">
                        <?php if (isset($data['files'])) {
                            foreach ($data['files'] as $index => $file) { ?>
                                <div class="slick-item draggable-item upload-item" draggable="true"
                                     style="background: url('/file/<?= $file['id'] ?>') no-repeat center; background-size: cover; font-size: 0;">
                                    Slider #<?= $file['id'] ?>
                                    <input hidden type="text" name="id" value="<?= $file['id'] ?>">
                                    <div class="upload-item-hover">
                                        <a href="javascript:deleteImageFile('<?= $file['id'] ?>', 'artist_preview')"
                                           class="button delete-image black">
                                            <img src="/asset/images/icon/cancel_white.png"/>
                                        </a>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                        <div class="slick-item upload-item-add"
                             style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                            <label for="file" class="button"></label>
                            <input type="file" name="file" multiple id="file"
                                   onchange="onFileUpload(this, 'artist_preview', 'artist_preview', 'video');"
                                   accept="image/*"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="info-text-wrap">
                <?= lang('Service.message_info_drag') ?>
            </div>
            <div class="button-wrap">
                <a href="<?= $type == 'create' ? 'javascript:confirmCreateArtist()' : 'javascript:confirmCreateArtist(' . $data['id'] . ')' ?>"
                   class="button confirm black"><?= lang('Service.confirm') ?></a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function confirmCreateArtist(id) {
        let data = parseInputToData($(`.topic-wrap .form-wrap .editable`))
        data['files'] = files.get('artist_preview');
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
        let data = parseInputToData($(`.topic-wrap .form-wrap .editable`))
        data['files'] = files.get('artist_preview');
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
