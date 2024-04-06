$(document).ready(function () {
    try {
        $('.row-uploader.artists').initDraggable({
            onDragFinished: generateOnDragFinished('artists'),
        });
    } catch (e) {
        // do nothing
        // topic view page doesn't need initDraggable
    }
    try {
        $('.row-uploader.reward').initDraggable({
            onDragFinished: async (from, to) => {
                console.log(from, to)
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
});
