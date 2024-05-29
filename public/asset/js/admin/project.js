const rewardCheckboxBuffer = {}

function onRewardRandomItemChanged(index, element) {
    if (!rewardCheckboxBuffer[index]) rewardCheckboxBuffer[index] = [];
    const id = element.getAttribute('id');
    if(element.checked) {
        if(rewardCheckboxBuffer[index].indexOf(id) < 0) {
            rewardCheckboxBuffer[index].push(id)
        }
    } else {
        const i = rewardCheckboxBuffer[index].indexOf(id);
        if(i >= 0) {
            rewardCheckboxBuffer[index].splice(i, 1);
        }
    }
}

function getRewardRandomItemHtml(index, array, isEditable = true, data = null) {
    const language = getCookie('lang')
    let html = ``;
    for (const i in array) {
        const item = array[i];
        const checked = data && data.indexOf(item.id) >= 0 ? 'checked' : '';
        const option = isEditable ? '' : 'disabled';
        html += `
        <div class="input-wrap">
            <input class="editable" type="checkbox" id="${item.id}" name="artist" ${checked} ${option} onchange="onRewardRandomItemChanged(${index}, this)"/>
            <p class="input-title">${language == 'ko' ? item['name'] : item['name_en']}</p>
        </div>`
    }
    return `
    <div class="checkbox-group artist">
        ${html}
    </div>`
}

function onRewardItemSelectChanged(element, target, index, isEditable = true) {
    const $container = $(`.row-uploader.${target} .index-${index}`);
    const selectedValue = $(element).val();
    if (selectedValue == 'all') {
        $container.find('.checkbox-group.artist').remove();
    } else if (selectedValue == 'random') {
        // const $selectBox = $container.find('.select-box');
        // const extras = uploadData.getExtra('artist');
        // $selectBox.after(getRewardRandomItemHtml(index, extras, isEditable))
        refreshReward(isEditable)
    }
}

function getRewardItemHtml(target, index, isEditable = true, data = null) {
    const option = isEditable ? '' : 'readonly'
    return `
    <div class="draggable-item row-uploader-item index-${index}" ${isEditable ? 'draggable="true"' : ''}>
        ${data?.['id'] ? `<input hidden class="editable" type="text" name="id" value="${data?.['id']}">` : ''}
        <div class="input-wrap inline select-box">
            <p class="input-title">${lang('category')}</p>
            <select class="editable" name="type" value="${data?.['type'] ?? ''}" onchange="onRewardItemSelectChanged(this, '${target}', '${index}')" ${isEditable ? '' : 'disabled'}>
                <option value="all" ${data?.['type'] == 'all' ? 'selected' : ''}>${lang('reward_type_all')}</option>
                <option value="random" ${data?.['type'] == 'random' ? 'selected' : ''}>${lang('reward_type_random')}</option>
            </select>
        </div>
        <div class="tab-box">
            <div class="tab-button-wrap">
                <a class="button ko active" onclick="clickTab(this,'ko')">한국어</a>
                <a class="button en" onclick="clickTab(this,'en')">English</a>
            </div>
            <div class="tab-wrap ko active">
                <div class="input-wrap">
                    <p class="input-title">${lang('title')}</p>
                    <input type="text" name="title" class="editable under-line" value="${data?.['title'] ?? ''}" ${option}/>
                </div>
               <div class="input-wrap">
                   <p class="input-title">${lang('content')}</p>
                   <textarea class="editable" name="content" onkeydown="resizeInputPopupTextarea(this)"
                             onkeyup="resizeInputPopupTextarea(this)" ${option}>${data?.['content'] ?? ''}</textarea>
               </div>
            </div>
            <div class="tab-wrap en">
                <div class="input-wrap">
                    <p class="input-title">${lang('title')}</p>
                    <input type="text" name="title_en" class="editable under-line" value="${data?.['title_en'] ?? ''}" ${option}/>
                </div>
               <div class="input-wrap">
                   <p class="input-title">${lang('content')}</p>
                   <textarea class="editable" name="content_en" onkeydown="resizeInputPopupTextarea(this)"
                             onkeyup="resizeInputPopupTextarea(this)" ${option}>${data?.['content_en'] ?? ''}</textarea>
               </div>
            </div>
        </div>
        <div class="line"></div>
        <div class="input-wrap price">
           <p class="input-title">${lang('price')}</p>
           <input type="number" name="price" class="editable under-line" value="${data?.['price'] ?? 0}" ${option}/>
           <p class="description">KRW</p>
        </div>
        <div class="column">
           <div class="input-wrap">
               <p class="input-title">${lang('stock_count')}</p>
               <input type="number" name="total_count" class="editable under-line" value="${data?.['total_count'] ?? 0}" ${option}/>
           </div>
           <div class="input-wrap">
               <p class="input-title">${lang('available_count')}</p>
               <input type="number" name="limited_count" class="editable under-line" value="${data?.['limited_count'] ?? 0}" ${option}/>
           </div>
        </div>

        ${isEditable ? `<a href="javascript:removeRowDraggableItem('${target}', '${index}')"
           class="button delete-image">
            <img src="/asset/images/icon/cancel.png"/>
        </a>` : ''}
    </div>`
}

function loadReward(project_id, isEditable = true) {
    return apiRequest({
        type: 'GET',
        url: `/api/project/reward/${project_id}`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                return;
            }
            let array = response.data.array;
            const target = 'reward';
            let $container = $(`.row-uploader.${target}`);
            for (let i in array) {
                const data = array[i];
                uploadData.push(target, data.id);
                $container.append(getRewardItemHtml(target, i, isEditable, array[i]));
                if (data['type'] == 'random') {
                    const $selectBox = $container.find(`.index-${i}`).find('.select-box');
                    const extras = uploadData.getExtra('artist');
                    $selectBox.after(getRewardRandomItemHtml(i, extras, isEditable, data.artists?.map(artist => artist.id) ?? []))
                }
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

function getArtistItemHtml(target, index, isEditable = true, data = null) {
    let file_url = `/file/${data['profile_id']}`
    return `
    <div class="draggable-item row-uploader-item index-${index}" ${isEditable ? 'draggable="true"' : ''}>
        <input hidden type="text" name="id" value="${data['id']}">
        <div class="profile" style=" background: url('${file_url}'); background-size: cover; font-size: 0;"></div>
        <div class="info-wrap">
            <p class="name">${data['name']}</p>
            <p>${data['job']}</p>
            <p>${data['introduction']}</p>
        </div>
        ${isEditable ? `<div class="upload-item-hover">
            <a href="javascript:removeRowDraggableItem('${target}', '${index}', '${data['id']}')"
               class="button delete-image black">
                <img src="/asset/images/icon/cancel_white.png"/>
            </a>
        </div>` : ''}
    </div>`
}

function loadArtist(project_id, isEditable = true) {
    return apiRequest({
        type: 'GET',
        url: `/api/project/artist/${project_id}`,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                return;
            }
            let array = response.data.array;
            const target = 'artist';
            let $container = $(`.row-uploader.${target}`);
            for (let i in array) {
                const data = array[i];
                uploadData.push(target, data.id, {
                    id: data.id,
                    name: data.name,
                    name_en: data.name_en,
                });
                $container.append(getArtistItemHtml(target, i, isEditable, array[i]));
            }
            try {
                $container.initDraggable({
                    onDragFinished: generateOnDragFinished(target),
                    afterDragFinished: () => {
                        refreshReward()
                    }
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

function refreshReward(isEditable = true) {
    console.log('refresh-reward')
    let $rewards = $(`.project-wrap .form-wrap.extra .reward .row-uploader-item`);
    const extras = uploadData.getExtra('artist')
    for (let i = 0; i < $rewards.length; ++i) {
        const $reward = $rewards.eq(i);

        if ($reward.find('select[name=type]').val() == 'random') {
            $reward.find('.checkbox-group.artist').remove();
            const $selectBox = $reward.find('.select-box');
            $selectBox.after(getRewardRandomItemHtml(i, extras, isEditable, rewardCheckboxBuffer[i] ?? []))
        }
    }
}
