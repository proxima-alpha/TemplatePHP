$(document).ready(function () {
    $('body').setOnResolutionChanged((event) => {
        const $slick = $('.slider-wrap .slick');
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

            uploadData.clearItems();
            for (let target in data) {
                let array = data[target]
                for (let i in array) {
                    const item = array[i];
                    if (target == 'project' || (projectCodes.indexOf(target) >= 0)) {
                        uploadData.push(target, item['id'], {
                            image_id: item['image_id'],
                            title: item['title'],
                            start_date: item['start_date'],
                            end_date: item['end_date'],
                            content: item['content']
                        });
                    } else if (target != 'main' && target != 'relation') {
                        uploadData.push(target, item['id'], {
                            image_id: item['image_id'],
                            name: item['name'],
                            job: item['job']
                        });
                    } else {
                        uploadData.push(target, item['id'], {
                            type: item['type'],
                            relative_path: item['relative_path'],
                            width: item['width'],
                            height: item['height'],
                        });
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
        default:
            apiRequest({
                type: 'POST',
                url: target == 'project' ? `/api/project/post` : `/api/project/post/${target}`,
                data: {
                    projects: uploadData.get(target)
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

function generateOnSettingFileUploaded() {
    return (target, file_id, extra) => {
        uploadData.push(target, file_id, extra);
        let $parent = $(`.content-box.${target}`)
        let $container = $parent.find(`.content-wrap-inner`);
        $container.empty();

        const file_url = !extra ? `/file/${file_id}` : extra.relative_path;
        if (!extra || extra.type == 'image') {
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

    let index = uploadData.get(target).indexOf(id);
    if (index < 0) return;
    uploadData.splice(target, index);

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

function getMediaSlickItemHtml(target, isEditable = false) {
    return function (id, extra) {
        const file_url = !extra ? `/file/${id}` : extra.relative_path;
        if (isEditable) {
            if (!extra || extra.type == 'image') {
                return `
                <div class="slick-item draggable-item upload-item" draggable="true"
                     style="background: url('${file_url}') no-repeat center; background-size: cover; font-size: 0;">
                    Slider #${id}
                    <div class="size-text">${extra['width']}X${extra['height']}</div>
                    <input hidden type="text" name="id" value="${id}">
                    <div class="upload-item-hover">
                        <a href="javascript:deleteUploadedSlickFile('${target}', '${id}')"
                           class="button delete-image black">
                            <img src="/asset/images/icon/cancel_white.png"/>
                        </a>
                    </div>
                </div>`;
            } else {
                return `
                <div class="slick-item draggable-item upload-item" draggable="true">
                    <div class="size-text">${extra['width']}X${extra['height']}</div>
                    <video preload="metadata">
                        <source src="${file_url}">
                    </video>
                    <input hidden type="text" name="id" value="${id}">
                    <div class="upload-item-hover">
                        <a href="javascript:deleteUploadedSlickFile('${target}', '${id}')"
                           class="button delete-image black">
                            <img src="/asset/images/icon/cancel_white.png"/>
                        </a>
                    </div>
                </div>`;
            }
        } else {
            if (!extra || extra.type == 'image') {
                return `
                <div class="slick-item button"
                     style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;"
                     onclick="openImagePopup(${id})">
                    <div class="size-text">${extra['width']}X${extra['height']}</div>
                    Slider #${id}
                </div>`;
            } else {
                return `
                <div class="slick-item button">
                    <div class="size-text">${extra['width']}X${extra['height']}</div>
                    <video preload="metadata">
                        <source src="${file_url}">
                    </video>
                </div>`;
            }
        }
    }
}

function getProjectSlickItemHtml(target, isEditable = false) {
    const language = getCookie('lang')
    return function (id, extra) {
        if(isEditable) {
            return `
            <div class="slick-item draggable-item upload-item" draggable="true">
                    <div class="image-item-wrap"><div class="image-item" style="background: url('/file/${extra['image_id']}') no-repeat center; background-size: cover; font-size: 0;"></div></div>
                    <div class="text-item-wrap">
                        <p class="item-title">${language == 'ko' ? extra['title'] : extra['title_en']}</p>
                        <p class="item-date">${toDateString(extra['start_date'])} ~ ${toDateString(extra['end_date'])}</p>
                        <p class="item-content">${language == 'ko' ? extra['content'] : extra['content_en']}</p>
                    </div>
                <input hidden type="text" name="id" value="${id}">
                <div class="upload-item-hover">
                    <a href="javascript:deleteUploadedSlickFile('${target}', '${id}')"
                       class="button delete-image black">
                        <img src="/asset/images/icon/cancel_white.png"/>
                    </a>
                </div>
            </div>`
        } else {
            return `
            <div class="slick-item">
                <div class="image-item-wrap"><div class="image-item" style="background: url('/file/${extra['image_id']}') no-repeat center; background-size: cover; font-size: 0;"></div></div>
                <div class="text-item-wrap">
                    <p class="item-title">${language == 'ko' ? extra['title'] : extra['title_en']}</p>
                    <p class="item-date">${toDateString(extra['start_date'])} ~ ${toDateString(extra['end_date'])}</p>
                    <p class="item-content">${language == 'ko' ? extra['content'] : extra['content_en']}</p>
                </div>
            </div>`;
        }
    }
}

function setEditing($parent, target) {
    let $container = $parent.find(`.content-wrap`);
    let accept = getAcceptFromTarget(target);
    let html;
    switch
        (target) {
        case 'main':
        case 'relation': {
            $container.append(getSlickHtml(target, getMediaSlickItemHtml(target,true), () => {
                return `<div class="slick-item upload-item-add"
                     style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                    <label for="image-file" class="button"></label>
                    <input type="file" name="file" id="image-file"
                           onchange="onFileUpload(this, '${target}');"
                           accept="${accept}"/>
                </div>`
            }));
        }
            break;
        case 'project': {
            $container.append(getSlickHtml(target, getProjectSlickItemHtml(target,true), () => {
                return `
                <div class="slick-item upload-item-add button"
                     style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;"
                     onClick="searchProject('${target}')">
                </div>`;
            }));
        }
            break;
        default:
            if (projectCodes.indexOf(target) >= 0) {
                $container.append(getSlickHtml(target, getProjectSlickItemHtml(target,true), () => {
                    return `
                    <div class="slick-item upload-item-add button"
                         style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;"
                         onClick="searchProject('${target}')">
                    </div>`
                }));
            } else {
                html = `<div class="content-wrap-inner lines-horizontal">`;
                if (uploadData.get(target).length == 0) {
                    html += `
                    <div class="upload-item-add"
                         style="background: url('/asset/images/icon/plus_circle_big.png') no-repeat center; font-size: 0;">
                        <label for="${target}-file" class="button"></label>
                        <input type="file" name="file" id="${target}-file"
                               onchange="onFileUpload(this, '${target}', generateOnSettingFileUploaded());"
                               accept="${accept}"/>
                    </div>`;
                } else {
                    for (let i in uploadData.get(target)) {
                        let file_id = uploadData.get(target)[i];
                        let extra = uploadData.getExtra(target)[i];
                        const file_url = !extra ? `/file/${file_id}` : extra.relative_path;
                        if (!extra || extra.type == 'image') {
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
            }
    }

    let $slick = $container.find('.slick');
    if ($slick.length > 0) {
        $slick.setCustomSlick(isMobile(), {
            infinite: false,
            autoplay: false,
            draggable: false,
        });
        $container.find('.slick').initDraggable({
            onDragFinished: generateOnDragFinished(target)
        });
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
        style = ` style="height : 398px; line-height: 396px" `
    } else if (target != 'main' && target != 'relation') {
        style = ` style="height : 372px; line-height: 370px" `
    }
    if (uploadData.get(target).length == 0) {
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
            case 'relation' :
                $container.append(getSlickHtml(target, getMediaSlickItemHtml(target)));
                break;
            case 'project':
                $container.append(getSlickHtml(target, getProjectSlickItemHtml(target)));
                break;
            default:
                if (projectCodes.indexOf(target) >= 0) {
                    $container.append(getSlickHtml(target, getProjectSlickItemHtml(target)));
                } else {
                    html = `<div class="content-wrap-inner lines-horizontal">`
                    for (let i in uploadData.get(target)) {
                        let file_id = uploadData.get(target)[i];
                        let extra = uploadData.getExtra(target)[i];
                        const file_url = !extra ? `/file/${file_id}` : extra.relative_path;
                        if (!extra || extra.type == 'image') {
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
                }
        }
    }

    let $slick = $container.find('.slick');
    if ($slick.length > 0) {
        $slick.setCustomSlick(isMobile(), {
            infinite: false,
            autoplay: false,
            draggable: false,
        });
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

    let targets = uploadData.getKeys();
    for (let i in targets) {
        refresh(targets[i])
    }
}

// override

function searchProject(target, page = 1) {
    let searchTarget = target == 'project' ? 'all' : target;
    const assignCheckFieldName = searchTarget == 'all'? 'is_posted_popular' : 'is_posted';
    apiRequest({
        type: 'GET',
        url: `/api/project/${searchTarget}?page=${page}`,
        dataType: 'json',
        success: async function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            let data = response.data
            openProjectSearchPopup(target, data.array, data.pagination, assignCheckFieldName)
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

// override

function confirmProjectSearch(className, target) {
    let data = parseInputToData($(`.${className} input, .${className} textarea`))
    const id = data[target]
    if (id) {
        if (uploadData.get(target).indexOf(id) >= 0) {
            openPopupMessage(lang('message_item_already_selected'))
            return;
        }
        uploadData.push(target, id);
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
                let file_url = `/file/${data['image_id']}`
                let $uploader = $(`.uploader.${target}`);

                if ($uploader.attr('class').includes('slick')) {
                    let index = $uploader.attr('total') - 1;
                    $uploader.addCustomSlickItem(index, getProjectSlickItemHtml(true)(data.id, data));

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
        openPopupMessage(lang('message_item_select'))
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

function getSlickHtml(target, getSlickItemHtml, getAdditionalHtml = null) {
    let html = `
    <div class="content-wrap-inner slider-wrap lines-horizontal">
        <div class="slick-wrap">`;
    html += `
            <div class="slick uploader ${target}">`;

    for (let i in uploadData.get(target)) {
        let id = uploadData.get(target)[i];
        let extra = uploadData.getExtra(target)[i];
        html += getSlickItemHtml(id, extra);
    }
    if (getAdditionalHtml && typeof getAdditionalHtml == 'function') html += getAdditionalHtml()
    html += `
            </div>
        </div>
    </div>`;
    return html
}
