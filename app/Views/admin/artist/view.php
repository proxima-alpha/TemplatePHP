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
                <div class="tab-box">
                    <div class="tab-button-wrap">
                        <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
                        <a class="button en" onclick="clickTab(this,'en')">English</a>
                    </div>
                    <div class="tab-wrap ko active">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.name') ?></p>
                            <input type="text" name="name" class="editable under-line" value="<?= $data['name'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.job') ?></p>
                            <input type="text" name="job" class="editable under-line" value="<?= $data['job'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.introduction') ?></p>
                            <textarea name="introduction" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)"
                                      readonly><?= $data['introduction'] ?></textarea>
                        </div>
                    </div>
                    <div class="tab-wrap en">
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.name') ?></p>
                            <input type="text" name="name_en" class="editable under-line"
                                   value="<?= $data['name_en'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.job') ?></p>
                            <input type="text" name="job_en" class="editable under-line" value="<?= $data['job_en'] ?>"
                                   readonly/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title"><?= lang('Service.introduction') ?></p>
                            <textarea name="introduction_en" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)"
                                      readonly><?= $data['introduction_en'] ?></textarea>
                        </div>
                    </div>
                </div>
                <?php if (isset($data['profile_id'])) { ?>
                    <div class="input-wrap">
                        <p class="input-title"><?= lang('Service.profile_image') ?></p>
                        <?= \App\Helpers\HtmlHelper::getImageUploader('artist_profile', $data['profile_id'] ?? null, 'view') ?>
                    </div>
                <?php } ?>
            </div>
            <?php if (isset($data['previews']) && sizeof($data['previews']) > 0) { ?>
                <div class="slider-box">
                    <p class="title"><?= lang('Service.sample_video') ?></p>
                    <?= \App\Helpers\HtmlHelper::getSlickUploader('artist_preview', $data['previews'] ?? null) ?>
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
