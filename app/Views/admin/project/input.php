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
    <?php if (isset($data['artists'])) {
    foreach ($data['artists'] as $index => $item) { ?>
    files.push('artist', '<?=$item['id']?>');
    <?php }
    }
    if (isset($data['project_image_id'])) {?>
    files.push('project', '<?=$data['project_image_id']?>');
    <?php }?>
</script>
<div class="container-inner">
    <div class="container-wrap">
        <div class="project-wrap">
            <div class="form-wrap project">
                <input hidden type="text" name="identifier" class="editable" value="<?= $identifier ?>"/>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('제목') ?></p>
                    <input type="text" name="title" class="editable under-line" value="<?= $data['title'] ?>"/>
                </div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('내용') ?></p>
                    <textarea class="editable" name="content" onkeydown="resizeInputPopupTextarea(this)"
                              onkeyup="resizeInputPopupTextarea(this)"><?= $data['content'] ?></textarea>
                </div>
                <div class="line"></div>
                <div class="input-wrap">
                    <p class="input-title"><?= lang('타이틀 이미지') ?></p>
                    <?= \App\Helpers\HtmlHelper::getImageUploader('project', $data['project_image_id'] ?? null) ?>
                </div>
                <div class="line"></div>
                <div class="input-wrap calendar">
                    <p class="input-title"><?= lang('시작일') ?></p>
                    <a class="button" href="javascript:openCalendarPopup('start_date')">
                        <input class="editable" name="start_date" value="<?= \App\Helpers\HtmlHelper::toDateString($data['start_date'] ?? null) ?>" readonly>
                    </a>
                </div>
                <div class="input-wrap calendar">
                    <p class="input-title"><?= lang('마감일') ?></p>
                    <a class="button" href="javascript:openCalendarPopup('end_date')">
                        <input class="editable" name="end_date" value="<?= \App\Helpers\HtmlHelper::toDateString($data['end_date'] ?? null) ?>" readonly>
                    </a>
                </div>
            </div>
            <div class="form-wrap extra">
                <div class="line black"></div>
                <div class="input-wrap artist">
                    <p class="input-title"><?= lang('아티스트') ?></p>
                    <?= \App\Helpers\HtmlHelper::getRowUploaderArtist('artist', $data['artists'] ?? []) ?>
                    <div class="button-wrap">
                        <a class="button" href="javascript:searchArtist('artist')">
                        </a>
                    </div>
                </div>
                <div class="line black"></div>
                <div class="input-wrap reward">
                    <p class="input-title"><?= lang('가격 및 리워드') ?></p>
                    <?= \App\Helpers\HtmlHelper::getRowUploaderReward('reward', $data['rewards'] ?? []) ?>
                    <div class="button-wrap">
                        <a class="button" href="javascript:addRewardForm('reward')">
                        </a>
                    </div>
                </div>
                <div class="line black"></div>
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

    function removeRowDraggableItem(target, index, id) {
        if (id) {
            let index = files.get(target).indexOf(id);
            if (index >= 0) files.splice(target, index);
        }
        $(`.row-uploader.${target} .index-${index}`).remove();
    }

    function confirmArtistSearch(className, target) {
        let data = parseInputToData($(`.${className} input, .${className} textarea`))
        const id = data['artist']
        if (id) {
            if (files.get(target).indexOf(id) >= 0) {
                openPopupMessage(lang('이미 선택된 아티스트입니다'))
                return;
            }
            files.push(target, id);
            apiRequest({
                type: 'GET',
                url: `/api/artist/get/${id}`,
                data: data,
                dataType: 'json',
                success: function (response, status, request) {
                    if (!response.success) {
                        openPopupErrors('popup-error', response, status, request);
                        return;
                    }

                    const data = response.data
                    let file_url = `/file/${data['profile_id']}`
                    let $container = $(`.row-uploader.${target}`);

                    const index = $container.find('.row-uploader-item').length

                    $container.append(
                        `<div class="draggable-item row-uploader-item index-${index}" draggable="true">
                            <input hidden type="text" name="id" value="${data['id']}">
                            <div class="profile" style=" background: url('${file_url}'); background-size: cover; font-size: 0;"></div>
                            <div class="info-wrap">
                                <p class="name">${data['name']}</p>
                                <p>${data['job']}</p>
                                <p>${data['introduction']}</p>
                            </div>
                            <div class="upload-item-hover">
                                <a href="javascript:removeRowDraggableItem('${target}', '${index}', '${data['id']}')"
                                   class="button delete-image black">
                                    <img src="/asset/images/icon/cancel_white.png"/>
                                </a>
                            </div>
                        </div>`);

                    $container.initDraggable({
                        onDragFinished: generateOnDragFinished(target),
                    });
                },
                error: function (response, status, error) {
                    openPopupErrors('popup-error', response, status, error);
                },
            });
            closePopup(className);
        } else {
            openPopupMessage(lang('아티스트를 선택해주세요'))
        }
    }

    function addRewardForm(target) {
        let $container = $(`.row-uploader.${target}`);
        const index = $container.find('.row-uploader-item').length
        $container.append(`
            <div class="draggable-item row-uploader-item index-${index}" draggable="true">
                <div class="input-wrap price">
                   <p class="input-title">${lang('가격')}</p>
                   <input type="number" name="price" class="editable under-line" value=""/>
                   <p class="description">KRW</p>
               </div>
               <div class="input-wrap">
                   <p class="input-title">${lang('리워드')}</p>
                   <textarea class="editable" name="content" onkeydown="resizeInputPopupTextarea(this)"
                             onkeyup="resizeInputPopupTextarea(this)"></textarea>
               </div>
               <div class="column">
                   <div class="input-wrap">
                       <p class="input-title">${lang('재고')}</p>
                       <input type="number" name="total_count" class="editable under-line" value="title"/>
                   </div>
                   <div class="input-wrap">
                       <p class="input-title">${lang('구매가능한 수량')}</p>
                       <input type="number" name="limited_count" class="editable under-line" value="title"/>
                   </div>
               </div>

                <a href="javascript:removeRowDraggableItem('${target}', '${index}')"
                   class="button delete-image">
                    <img src="/asset/images/icon/cancel.png"/>
                </a>
            </div>`);
    }

    function confirmEditProject(id) {
        let data = parseInputToData($(`.project-wrap .form-wrap .editable`))
        data['artists'] = files.get('artist');
        data['project_image_id'] = files.get('project');

        let rewards = [];
        let $rewards = $(`.project-wrap .form-wrap.extra .reward .row-uploader-item`);
        for (let i = 0; i < $rewards.length; ++i) {
            const $reward = $rewards.eq(i);
            const rewardData = parseInputToData($reward.find('.editable'))
            if (Object.keys(rewardData).length > 0) {
                rewards.push(rewardData);
            }
        }
        data['rewards'] = rewards;

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
        let data = parseInputToData($(`.project-wrap .form-wrap.project .editable`))
        data['artists'] = files.get('artist');
        data['project_image_id'] = files.get('project');

        let rewards = [];
        let $rewards = $(`.project-wrap .form-wrap.extra .reward .row-uploader-item`);
        for (let i = 0; i < $rewards.length; ++i) {
            const $reward = $rewards.eq(i);
            const rewardData = parseInputToData($reward.find('.editable'))
            if (Object.keys(rewardData).length > 0) {
                rewards.push(rewardData);
            }
        }
        data['rewards'] = rewards;

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
