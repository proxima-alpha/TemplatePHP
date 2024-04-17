$(document).ready(function () {
    let $slick = $('.slider-wrap .slick');
    $slick.setOnResolutionChanged((event) => {
        $slick.setCustomSlick(event.detail.isMobile, {
            infinite: false,
            autoplay: false,
            draggable: false,
        });
    })
    $slick.setVideoCoverStyle();
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

function editSetting(target) {
    if (isEmpty(target)) return;
    let $parent = $(`.content-box.${target}`)
    let $container = $parent.find(`.content-wrap`);
    $container.empty()

    $parent.addClass('editing')

    refreshSetting();
}

function refreshViews(target) {
    apiRequest({
        type: 'GET',
        url: `/api/graphic-setting/get/all`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) return;
            let data = response.data;
            let $parent = $(`.content-box.${target}`)
            $parent.removeClass('editing')

            files.clearItems();
            for (let target in data) {
                let array = data[target]
                for (let i in array) {
                    const item = array[i];
                    if (target == 'project') {
                        files.push(target, item['id'], {
                            project_image_id: item['project_image_id'],
                            title: item['title'],
                            start_date: item['start_date'],
                            end_date: item['end_date'],
                            content: item['content']
                        });
                    } else if (target != 'main' && target != 'relation') {
                        files.push(target, item['id'], {
                            profile_id: item['profile_id'],
                            name: item['name'],
                            job: item['job']
                        });
                    } else {
                        files.push(target, item['id'], item['type']);
                    }
                }
            }
            refreshSetting()
        },
        error: function (response, status, error) {
        },
    });
}

function cancelSettingFileEdit(target) {
    let accept = getAcceptFromTarget(target);
    if (isEmpty(target) || isEmpty(accept)) {
        refreshViews(target);
        return;
    }
    dropEditingFiles(target, () => refreshViews(target));
}

function confirmSettingFileEdit(target) {
    if (isEmpty(target)) return;
    switch (target) {
        case 'main' :
        case 'relation' :
            confirmEditFiles(target, () => refreshViews(target));
            break;
        case 'project' : {
            const identifier = files.getIdentifier(target);
            apiRequest({
                type: 'POST',
                url: `/api/project/post`,
                data: {
                    projects: files.get(target)
                },
                dataType: 'json',
                success: function (response, status, request) {
                    if (!response.success) {
                        openPopupErrors('popup-error', response, status, request);
                        return;
                    }
                    refreshViews(target);
                },
                error: function (response, status, error) {
                    openPopupErrors('popup-error', response, status, error);
                },
            });
        }
            break;
        default : {
            const identifier = files.getIdentifier(target);
            apiRequest({
                type: 'POST',
                url: `/api/artist/post/${target}`,
                data: {
                    artists: files.get(target)
                },
                dataType: 'json',
                success: function (response, status, request) {
                    if (!response.success) {
                        openPopupErrors('popup-error', response, status, request);
                        return;
                    }
                    refreshViews(target);
                },
                error: function (response, status, error) {
                    openPopupErrors('popup-error', response, status, error);
                },
            });
        }
    }
}

function generateOnSettingFileUploaded() {
    return (target, file_id, type) => {
        files.push(target, file_id, type);
        let $parent = $(`.content-box.${target}`)
        let $container = $parent.find(`.content-wrap-inner`);
        $container.empty();

        let file_url = `/file/${file_id}`;
        if (type == 'image') {
            $container.append(`
            <div class="upload-item" style="background: url('${file_url}') no-repeat center;font-size: 0; background-size: cover;">
                <div class="upload-item-hover">
                    <a href="javascript:deleteSettingFile('${target}', '${file_id}')"
                       class="button delete-image black">
                        <img src="/asset/images/icon/cancel_white.png"/>
                    </a>
                </div>
            </div>`);
        } else {
            $container.append(`
            <div class="upload-item">
                <video preload="metadata">
                    <source src="${file_url}">
                </video>
                <div class="upload-item-hover">
                    <a href="javascript:deleteSettingFile('${target}', '${file_id}')"
                       class="button delete-image black">
                        <img src="/asset/images/icon/cancel_white.png"/>
                    </a>
                </div>
            </div>`);
        }
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
        <input type="file" name="file" id="${target}-file"
               onchange="onFileUpload(this, '${target}', generateOnSettingFileUploaded());"
               accept="${accept}"/>
    </div>`);
}

function setEditing($parent, target) {
    let $container = $parent.find(`.content-wrap`);
    let accept = getAcceptFromTarget(target);
    let html;
    switch
        (target) {
        case 'main':
        case 'relation': {
            html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
            html += `
                <div class="slick uploader ${target}">`;

            for (let i in files.get(target)) {
                let file_id = files.get(target)[i];
                let type = files.getExtra(target)[i];
                const file_url = `/file/${file_id}`;
                if (type == 'image') {
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
                } else {
                    html += `
                    <div class="slick-item draggable-item upload-item" draggable="true">
                        <video preload="metadata">
                            <source src="${file_url}">
                        </video>
                        <input hidden type="text" name="id" value="${file_id}">
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedSlickFile('${target}', '${file_id}')"
                               class="button delete-image black">
                                <img src="/asset/images/icon/cancel_white.png"/>
                            </a>
                        </div>
                    </div>`;
                }
            }
            html += `
                        <div class="slick-item upload-item-add"
                             style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                            <label for="image-file" class="button"></label>
                            <input type="file" name="file" id="image-file"
                                   onchange="onFileUpload(this, '${target}');"
                                   accept="${accept}"/>
                        </div>
                    </div>
                    </div>
                </div>`;
            $container.append(html);

            let $slick = $container.find('.slick');
            $slick.setVideoCoverStyle();
            $slick.setCustomSlick(isMobile(), {
                infinite: false,
                autoplay: false,
                draggable: false,
            });
            $container.find('.slick').initDraggable({
                onDragFinished: generateOnDragFinished(target)
            });
        }
            break;
        case 'project': {
            html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
            html += `
                <div class="slick uploader ${target}">`;
            for (let i in files.get(target)) {
                let file_id = files.get(target)[i];
                let extra = files.getExtra(target)[i];
                html += `
                    <div class="slick-item draggable-item upload-item" draggable="true">
                            <div class="image-item" style="background: url('/file/${extra['project_image_id']}') no-repeat center; background-size: cover; font-size: 0;"></div>
                            <p class="item-title">${extra['title']}</p>
                            <p class="item-date">${toDateString(extra['start_date'])} ~ ${toDateString(extra['end_date'])}</p>
                            <p class="item-content">${extra['content']}</p>
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
                        <div class="slick-item upload-item-add button"
                             style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;"
                             onClick="searchProject('${target}')">
                        </div>
                    </div>
                    </div>
                </div>`;
            $container.append(html);

            let $slick = $container.find('.slick');
            $slick.setVideoCoverStyle();
            $slick.setCustomSlick(isMobile(), {
                infinite: false,
                autoplay: false,
                draggable: false,
            });
            $container.find('.slick').initDraggable({
                onDragFinished: generateOnDragFinished(target)
            });
        }
            break;
        default:
            if (artist_codes.indexOf(target) >= 0) {
                html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
                html += `
                <div class="slick uploader ${target}">`;
                for (let i in files.get(target)) {
                    let file_id = files.get(target)[i];
                    let extra = files.getExtra(target)[i];
                    html += `
                    <div class="slick-item draggable-item upload-item" draggable="true">
                            <div class="image-item" style="background: url('/file/${extra['profile_id']}') no-repeat center; background-size: cover; font-size: 0;"></div>
                            <p class="item-title">${extra['name']}</p>
                            <p class="item-content">${extra['job']}</p>
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
                        <div class="slick-item upload-item-add button"
                             style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;"
                             onClick="searchArtist('${target}', true)">
                        </div>
                    </div>
                    </div>
                </div>`;
                $container.append(html);

                let $slick = $container.find('.slick');
                $slick.setVideoCoverStyle();
                $slick.setCustomSlick(isMobile(), {
                    infinite: false,
                    autoplay: false,
                    draggable: false,
                });
                $container.find('.slick').initDraggable({
                    onDragFinished: generateOnDragFinished(target)
                });
            } else {
                html = `<div class="content-wrap-inner lines-horizontal">`;
                if (files.get(target).length == 0) {
                    html += `
                    <div class="upload-item-add"
                         style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                        <label for="${target}-file" class="button"></label>
                        <input type="file" name="file" id="${target}-file"
                               onchange="onFileUpload(this, '${target}', generateOnSettingFileUploaded());"
                               accept="${accept}"/>
                    </div>`;
                } else {
                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        let type = files.getExtra(target)[i];
                        let file_url = `/file/${file_id}`
                        if (type == 'image') {
                            html += `
                            <div class="upload-item" style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;">
                                <div class="upload-item-hover">
                                    <a href="javascript:deleteSettingFile('${target}', '${file_id}')"
                                       class="button delete-image black">
                                        <img src="/asset/images/icon/cancel_white.png"/>
                                    </a>
                                </div>
                            </div>`
                        } else {
                            html += `
                            <div class="upload-item">
                                <video preload="metadata">
                                    <source src="${file_url}">
                                </video>
                                <div class="upload-item-hover">
                                    <a href="javascript:deleteSettingFile('${target}', '${file_id}')"
                                       class="button delete-image black">
                                        <img src="/asset/images/icon/cancel_white.png"/>
                                    </a>
                                </div>
                            </div>`
                        }
                    }
                }
                html += `
                </div>`;
                $container.append(html);
                $container.setVideoCoverStyle();
            }
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
}

function setView($parent, target) {
    let $container = $parent.find(`.content-wrap`);
    let style = '';
    if (target == 'project') {
        style = ` style="height : 390px; line-height: 388px" `
    } else if (target != 'main' && target != 'relation') {
        style = ` style="height : 370px; line-height: 368px" `
    }
    if (files.get(target).length == 0) {
        $container.append(`
                <div class="no-data-box" ${style}>
                    <div class="no-data-wrap">
                        <img src="/asset/images/icon/err_empty_folder.png">
                        <span>No data available.</span>
                    </div>
                </div>`);
    } else {
        let html;
        switch (target) {
            case 'main' :
            case 'relation' : {
                html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
                html += `
                    <div class="slick uploader ${target}">`;

                for (let i in files.get(target)) {
                    let file_id = files.get(target)[i];
                    let type = files.getExtra(target)[i];
                    let file_url = `/file/${file_id}`;
                    if (type == 'image') {
                        html += `
                        <div class="slick-item button"
                             style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;"
                             onclick="openImagePopup(${file_id}, '${type}')">
                            Slider #${file_id}
                        </div>`;
                    } else {
                        html += `
                        <div class="slick-item button"
                             onclick="openImagePopup(${file_id}, '${type}')">
                            <video preload="metadata">
                                <source src="${file_url}">
                            </video>
                        </div>`;
                    }
                }
                html += `
                        </div>
                    </div>
                </div>`;
                $container.append(html);

                let $slick = $container.find('.slick');
                $slick.setVideoCoverStyle();
                $slick.setCustomSlick(isMobile(), {
                    infinite: false,
                    autoplay: false,
                    draggable: false,
                });
                $container.find('.slick').initDraggable({
                    onDragFinished: generateOnDragFinished(target)
                });
            }
                break;
            case 'project': {
                html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
                html += `
                    <div class="slick uploader ${target}">`;
                for (let i in files.get(target)) {
                    let file_id = files.get(target)[i];
                    let extra = files.getExtra(target)[i];
                    html += `
                        <div class="slick-item">
                            <div class="image-item" style="background: url('/file/${extra['project_image_id']}') no-repeat center; background-size: cover; font-size: 0;"></div>
                            <p class="item-title">${extra['title']}</p>
                            <p class="item-date">${toDateString(extra['start_date'])} ~ ${toDateString(extra['end_date'])}</p>
                            <p class="item-content">${extra['content']}</p>
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
            }
                break;
            default:
                if (artist_codes.indexOf(target) >= 0) {
                    html = `
                <div class="content-wrap-inner slider-wrap lines-horizontal">
                    <div class="slick-wrap">`;
                    html += `
                    <div class="slick uploader ${target}">`;
                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        let extra = files.getExtra(target)[i];
                        html += `
                        <div class="slick-item">
                            <div class="image-item" style="background: url('/file/${extra['profile_id']}') no-repeat center; background-size: cover; font-size: 0;"></div>
                            <p class="item-title">${extra['name']}</p>
                            <p class="item-content">${extra['job']}</p>
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
                    html = `<div class="content-wrap-inner lines-horizontal">`
                    for (let i in files.get(target)) {
                        let file_id = files.get(target)[i];
                        let type = files.getExtra(target)[i];
                        let file_url = `/file/${file_id}`
                        if (type == 'image') {
                            html += `
                            <div class="upload-item"
                                 style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;">
                            </div>`
                        } else {
                            html += `
                            <div class="upload-item">
                                <video preload="metadata">
                                    <source src="${file_url}">
                                </video>
                            </div>`
                        }
                    }
                    html += `</div>`;

                    $container.append(html);
                    $container.setVideoCoverStyle();
                }
        }
    }
    let $wrapButtonControls = $parent.find(`.control-button-wrap`);
    $wrapButtonControls.empty();
    $wrapButtonControls.append(`
    <a href="javascript:editSetting('${target}');"
       class="button under-line edit">
        <img src="/asset/images/icon/edit.png"/>
        <span>${lang('edit')}</span>
    </a>`)
}

function refreshSetting() {
    let refresh = (target) => {
        if (isEmpty(target)) return;
        let $parent = $(`.content-box.${target}`)
        if ($parent.length == 0) return;
        let $container = $parent.find(`.content-wrap`);
        $container.empty()

        if ($parent.hasClass('editing')) {
            setEditing($parent, target)
        } else {
            setView($parent, target)
        }
    }

    let targets = files.getKeys();
    for (let i in targets) {
        refresh(targets[i])
    }
}

// override

function confirmProjectSearch(className, target) {
    let data = parseInputToData($(`.${className} input, .${className} textarea`))
    const id = data[target]
    if (id) {
        if (files.get(target).indexOf(id) >= 0) {
            openPopupMessage(lang('이미 선택된 프로젝트 입니다'))
            return;
        }
        files.push(target, id);
        apiRequest({
            type: 'GET',
            url: `/api/project/get/${id}`,
            data: data,
            dataType: 'json',
            success: function (response, status, request) {
                if (!response.success) {
                    openPopupErrors('popup-error', response, status, request);
                    return;
                }

                const data = response.data
                let file_url = `/file/${data['project_image_id']}`
                let $uploader = $(`.uploader.${target}`);

                if ($uploader.attr('class').includes('slick')) {
                    let index = $uploader.attr('total') - 1;
                    $uploader.addCustomSlickItem(index,
                        `<div class="slick-item">
                    <div class="image-item" style="background: url('${file_url}') no-repeat center; background-size: cover; font-size: 0;"></div>
                    <p class="item-title">${data['title']}</p>
                    <p class="item-date">${toDateString(data['start_date'])} ~ ${toDateString(data['end_date'])}</p>
                    <p class="item-content">${data['content']}</p>
                </div>`);

                    $uploader.initDraggable({
                        onDragFinished: generateOnDragFinished(target),
                    });
                }
            },
            error: function (response, status, error) {
                openPopupErrors('popup-error', response, status, error);
            },
        });
        closePopup(className);
    } else {
        openPopupMessage(lang('프로젝트를 선택해주세요'))
    }
}

// override
function confirmArtistSearch(className, target) {
    let data = parseInputToData($(`.${className} input, .${className} textarea`))
    const id = data[target]
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
                let $uploader = $(`.uploader.${target}`);

                if ($uploader.attr('class').includes('slick')) {
                    let index = $uploader.attr('total') - 1;
                    $uploader.addCustomSlickItem(index,
                        `<div class="slick-item">
                    <div class="image-item" style="background: url('${file_url}') no-repeat center; background-size: cover; font-size: 0;"></div>
                    <p class="item-title">${data['name']}</p>
                    <p class="item-content">${data['job']}</p>
                </div>`);

                    $uploader.initDraggable({
                        onDragFinished: generateOnDragFinished(target),
                    });
                }
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

function onMembershipInputValueChanged(element) {
    let $button = $(`#page-last .button-wrap .button`);
    if (!element.checked) {
        $button.addClass('disabled');
    } else {
        $button.removeClass('disabled');
    }
}

function onSettingChanged(element, code) {
    apiRequest({
        type: 'POST',
        url: `/api/setting/update`,
        data: {
            code: `main-show-${code}`,
            value: element.checked ? 1 : 0
        },
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                if (element.checked) {
                    element.checked = false;
                } else {
                    element.checked = true;
                }
                openPopupErrors('popup-error', response, status, request);
                return;
            }
        },
        error: function (response, status, error) {
            if (element.checked) {
                element.checked = false;
            } else {
                element.checked = true;
            }
            openPopupErrors('popup-error', response, status, error);
        },
    });
}
