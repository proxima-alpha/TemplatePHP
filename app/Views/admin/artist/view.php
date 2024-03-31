<div class="container-inner">
    <div class="container-wrap">
        <div class="artist-wrap">
            <div class="form-wrap line-after">
                <div class="input-wrap inline">
                    <p class="input-title"><?= lang('아티스트 타입') ?></p>
                    <select class="editable" name="code_artist_id" value="1" disabled>`
                        <option value="1"><?= $data['code_artist'] ?></option>
                    </select>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('Service.name') ?></p>
                    <input type="name" name="name" class="editable under-line" value="<?= $data['name'] ?>" readonly/>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('소개') ?></p>
                    <textarea name="introduction" onkeydown="resizeInputPopupTextarea(this)"
                              onkeyup="resizeInputPopupTextarea(this)" readonly><?= $data['introduction'] ?></textarea>
                </div>
                <?php if (isset($data['profile_id'])) { ?>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('프로필 이미지') ?></p>
                        <div class="uploader artist_profile">
                            <div class="upload-item button"
                                 style="background: url('/file/<?= $data['profile_id'] ?>') no-repeat center; background-size: cover; font-size: 0;"
                                 onclick="openImagePopup(<?= $data['profile_id'] ?>)">
                                Slider #<?= $data['profile_id'] ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if (isset($data['files']) && sizeof($data['files']) > 0) { ?>
                <div class="slider-box">
                    <p class="title"><?= lang('샘플 영상') ?></p>
                    <div class="slider-wrap">
                        <div class="slick">
                            <?php foreach ($data['files'] as $index => $file) { ?>
                                <div class="slick-item button"
                                     style="background: url('<?=$file['type'] == 'image' ? '/file/'.$file['id'] : '/file/'.$file['id'].'/thumbnail'?>') no-repeat center; background-size: cover; font-size: 0;"
                                     onclick="openImagePopup(<?= $file['id'] ?>, '<?=$file['type']?>', '<?=$file['mime_type']?>')">
                                    Slider #<?= $file['id'] ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php }
            if ($is_login && ($is_admin || $user_id == $data['user_id'])) { ?>
                <div class="control-button-wrap">
                    <a href="<?= $is_admin_page ? '/admin/artist/' . $data['id'] . '/edit' : '/artist/' . $data['id'] . '/edit' ?>"
                       class="button under-line edit">
                        <img src="/asset/images/icon/edit.png"/>
                        <span><?= lang('Service.edit') ?></span>
                    </a>
                    <a href="javascript:openPopupDelete('/api/artist/delete/<?= $data['id'] ?>')"
                       class="button under-line delete">
                        <img src="/asset/images/icon/delete.png"/>
                        <span><?= lang('Service.delete') ?></span>
                    </a>
                </div>
            <?php } ?>
        </div>
        <div class="line black"></div>
    </div>
</div>
</div>
