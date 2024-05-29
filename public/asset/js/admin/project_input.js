async function openCalendarPopup(target, expect_date = null, expect_time = null) {
    let className = 'popup-calendar';
    let html = `
    <h3 class="popup-title">
        ${lang('select_date')}
    </h3>
    <div class="form-wrap">
        <div class= "calendar-wrap">
            <div class= "calendar"></div>
        </div>
    </div>
    <div class="control-button-wrap absolute line-before">
        <div class="control-button-box">
            <a href="javascript:cancelCalendarSelect('${className}', '${target}');"
                class="button under-line cancel">
                <img src="/asset/images/icon/cancel.png"/>
                <span>${lang('cancel')}</span>
            </a>
            <a href="javascript:confirmCalendarSelect('${className}', '${target}')" class="button confirm">
                <img src="/asset/images/icon/check.png"/>
                <span>${lang('confirm')}</span>
            </a>
        </div>
    </div>`;
    openPopup({
        className: className,
        style: `
        <style>
            .popup {
                width: 400px;
            }
        </style>
        `,
        html: html,
    }, ($parent) => {
        $parent.find(`.calendar`).initCalendar({
            cellSize: 60,
            selectedDate: expect_date ?? null,
            standardDate: expect_date ?? null,
            limitStandard: false,
            limitPrevious: false,
        })
    })
}

function confirmCalendarSelect(className, target) {
    closePopup(className);
}

function cancelCalendarSelect(className, target) {
    closePopup(className);
}

function deleteUploadedArtistFile(target = 'topic', id) {
    let index = uploadData.get(target).indexOf(id.toString());
    if (index < 0) return;
    const $uploader = $(`.row-uploader.${target}`)
    const $inputs = $uploader.find('input[name="id"]');
    for(let i= 0; i< $inputs.length; ++i) {
        if($inputs.eq(i).val() == id) {
            uploadData.splice(target, index);
            $inputs.eq(i).parent().remove();
        }
    }
}

function confirmCalendarSelect(className, target) {
    let data = parseInputToData($(`.${className} input, .${className} textarea`))

    $(`.form-wrap input[name=${target}]`).val(data['date'])
    closePopup(className);
}

function removeRowDraggableItem(target, index, id) {
    if (id) {
        let index = uploadData.get(target).indexOf(id);
        if (index >= 0) uploadData.splice(target, index);
    }
    $(`.row-uploader.${target} .index-${index}`).remove();
}

function confirmArtistSearch(className, target) {
    let data = parseInputToData($(`.${className} input, .${className} textarea`))
    const id = data['artist']
    if (id) {
        if (uploadData.get(target).indexOf(id) >= 0) {
            openPopupMessage(lang('이미 선택된 아티스트입니다'))
            return;
        }
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
                uploadData.push(target, id, {
                    id: data.id,
                    name: data.name,
                    name_en: data.name_en,
                });
                let $container = $(`.row-uploader.${target}`);

                const index = $container.find('.row-uploader-item').length
                $container.append(getArtistItemHtml(target, index, true, data));

                $container.initDraggable({
                    onDragFinished: generateOnDragFinished(target),
                    afterDragFinished: () => {
                        refreshReward()
                    }
                });
                refreshReward()
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

function addRewardForm(target) {
    let $container = $(`.row-uploader.${target}`);
    const index = $container.find('.row-uploader-item').length
    $container.append(getRewardItemHtml(target, index, true));
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
}

function confirmEditProject(id) {
    let data = parseInputToData($(`.project-wrap .form-wrap.project .editable`))
    data['artists'] = uploadData.get('artist');
    data['project_image_id'] = uploadData.get('project');

    let rewards = [];
    let $rewards = $(`.project-wrap .form-wrap.extra .reward .row-uploader-item`);
    for (let i = 0; i < $rewards.length; ++i) {
        // TODO update values
        const $checkboxs = $reward.find('.checkbox-group.artist input[type=checkbox]:checked')
        const $reward = $rewards.eq(i);
        const rewardData = parseInputToData($reward.find('.editable'))
        if (Object.keys(rewardData).length > 0) {
            rewards.push(rewardData);
        }
    }
    data['rewards'] = rewards;

    apiRequest({
        type: 'POST',
        url: `/api/project/update/${id}`,
        data: data,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            history.back();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function confirmCreateProject() {
    let data = parseInputToData($(`.project-wrap .form-wrap.project .editable`))
    data['artists'] = uploadData.get('artist');
    data['project_image_id'] = uploadData.get('project');

    let rewards = [];
    let $rewards = $(`.project-wrap .form-wrap.extra .reward .row-uploader-item`);
    for (let i = 0; i < $rewards.length; ++i) {
        const $reward = $rewards.eq(i);
        const rewardData = parseInputToData($reward.find('.editable'))
        if (Object.keys(rewardData).length > 0) {
            rewards.push(rewardData);
        }
    }
    data['rewards'] = rewards;

    apiRequest({
        type: 'POST',
        url: `/api/project/create`,
        data: data,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            history.back();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}
