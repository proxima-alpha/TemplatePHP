<?php

use Crisu83\ShortId\ShortId;

if ($type == 'create') {
    $data['title'] = '';
    $data['content'] = '';
}
$shortid = ShortId::create();
$identifier = $shortid->generate();
?>
<script type="text/javascript">
    default_identifier = '<?=$identifier?>';
    <?php if (isset($data['files'])) {
    foreach ($data['files'] as $index => $item) { ?>
    files.push('artist_preview', '<?=$item['id']?>');
    <?php }
    }
    if (isset($data['title_image_id'])) {?>
    files.push('project_title', '<?=$data['title_image_id']?>');
    <?php }?>
</script>
<div class="container-inner">
    <div class="container-wrap">
        <div class="artist-wrap">
            <div class="form-wrap line-after">
                <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('제목') ?></p>
                    <input type="name" name="name" class="editable under-line" value="<?= $data['title'] ?>"/>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('내용') ?></p>
                    <textarea class="editable" name="introduction" onkeydown="resizeInputPopupTextarea(this)"
                              onkeyup="resizeInputPopupTextarea(this)"><?= $data['content'] ?></textarea>
                </div>
                <div class="input-wrap calendar">
                    <p class="input-title"><?= lang('시작일') ?></p>
                    <a class="button" href="javascript:openCalendarPopup('start_date')">
                        <input name="start_date" readonly>
                    </a>
                </div>
                <div class="input-wrap calendar">
                    <p class="input-title"><?= lang('마감일') ?></p>
                    <a class="button" href="javascript:openCalendarPopup('end_date')">
                        <input name="end_date" readonly>
                    </a>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('아티스트 추가') ?></p>
                    <a class="button" href="javascript:searchArtist('artist_id')">
                        <input name="artist_id" readonly>
                    </a>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('타이틀 이미지') ?></p>
                    <?= \App\Helpers\HtmlHelper::getImageUploader('project_title', $data['title_image_id'] ?? null) ?>
                </div>
            </div>
            <div class="button-wrap">
                <a href="<?= $type == 'create' ? 'javascript:confirmCreateProject()' : 'javascript:confirmEditProject(' . $data['id'] . ')' ?>"
                   class="button confirm black"><?= lang('Service.confirm') ?></a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function confirmCalendarSelect(className, target) {
        let data = parseInputToData($(`.${className} input, .${className} textarea`))

        $(`.form-wrap input[name=${target}]`).val(data['date'])
        closePopup(className);
    }

    function confirmArtistSearch(className, target) {
        let data = parseInputToData($(`.${className} input, .${className} textarea`))

        if (data['artist_id']) {
            $(`.form-wrap input[name=${target}]`).val(data['artist_id'])
            closePopup(className);
        } else {
            openPopupMessage(lang('아티스트를 선택해주세요'))
        }
    }

    function confirmEditProject(id) {
        let data = parseInputToData($(`.artist-wrap .form-wrap .editable`))
        data['files'] = files.get('artist_preview');
        data['profile_id'] = files.get('artist_profile');

        apiRequest({
            type: 'POST',
            url: `/api/project/update/${id}`,
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

    function confirmCreateProject() {
        let data = parseInputToData($(`.artist-wrap .form-wrap .editable`))
        data['files'] = files.get('artist_preview');
        data['profile_id'] = files.get('artist_profile');

        console.log(data)

        apiRequest({
            type: 'POST',
            url: `/api/project/create`,
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
