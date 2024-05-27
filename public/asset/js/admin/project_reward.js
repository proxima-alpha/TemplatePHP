function onRewardItemSelectChanged(element, target, index) {
    console.log($(element).val())
    const $container = $(`.row-uploader.${target} .index-${index}`);
    const selectedValue = $(element).val();
    if (selectedValue == 'all') {
        $container.find('.input-wrap.select-box.artist').remove();
    } else if (selectedValue == 'random') {
        const $selectBox = $container.find('.select-box');
        $selectBox.after(`
        <div class="input-wrap inline select-box artist">
            <p class="input-title">${lang('artist')}</p>
                <input type="checkbox" name="test" />
        </div>`)
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
            for (let i in array) {
                let $container = $(`.row-uploader.${target}`);
                $container.append(getRewardItemHtml(target, i, isEditable, array[i]));
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
            for (let i in array) {
                let $container = $(`.row-uploader.${target}`);
                $container.append(getArtistItemHtml(target, i, isEditable, array[i]));
            }
        },
        error: function (response, status, error) {
        },
    });
}
