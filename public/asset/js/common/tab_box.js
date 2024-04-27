function clickTab(element, className) {
    $(element).parent().parent().find('.active').removeClass('active');
    $(element).parent().parent().find(`.${className}`).addClass('active')
}
