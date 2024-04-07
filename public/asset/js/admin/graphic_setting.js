$(document).ready(function () {
    let $slick = $('.slider-wrap .slick');
    $slick.setOnResolutionChanged((event) => {
        $slick.setCustomSlick(event.detail.isMobile, {
            infinite: false,
            autoplay: false,
            draggable: false,
        });
    })
});

function getTypeFromTarget(target) {
    let type;
    switch (target) {
        case 'favicon':
        case 'open_graph':
        case 'logo':
        case 'footer_logo':
            type = 'image';
            break;
        case 'main_video':
            type = 'video';
            break;
        case 'main':
            type = 'image';
            break;
    }
    return type;
}

function editSettingFile(target) {
    let type = getTypeFromTarget(target);
    if (isEmpty(target) || isEmpty(type)) return;
    let $parent = $(`.content-box.${target}`)
    let $container = $parent.find(`.content-wrap`);
    $container.empty()

    $parent.addClass('editing')

    refreshSettingFile();
}

function cancelSettingFileEdit(target) {
    let type = getTypeFromTarget(target);
    if (isEmpty(target) || isEmpty(type)) return;
    dropEditingFiles(target, function () {
        let $parent = $(`.content-box.${target}`)
        $parent.removeClass('editing')
        refreshSettingFile()
    });
}

function confirmSettingFileEdit(target) {
    if (isEmpty(target)) return;
    confirmEditFiles(target, function () {
        apiRequest({
            type: 'GET',
            url: `/api/graphic-setting/get/all`,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) return;
                let data = response.data;
                let $parent = $(`.content-box.${target}`)
                $parent.removeClass('editing')

                files.clear();
                for (let target in data) {
                    let array = data[target]
                    for (let i in array) {
                        files.push(target, array[i]['id']);
                    }
                }
                refreshSettingFile()
            },
            error: function (response, status, error) {
            },
        });
        // location.reload();
    });
}

function generateOnSettingFileUploaded(target) {
    return (target, file_id, mime_type) => {
        files.push(target, file_id);
        let $parent = $(`.content-box.${target}`)
        let $container = $parent.find(`.content-wrap-inner`);
        $container.empty();

        let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`
        let option = type == 'video' ? `background-size: cover;` : `background-size: contain;`
        $container.append(`
        <div class="upload-item" style="background: url('${file_url}') no-repeat center;font-size: 0;${option}">
            <div class="upload-item-hover">
                <a href="javascript:deleteSettingFile('${target}', '${file_id}')"
                   class="button delete-image black">
                    <img src="/asset/images/icon/cancel_white.png"/>
                </a>
            </div>
        </div>`);
    }
}

function deleteSettingFile(target, id) {
    let type = getTypeFromTarget(target);
    if (isEmpty(target) || isEmpty(type)) return;

    let index = files.get(target).indexOf(id);
    if (index < 0) return;
    files.splice(target, index);

    let $parent = $(`.content-box.${target}`)
    let $container = $parent.find(`.content-wrap-inner`);
    $container.empty();

    let icon_url = type == 'video' ? '/asset/images/icon/plus_circle_big.png' : '/asset/images/icon/plus_circle_big_gray.png';
    let accept = type == 'video' ? 'video/*' : 'image/png';
    $container.append(`
    <div class="upload-item-add"
         style="background: url('${icon_url}') no-repeat center; font-size: 0;">
        <label for="${target}-file" class="button"></label>
        <input type="file" name="file" multiple id="${target}-file"
               onchange="onFileUpload(this, '${target}', generateOnSettingFileUploaded('${target}'));"
               accept="${accept}"/>
    </div>`);
}

function refreshSettingFile() {
    let refresh = (target) => {
        let type = getTypeFromTarget(target);
        if (isEmpty(target) || isEmpty(type)) return;
        let $parent = $(`.content-box.${target}`)
        if ($parent.length == 0) return;
        let $container = $parent.find(`.content-wrap`);
        $container.empty()

        if ($parent.hasClass('editing')) {
            if (target == 'main') {
                let html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
                html += `
                <div class="slick uploader ${target}">`;

                for (let i in files.get(target)) {
                    let file_id = files.get(target)[i];
                    html += `
                    <div class="slick-item draggable-item upload-item" draggable="true"
                         style="background: url('/file/${file_id}') no-repeat center; background-size: cover; font-size: 0;">
                        Slider #${file_id}
                        <input hidden type="text" name="id" value="${file_id}">
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedSlickFile('main', '${file_id}')"
                               class="button delete-image black">
                                <img src="/asset/images/icon/cancel_white.png"/>
                            </a>
                        </div>
                    </div>`;
                }
                html += `
                        <div class="slick-item upload-item-add"
                             style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                            <label for="image-file" class="button"></label>
                            <input type="file" name="file" multiple id="image-file"
                                   onchange="onFileUpload(this, 'main');"
                                   accept="image/*"/>
                        </div>
                    </div>
                    </div>
                </div>`;
                $container.append(html);

                let $slick = $('.slider-wrap .slick');
                $slick.setCustomSlick(isMobile(), {
                    infinite: false,
                    autoplay: false,
                    draggable: false,
                });
                $container.find('.slick').initDraggable({
                    onDragFinished: generateOnDragFinished('main')
                });
            } else {
                let html = `<div class="content-wrap-inner lines-horizontal">`;
                if (files.get(target).length == 0) {
                    let icon_url = type == 'video' ? '/asset/images/icon/plus_circle_big.png' : '/asset/images/icon/plus_circle_big_gray.png';
                    let accept = type == 'video' ? 'video/*' : 'image/png';
                    html += `
                    <div class="upload-item-add"
                         style="background: url('${icon_url}') no-repeat center; font-size: 0;">
                        <label for="${target}-file" class="button"></label>
                        <input type="file" name="file" multiple id="${target}-file"
                               onchange="onFileUpload(this, '${target}', generateOnSettingFileUploaded('${target}'));"
                               accept="${accept}"/>
                    </div>`;
                } else {
                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`
                        let option = target == 'open_graph' || target == 'main_video' ? `background-size: cover;` : `background-size: contain;`
                        html += `
                        <div class="upload-item" style="background: url('${file_url}') no-repeat center; font-size: 0;${option}">
                            <div class="upload-item-hover">
                                <a href="javascript:deleteSettingFile('${target}', '${file_id}')"
                                   class="button delete-image black">
                                    <img src="/asset/images/icon/cancel_white.png"/>
                                </a>
                            </div>
                        </div>`
                    }
                }
                html += `
                </div>`;
                $container.append(html);
            }
            let $wrapButtonControls = $parent.find(`.control-button-wrap`);
            $wrapButtonControls.empty();
            $wrapButtonControls.append(`
            <a href="javascript:cancelSettingFileEdit('${target}');"
               class="button under-line cancel">
                <img src="/asset/images/icon/cancel.png"/>
                <span>${lang('cancel')}</span>
            </a>
            <a href="javascript:confirmSettingFileEdit('${target}');"
               class="button under-line confirm">
                <img src="/asset/images/icon/check.png"/>
                <span>${lang('confirm')}</span>
            </a>`)
            if (type == 'main') {
                $wrapButtonControls.after(`
                <div class="info-text-wrap">
                    ${lang('message_info_drag')}
                </div>`)
            }
        } else {
            if (files.get(target).length == 0) {
                $container.append(`
                <div class="no-data-box">
                    <div class="no-data-wrap">
                        <img src="/asset/images/icon/err_empty_folder.png">
                        <span>No data available.</span>
                    </div>
                </div>`);
            } else {
                if (target == 'main') {
                    let html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
                    html += `
                    <div class="slick uploader ${target}">`;

                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        html += `
                        <div class="slick-item button"
                             style="background: url('/file/${file_id}') no-repeat center; background-size: cover; font-size: 0;"
                             onclick="openImagePopup(${file_id})">
                            Slider #${file_id}
                        </div>`;
                    }
                    html += `
                        </div>
                    </div>
                </div>`;
                    $container.append(html);

                    let $slick = $('.slider-wrap .slick');
                    $slick.setCustomSlick(isMobile(), {
                        infinite: false,
                        autoplay: false,
                        draggable: false,
                    });
                    $container.find('.slick').initDraggable({
                        onDragFinished: generateOnDragFinished('main')
                    });
                } else {
                    let html = `<div class="content-wrap-inner lines-horizontal">`
                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`
                        let option = target == 'open_graph' || target == 'main_video' ? `background-size: cover;` : `background-size: contain;`
                        html += `
                        <div class="upload-item"
                             style="background: url('${file_url}') no-repeat center; font-size: 0;${option}">
                        </div>`
                    }
                    html += `</div>`;

                    $container.append(html);
                }
            }
            let $wrapButtonControls = $parent.find(`.control-button-wrap`);
            $wrapButtonControls.empty();
            $wrapButtonControls.append(`
            <a href="javascript:editSettingFile('${target}');"
               class="button under-line edit">
                <img src="/asset/images/icon/edit.png"/>
                <span>${lang('edit')}</span>
            </a>`)
        }
    }
    let targets = ['favicon', 'open_graph', 'logo', 'footer_logo', 'main_video', 'main'];
    for (let i in targets) {
        refresh(targets[i])
    }
}
