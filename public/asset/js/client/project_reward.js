
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
        <p class="title">${language == 'ko' ? data['title'] : data['title_en']}</p>`
    if (data['type'] === 'all') {
        html += `<p class="type">${lang('reward_type_all')}</p>`
    } else if (data['type'] === 'random' && (data['artists'] ?? null)) {
        let artistString = ''
        let prefix = ''
        for (const artist of data['artists']) {
            artistString += `${prefix}${language == 'ko' ? artist['name'] : artist['name_en']}`
            prefix = '/';
        }
        html += `<p class="type">${artistString}</p>`
    }
    html += `<p class="content">${language == 'ko' ? data['content'] : data['content_en']}</p>
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
