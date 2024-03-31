$(document).ready(function () {
    try {
        $('.slick.uploader').initDraggable({
            onDragFinished: generateOnDragFinished('artist_preview'),
        });
    } catch (e) {
        // do nothing
        // topic view page doesn't need initDraggable
    }
    let $slick = $('.slider-wrap .slick');
    $slick.setOnResolutionChanged((event) => {
        let isSwipe = $slick.hasClass('uploader') ? false : true;
        $slick.setCustomSlick(event.detail.isMobile, {
            infinite: false,
            autoplay: false,
            draggable: false,
            swipe: isSwipe,
        });
        try {
            $slick.initDraggable({
                onDragFinished: generateOnDragFinished('artist_preview'),
            });
        } catch (e) {
            // do nothing
            // topic view page doesn't need initDraggable
        }
    })
});
