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

function getAcceptFromTarget(target) {
    let accept;
    switch (target) {
        case 'main':
            accept = 'image/png,image/jpg';
            break;
        case 'relation':
            accept = 'video/*';
            break;
    }
    return accept;
}

function editSettingFile(target) {
    let accept = getAcceptFromTarget(target);
    if (isEmpty(target) || isEmpty(accept)) return;
    let $parent = $(`.content-box.${target}`)
    let $container = $parent.find(`.content-wrap`);
    $container.empty()

    $parent.addClass('editing')

    refreshSettingFile();
}

function cancelSettingFileEdit(target) {
    let accept = getAcceptFromTarget(target);
    if (isEmpty(target) || isEmpty(accept)) return;
    dropEditingFiles(target, function () {
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
                        files.push(target, array[i]['id'], array[i]['type']);
                    }
                }
                refreshSettingFile()
            },
            error: function (response, status, error) {
            },
        });
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
                        files.push(target, array[i]['id'], array[i]['type']);
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

function generateOnSettingFileUploaded() {
    return (target, file_id, type) => {
        files.push(target, file_id, type);
        let $parent = $(`.content-box.${target}`)
        let $container = $parent.find(`.content-wrap-inner`);
        $container.empty();

        let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`;
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
    let accept = getAcceptFromTarget(target);
    if (isEmpty(target) || isEmpty(accept)) return;

    let index = files.get(target).indexOf(id);
    if (index < 0) return;
    files.splice(target, index);

    let $parent = $(`.content-box.${target}`)
    let $container = $parent.find(`.content-wrap-inner`);
    $container.empty();

    $container.append(`
    <div class="upload-item-add"
         style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
        <label for="${target}-file" class="button"></label>
        <input type="file" name="file" multiple id="${target}-file"
               onchange="onFileUpload(this, '${target}', generateOnSettingFileUploaded());"
               accept="${accept}"/>
    </div>`);
}

function refreshSettingFile() {
    let refresh = (target) => {
        let accept = getAcceptFromTarget(target);
        if (isEmpty(target) || isEmpty(accept)) return;
        let $parent = $(`.content-box.${target}`)
        if ($parent.length == 0) return;
        let $container = $parent.find(`.content-wrap`);
        $container.empty()

        if ($parent.hasClass('editing')) {
            if (target == 'main' || target == 'relation') {
                let html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
                html += `
                <div class="slick uploader ${target}">`;

                for (let i in files.get(target)) {
                    let file_id = files.get(target)[i];
                    let type = files.getType(target)[i];
                    let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`;
                    html += `
                    <div class="slick-item draggable-item upload-item" draggable="true"
                         style="background: url('${file_url}') no-repeat center; background-size: cover; font-size: 0;">
                        Slider #${file_id}
                        <input hidden type="text" name="id" value="${file_id}">
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedSlickFile('${target}', '${file_id}')"
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
                                   onchange="onFileUpload(this, '${target}');"
                                   accept="${accept}"/>
                        </div>
                    </div>
                    </div>
                </div>`;
                $container.append(html);

                let $slick = $container.find('.slick');
                $slick.setCustomSlick(isMobile(), {
                    infinite: false,
                    autoplay: false,
                    draggable: false,
                });
                $container.find('.slick').initDraggable({
                    onDragFinished: generateOnDragFinished(target)
                });
            } else {
                let html = `<div class="content-wrap-inner lines-horizontal">`;
                if (files.get(target).length == 0) {
                    html += `
                    <div class="upload-item-add"
                         style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                        <label for="${target}-file" class="button"></label>
                        <input type="file" name="file" multiple id="${target}-file"
                               onchange="onFileUpload(this, '${target}', generateOnSettingFileUploaded());"
                               accept="${accept}"/>
                    </div>`;
                } else {
                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        let type = files.getType(target)[i];
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
            if (target == 'main' || target == 'relationship') {
                $wrapButtonControls.append(`
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
                if (target == 'main' || target == 'relation') {
                    let html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
                    html += `
                    <div class="slick uploader ${target}">`;

                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        let type = files.getType(target)[i];
                        let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`;
                        html += `
                        <div class="slick-item button"
                             style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;"
                             onclick="openImagePopup(${file_id})">
                            Slider #${file_id}
                        </div>`;
                    }
                    html += `
                        </div>
                    </div>
                </div>`;
                    $container.append(html);

                    let $slick = $container.find('.slick');
                    $slick.setCustomSlick(isMobile(), {
                        infinite: false,
                        autoplay: false,
                        draggable: false,
                    });
                    $container.find('.slick').initDraggable({
                        onDragFinished: generateOnDragFinished(target)
                    });
                } else {
                    let html = `<div class="content-wrap-inner lines-horizontal">`
                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        let type = files.getType(target)[i];
                        let file_url = type == 'video' ? `/file/${file_id}/thumbnail` : `/file/${file_id}`
                        html += `
                        <div class="upload-item"
                             style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;">
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
    let targets = ['favicon', 'open_graph', 'logo', 'footer_logo', 'main_video', 'main', 'relation'];
    for (let i in targets) {
        refresh(targets[i])
    }
}
