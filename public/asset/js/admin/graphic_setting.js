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
    if (isEmpty(target)) return;
    let $parent = $(`.content-box.${target}`)
    let $container = $parent.find(`.content-wrap`);
    $container.empty()

    $parent.addClass('editing')

    refreshSettingFile();
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
                    if (target != 'main' && target != 'relation') {
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
            refreshSettingFile()
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
            break;
        }
        default:
            if (artist_codes.indexOf(target) >= 0) {
                {
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
                             onClick="searchArtist('${target}')">
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
                }
            } else {
                html = `<div class="content-wrap-inner lines-horizontal">`;
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
                        let type = files.getExtra(target)[i];
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
    if (target != 'main' && target != 'history') {
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
            }
                break;
            default:
                console.log(artist_codes, artist_codes.indexOf(target), target)
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

function refreshSettingFile() {
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

/** artist search **/

function searchArtist(target, page = 1) {
    apiRequest({
        type: 'GET',
        url: `/api/artist?page=${page}&code=${target}`,
        dataType: 'json',
        success: async function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            let data = response.data
            openArtistSearchPopup(target, data.array, data.pagination)
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function onSearchArtistSelected(className, target, id) {
    const $parent = $(`.${className} .table-wrap`)
    let $input = $parent.find('input[type=hidden]');
    if ($input != undefined) {
        $input.remove();
    }
    $parent.append(`<input type="hidden" name="${target}" value="${id}">`)
    const $selected = $parent.find('.selected')
    if ($selected != undefined) {
        $selected.removeClass('selected')
    }
    console.log($parent.find(`.item-${id}`))
    $parent.find(`.item-${id}`).addClass('selected')
}

async function openArtistSearchPopup(target, array, pagination) {
    let className = 'popup-artist-search';

    function addPagination($parent) {
        if (typeof $parent === 'string') {
            $parent = $($parent);
        }
        if (!$parent ||
            !$parent.get(0) ||
            !pagination ||
            !pagination['total'] ||
            !pagination['page'] ||
            !pagination['total-page'] ||
            !pagination['per-page']) {
            return;
        }

        let page = pagination['page'];
        let total_page = pagination['total-page'];

        let start = Math.floor((page - 1) / 5) * 5 + 1;
        let end = (Math.floor((page - 1) / 5) + 1) * 5;
        end = Math.min(end, total_page);

        let html = "";
        html += `<div class="pages">`
        if (start == 1) {
            html += `<span class="button disabled"><a href="#" onclick="return false"></a></span>`;
        } else {
            html += `<span class="button left"><a href="javascript:searchArtist(${start - 5})"></a></span>`;
        }
        for (let i = start; i <= end; ++i) {
            html += `<span class="number ${i == page ? 'now' : ''}"><a href="javascript:searchArtist(${i})">${i}</a></span>`;
        }
        if (total_page == end) {
            html += `<span class="button disabled"><a href="#" onClick="return false"></a></span>`;
        } else {
            html += `<span class="button left"><a href="javascript:searchArtist(${start - 5})"></a></span>`;
        }
        html += `</div>`;
        $parent.prepend(html);
    }

    function getHtml() {
        let html = `
        <div class="table-wrap">
        <div class="row-title">
            <div class="row">
                <span class="column code">${lang('분류')}</span>
                <span class="column name">${lang('name')}</span>
            </div>
        </div>
        <ul>`
        for (let item of array) {
            html += `
            <li class="row">
                <a class="button row-button item-${item['id']}" href="javascript:onSearchArtistSelected('${className}', '${target}', ${item['id']});">
                    <span class="column code">${item['code_artist']}</span>
                    <span class="column name">${item['name']}</span>
                </a>
            </li>`
        }
        html += `
        <div class="control-button-wrap absolute line-before">
            <div class="control-button-box">
                <a href="javascript:cancelArtistSearch('${className}', '${target}');"
                    class="button under-line cancel">
                    <img src="/asset/images/icon/cancel.png"/>
                    <span>${lang('cancel')}</span>
                </a>
               <a href="javascript:confirmArtistSearch('${className}', '${target}')" class="button confirm">
                    <img src="/asset/images/icon/check.png"/>
                    <span>${lang('confirm')}</span>
                </a>
            </div>
        </div>`;
        return html;
    }

    $parent = $(`.${className}`);
    if ($parent.length > 0) {
        $container = $parent.find('.popup-inner-wrap');
        $container.empty();
        $container.append(getHtml());
        addPagination($parent.find('.control-button-wrap'), pagination)
    } else {
        let css = await loadStyleFile('/asset/css/common/table.css', "." + className);
        css += await loadStyleFile('/asset/css/common/popup/artist_search.css', "." + className);
        openPopup({
            className: className,
            style: `<style>${css}</style>`,
            html: getHtml(),
        }, ($parent) => {
            addPagination($parent.find('.control-button-wrap'), pagination)
        })
    }
}

function cancelArtistSearch(className, target) {
    closePopup(className);
}

function confirmArtistSearch(className, target) {
    let data = parseInputToData($(`.${className} input, .${className} textarea`))
    const artist_id = data[target]
    if (artist_id) {
        if (files.get(target).indexOf(artist_id) >= 0) {
            openPopupMessage(lang('이미 선택된 아티스트입니다'))
            return;
        }
        files.push(target, artist_id);
        apiRequest({
            type: 'GET',
            url: `/api/artist/get/${data[target]}`,
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
