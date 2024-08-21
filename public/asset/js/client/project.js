let rewards = {};
let selectedReward;
let selectedArtistId;

function onRewardSelected(element, id) {
    selectedReward = rewards[id];
    $parent = $(element).parent();
    $parent.find('.selected').removeClass('selected')
    $(element).addClass('selected')
}

function getRewardItemHtml(data, isSelectable = true) {
    let language = getCookie('lang')
    let html = `
    <div class="reward-wrap" ${isSelectable? `onclick="javascript:onRewardSelected(this, '${data.id}')"` : ''}>
        <p class="title">${getLangItem(data, 'title', language)}</p>`
    if (data['type'] === 'all') {
        html += `<p class="type">${lang('reward_type_all')}</p>`
    } else if (data['type'] === 'random' && (data['artists'] ?? null)) {
        let artistString = ''
        let prefix = ''
        for (const artist of data['artists']) {
            artistString += `${prefix}${getLangItem(artist, 'name', language)}`
            prefix = '/';
        }
        html += `<p class="type">${artistString}</p>`
    }
    html += `<p class="content">${getLangItem(data, 'content', language)}</p>
        <p class="remaining-count">${sprintf(lang('reward_now_stock_string'), (data['total_count'] - data['purchased_count']))}</p>
        <p class="limited-count">${sprintf(lang('reward_limited_count_string'), data['limited_count'])}</p>
        <p class="available-count">${sprintf(lang('reward_available_count_string'), data['available_count'])}</p>
        <div class="line"></div>
        <p class="price">${toFormatNumber(data['price'])} KRW</p>
    </div>`
    return html
}

function loadReward(project_id, isSelectable = true, artist_id = null) {
    selectedArtistId = artist_id
    const queryParams = artist_id? `?artist_id=${artist_id}` : ``;
    return apiRequest({
        type: 'GET',
        url: `/api/project/reward/${project_id}${queryParams}`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                return;
            }
            let array = response.data.array;
            let $container = $(`.reward-box`);
            // object 로 parsing 하여 저장
            for (const item of array) {
                rewards[item.id] = item;
            }
            $container.find(`.reward-wrap`).remove();
            for (let i in array) {
                $container.append(getRewardItemHtml(array[i], isSelectable));
            }
            try {
                $container.initDraggable({
                    onDragFinished: async (from, to) => {
                        let temp = from.style.background;
                        from.style.background = to.style.background;
                        to.style.background = temp;
                        return true;
                    },
                });
            } catch (e) {
                // do nothing
                // topic view page doesn't need initDraggable
            }
        },
        error: function (response, status, error) {
        },
    });
}

function setArtist(project_id, id) {
    loadReward(project_id, false, id)
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
                    <h3 class="name">${getLangItem(data, 'name', language)}</h3>
                    <h4 class="job">${getLangItem(data, 'job', language)}</h4>
                </div>
                <p class="content-text-wrap">${getLangItem(data, 'introduction', language).replaceAll('\n', '<br/>')}</p>`;

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
                            <video preload="metadata" controls style="width: 100%; height: 100%;" controlsList="nodownload" poster="${preview['poster']}">
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
            setPosterForVideo($container.find(`video`));
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
    const queryParams = selectedArtistId? `?artist_id=${selectedArtistId}` : ``;
    window.location.href = `/project/${id}/purchase${queryParams}`;
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
