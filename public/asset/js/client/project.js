function setArtist(id) {
    apiRequest({
        type: 'GET',
        url: `/api/artist/get/${id}`,
        dataType: 'json',
        success: async function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            let data = response.data
            const language = getCookie('lang');
            $('#artists .selected').removeClass('selected')
            $(`#artist-${id}`).addClass('selected')
            const $container = $('.container-inner .artist-box');
            $container.empty();
            let html = `
                <div class="title-text-wrap">
                    <h3 class="name">${language == 'ko' ? data.name : data.name_en}</h3>
                    <h4 class="job">${language == 'ko' ? data.job : data.job_en}</h4>
                </div>
                <p class="content-text-wrap">${(language == 'ko' ? data.introduction : data.introduction_en).replaceAll('\n', '<br/>')}</p>`;

            if (data.previews.length > 0) {
                html += `
                <div class="preview-wrap">
                <div class="scroll-control-button-wrap">
                    <a href="javascript:;" onclick="onClickScrollLeft(this)" class="button left">
                        <img src="/asset/images/icon/button_left.png"/>
                    </a>
                    <a href="javascript:;" onclick="onClickScrollRight(this)" class="button right">
                        <img src="/asset/images/icon/button_right.png"/>
                    </a>
                </div>
                <div class="content-wrap scroll-horizontal-wrap">
                    <div class="content-wrap-inner" style="width: ${data['previews'].length * 180}px;">`;
                for (let preview of data.previews) {
                    let file_id = preview['id'];
                    let type = preview['type'];
                    let file_url = preview['relative_path'];
                    if (type == 'image') {
                        html += `
                        <div class="content-item button"
                             style="background: url('${file_url}') no-repeat center; font-size: 0; background-size: cover;"
                             onclick="openImagePopup(${file_id})">
                        </div>`;
                    } else {
                        html += `
                        <div class="content-item">
                            <video preload="metadata" controls style="width: 100%; height: 100%;">
                                <source src="${file_url}">
                            </video>
                            <p class="time-string">${secToString(preview['time'])}</p>
                        </div>`
                    }
                }
                html += `
                        </div>
                    </div>
                </div>`;
            }
            $container.append(html);
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function purchase(id, start_date, end_date) {
    if (getCookie('is_login') != 1) {
        openPopupMessage(lang('message_error_login'), '/login')
        return
    }
    // TODO move into purchase.js
    // if (available_count <= 0) {
    //     return openPopupMessage(lang('message_error_exceed'))
    // }
    const rawNowDate = new Date()
    if (!isEmpty(start_date)) {
        const rawStartDate = new Date(start_date)
        if (rawStartDate > rawNowDate) {
            return openPopupMessage(lang('message_error_not_started'))
        }
    }
    if (!isEmpty(end_date)) {
        const rawEndDate = new Date(end_date).setHours(23, 59, 59)
        if (rawEndDate < rawNowDate) {
            return openPopupMessage(lang('message_error_expired'))
        }
    }
    window.location.href = `/project/${id}/purchase`;
}

function onClickScrollLeft(element) {
    const $parent = $(element).parent().parent();
    const $container = $parent.find('.content-wrap');
    let offset = 0;
    if ($container.scrollLeft()) offset = $container.scrollLeft()
    offset -= $container.width()
    if (offset < 0) offset = 0
    $container.animate({scrollLeft: offset}, 500);
}

function onClickScrollRight(element) {
    const $parent = $(element).parent().parent();
    const $container = $parent.find('.content-wrap');
    let offset = 0;
    if ($container.scrollLeft()) offset = $container.scrollLeft()
    offset += $container.width()
    $container.animate({scrollLeft: offset}, 500);
}
