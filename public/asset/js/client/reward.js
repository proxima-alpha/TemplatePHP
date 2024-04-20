
const purchaseItems = [];

function onCountChange(element, price) {
    const $parent = $(element).parent()
    const count = $(element).val()
    if (count < 1) {
        $(element).val(1);
        return;
    }
    const $totalPrice = $('.total-price input');
    $totalPrice.val(`${count * price}`)

    //page-2 에서 구매 수량 세트 생성
    const $page2 = $('#page-2');
    for(let i = 0; i< purchaseItems.length || i < count; ++i) {

    }
    const childCount = $page2.children().length;
}

let rewardPageIndex = 1;

function onClickNext() {
    if (rewardPageIndex == 3) return;
    if (rewardPageIndex == 1) {
        $(`.container-inner .content-box .button-wrap .prev`).removeClass('disabled')
    } else if(rewardPageIndex == 2) {
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
    }
    rewardPageIndex--;
    refreshViews();
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
}
