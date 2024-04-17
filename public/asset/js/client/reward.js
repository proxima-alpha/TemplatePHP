function onCountChange(element, price) {
    const $parent = $(element).parent()
    const count = $(element).val()
    const $totalPrice = $parent.find('.total-price');
    $totalPrice.empty();
    $totalPrice.append(`${count * price} KRW`)
}

let rewardPageIndex = 1;

function onClickNext() {
    if (rewardPageIndex == 3) return;
    if (rewardPageIndex == 1) {
        $(`.container-inner .content-box .button-wrap .prev`).removeClass('disabled')
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
    $(`.container-inner .content-box .page-${rewardPageIndex}`).css({
        display: 'block',
    })
    const $stage =  $(`.side-content-box .stage-box`).children();
    $stage.removeClass('selected')
    $stage.eq(rewardPageIndex-1).addClass('selected')
}
