let purchaseItems = [];
let purchaseItemsCount = 1;
let rewardRequests = {}

let rewardPageIndex = 1;

function getPurchaseItemHtml(index, item) {
    if (!selectedReward) return;
    const rewardPrice = selectedReward['price'];

    let html = `
        <form class="form-wrap">
            <input class="editable" hidden type="number" name="price"
                   value="${rewardPrice}"/>
            <div class="input-wrap">
                <p class="input-title">${lang('reward_purchase_item_01')}</p>
                <select class="editable" name="code_reward_request_id">`
    const language = getCookie('lang')
    for (const id in rewardRequests) {
        const code = rewardRequests[id];
        html += `<option value="${code['id']}">${language == 'ko' ? code['name'] : code['name_en']}</option>`
    }
    html += `</select>
            </div>
            <div class="input-wrap mine">
                <p class="input-title">${lang('reward_purchase_item_02')}</p>
                <div class="input-item-wrap">
                    <input class="editable" type="radio" id="is-mine-true-${index}" name="is_mine" value="1" ${item['is_mine'] == '1' || item['is_mine'] == null ? 'checked' : ''}>
                    <label for="is-mine-true-${index}">${lang('reward_purchase_item_02_1')}</label>
                </div>
                <div class="input-item-wrap">
                    <input class="editable" type="radio" id="is-mine-false-${index}" name="is_mine" value="0" ${item['is_mine'] ?? null == '0' ? 'checked' : ''}>
                    <label for="is-mine-false-${index}">${lang('reward_purchase_item_02_2')}</label>
                </div>
            </div>
            <div class="input-wrap inquirer">
                <p class="input-title">${lang('reward_purchase_item_03')}</p>
                <input class="editable" type="text" name="inquirer_name" placeholder="${lang('reward_purchase_item_03_1')}" value="${item['inquirer_name'] ?? ''}"/>
                <input class="editable" type="email" name="inquirer_email" placeholder="${lang('reward_purchase_item_03_2')}" value="${item['inquirer_email'] ?? ''}"/>
            </div>
            <div class="input-wrap comment">
                <p class="input-title">${lang('reward_purchase_item_04')} ${lang('reward_purchase_item_04_1')}</p>
                <textarea class="editable" name="inquirer_comment" maxlength="300">${item['inquirer_comment'] ?? ''}</textarea>
            </div>
            <div class="input-wrap inline agree">
                <input class="editable" type="checkbox" id="is-agree-${index}" name="is_agreed" ${item['is_agreed'] ?? null == '1' ? 'checked' : ''}/>
                <label for="is-agree-${index}" class="input-title">${lang('reward_purchase_item_05')}</label>
            </div>
        </form>
    `;
    return html
}

function getPurchaseItemHtmlInShort(item) {
    let html = `
        <form class="form-wrap">
            <div class="input-wrap inline">
                <p class="input-title">${lang('name')}</p>
                <input type="name" name="inquirer_name" value="${item['inquirer_name']}" readonly/>
            </div>
            <div class="input-wrap inline">
                <p class="input-title">${lang('email')}</p>
                <input type="email" name="inquirer_email" value="${item['inquirer_email']}" readonly/>
            </div>
            <div class="input-wrap inline">
                <p class="input-title">${lang('reward_reaction')}</p>
                <input type="text" name="code_reaction_name" value="${rewardRequests[item['code_reward_request_id']].name}" readonly/>
                <input hidden type="text" name="code_reaction_id" value="${item['code_reward_request_id']}" readonly/>
            </div>
            <div class="input-wrap inline">
                <p class="input-title">${lang('reward_request')}</p>
                <textarea type="text" name="inquirer_comment" readonly>${item['inquirer_comment']}</textarea>
            </div>
        </form>
    `;
    return html
}

function getSelectedRewardHtml() {
    if (!selectedReward) return ``;
    if (selectedReward['available_count'] == 0) {
        $('.content-box > div.button-wrap .button.next').addClass('disabled')
    } else {
        $('.content-box > div.button-wrap .button.next').removeClass('disabled')
    }
    return `
    <div class="select-payment-box">
        <div class="limited-count-wrap">
            <span class="title">${lang('available_count')}</span>
            <span class="content">${selectedReward['available_count']}</span>
        </div>
        <input type="number" name="count" class="editable" value="${selectedReward['available_count'] > 0 ? 1 : 0}"
               onchange="onCountChange(this)"/>
        <div class="total-price">
            <input type="text" name="paid" value="${toFormatNumber(selectedReward['price'])}" readonly/>
            <p>KRW</p>
        </div>
    </div>`
}

function getSelectedRewardItemHtml(data) {
    const language = getCookie('lang')
    let html = `
    <div class="reward-wrap">
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
        artistString += `(${lang('reward_type_random')})`
        html += `<p class="type">${artistString}</p>`
    }
    html += `
        <div class="line"></div>
        <p class="price">${toFormatNumber(data['price'])} KRW</p>
    </div>`
    return html
}

/* override */
function onRewardSelected(element, id) {
    selectedReward = rewards[id];
    $parent = $(element).parent();
    $parent.find('.selected').removeClass('selected')
    $(element).addClass('selected')

    $('#page-1 .select-payment-box').remove();
    $('#page-1').append(getSelectedRewardHtml());
    //reset purchase item
    generatePurchaseItems(1)

    const rewardPrice = selectedReward['price'];
    const $totalPrice = $('.total-price input');
    $totalPrice.val(toFormatNumber(`${rewardPrice}`))
    $('#page-3 .project-box .reward-wrap').remove()
    $('#page-3 .project-box').prepend(getSelectedRewardItemHtml(selectedReward))
}

function generatePurchaseItems(count) {
    //page-2 에서 구매 수량 세트 생성
    const $page2 = $('#page-2');
    const container = $page2.find('.purchase-item-wrap');
    let html = '';
    for (let i = 0; i < count; ++i) {
        if (i < purchaseItems.length) {
            html += getPurchaseItemHtml(i, purchaseItems[i]);
        } else {
            const user_name = getCookie('user_name')
            const user_email = getCookie('user_email')
            html += getPurchaseItemHtml(i, {
                inquirer_name: user_name,
                inquirer_email: user_email,
            });
        }
    }
    container.empty();
    container.append(html);
}


function onCountChange(element) {
    if (!selectedReward) return;
    const availableCount = selectedReward['available_count'];
    const rewardPrice = selectedReward['price'];
    const count = $(element).val()
    if (count < 1) {
        $(element).val(1);
        return;
    }
    if (count > availableCount) {
        $(element).val(availableCount);
        return;
    }
    purchaseItemsCount = count;
    const $totalPrice = $('.total-price input');
    $totalPrice.val(toFormatNumber(`${count * rewardPrice}`))
    generatePurchaseItems(count);
}

function openPurchaseItemWrap() {
    $('#page-3 .purchase-item-wrap').css({
        height: 'auto'
    })
    $('#page-3 .purchase-item-box .button-wrap').css({
        display: 'none'
    })
}

function onClickNext() {
    if (rewardPageIndex == 3) return;
    if (rewardPageIndex == 1) {
        $(`.container-inner .content-box .button-wrap .prev`).removeClass('disabled')
    } else if (rewardPageIndex == 2) {
        getPurchaseItemData();
        for (let i = 0; i < purchaseItemsCount; ++i) {
            for (const key of Object.keys(purchaseItems[i])) {
                const value = purchaseItems[i][key];
                if (value === '') {
                    openPopupMessage(lang('message_error_field_empty'))
                    return;
                }
            }
        }
        const $page3 = $('#page-3');
        const container = $page3.find('.purchase-item-wrap');
        let html = '';
        for (let i = 0; i < purchaseItemsCount; ++i) {
            html += getPurchaseItemHtmlInShort(purchaseItems[i]);
        }
        container.empty();
        container.append(html);
        if (purchaseItemsCount <= 1) {
            $('#page-3 .purchase-item-box .button-wrap').css({
                display: 'none'
            })
        }
        $(`.container-inner .content-box > div.button-wrap`).css({
            display: 'none',
        })
    }
    rewardPageIndex++;
    refreshViews();
}

function onClickPrev() {
    if (rewardPageIndex == 1) return;
    if (rewardPageIndex == 2) {
        $(`.container-inner .content-box .button-wrap .prev`).addClass('disabled')
        getPurchaseItemData();
    }
    rewardPageIndex--;
    refreshViews();
}

function getPurchaseItemData() {
    const $purchaseItems = $(`#page-2 .purchase-item-wrap .form-wrap`)
    for (let i = 0; i < $purchaseItems.length; ++i) {
        const $reward = $purchaseItems.eq(i);
        const data = parseInputToData($reward.find('.editable'))
        if (Object.keys(data).length > 0) {
            if (purchaseItems[i]) {
                purchaseItems[i] = data;
            } else {
                purchaseItems.push(data);
            }
        }
    }
}

function refreshViews() {
    $(`.container-inner .content-box .page`).css({
        display: 'none',
    })
    $(`#page-${rewardPageIndex}`).css({
        display: 'block',
    })
    const $stage = $(`.side-content-box .stage-box`).children();
    $stage.removeClass('selected')
    $stage.eq(rewardPageIndex - 1).addClass('selected')
    $('html').scrollTop(0);
}

function requestPayment() {
    if (!selectedReward) return;
    let language = getCookie('lang')
    let data = parseInputToData($(`.payment-box .form-wrap .editable`))
    data['reward_id'] = selectedReward['id'];
    if (data['paid']) data['paid'] = data['paid'].replaceAll(',', '');
    data['purchase_items'] = purchaseItems.slice(0, purchaseItemsCount);
    // DB 저장은 동의 유무인데 지문이 비동의 유무이기 때문에 request 시에 변경
    for (let i in data['purchase_items']) {
        const isAgree = data['purchase_items'][i]['is_agreed'];
        data['purchase_items'][i]['is_agreed'] = isAgree == 0 ? 1 : 0;
    }
    apiRequest({
        type: 'POST',
        url: `/api/purchase`,
        data: data,
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            const purchase_id = response.data['id'];
            const payRequestData = {
                pg: data['pg'],
                pay_method: "card",
                merchant_uid: response.data['merchant_uid'], // 주문번호
                name: (language == 'ko' ? selectedReward['title'] : selectedReward['title_en']),
                amount: data['paid'], // 숫자 타입
                buyer_email: data['purchaser_email'],
                buyer_name: data['purchaser_name'],
                currency: 'KRW',
                language: language,
                m_redirect_url: `${window.location.host}/project/purchase/${purchase_id}/complete`,
                notice_url: `${window.location.host}/api/purchase/webhook`,
            };
            IMP.request_pay(payRequestData,
                (rsp) => {
                    if (rsp.success) {
                        completePayment(purchase_id, rsp.imp_uid, rsp.merchant_uid)
                    } else {
                        alert(rsp.error_msg);
                    }
                    // callback
                    //rsp.imp_uid 값으로 결제 단건조회 API를 호출하여 결제결과를 판단합니다.
                },
            );
            // history.back();
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}

function completePayment(id, imp_uid, merchant_uid) {
    apiRequest({
        type: 'POST',
        url: `/api/purchase/${id}/complete`,
        data: {
            imp_uid: imp_uid,
            merchant_uid: merchant_uid,
        },
        dataType: 'json',
        success: function (response, status, request) {
            if (!response.success) {
                openPopupErrors('popup-error', response, status, request);
                return;
            }
            window.location.href = '/project/purchase/complete';
        },
        error: function (response, status, error) {
            openPopupErrors('popup-error', response, status, error);
        },
    });
}
