/**
 * @file "draggable" 클래스를 추가하여 상호간에 자리교환이 가능하도록 하는 기능 스크립트
 */
let draggableDragged = null;
let draggableDropped = null;
let $draggableHandle = null;
let $draggableItemMap = {};

/**
 * pc용
 * drag 시작 handler
 * @param event
 */
function handleDragStart(event) {
    // wait for async function in drop handler
    if (draggableDropped) return;
    draggableDragged = this;

    let e = (event.originalEvent || event);
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.dropEffect = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
}

/**
 * pc용
 * drag 종료 handler
 * drop 이 안일어 나는 경우 전역변수 draggableDragged 를 null 로 만들기 위해 추가
 * @param event
 */
function handleDragEnd(event) {
    // wait for async function in drop handler
    if (draggableDropped) return;
    draggableDragged = null;
}

/**
 * pc용
 * drop handler
 * 데이터 변경 및 onDragFinished callback 호출
 * API 호출 후 결과에 따라 값을 반영/미반영 할 수 있으므로 await 로 호출함
 * @param event
 * @returns {Promise<function(*): boolean>}
 */
function handleDrop(parentElement) {
    return async function (event) {
        $(event.currentTarget).removeClass('active')
        let e = (event.originalEvent || event);
        e.preventDefault();

        function getParentNode(element) {
            let parentNode = element.parentNode;
            if(parentElement.hasClass('mobile'))  {
                parentNode = parentNode.parentNode
            }
            return parentNode;
        }

        if (draggableDragged !== this) {
            draggableDropped = this;
            if (getParentNode(draggableDragged).className === getParentNode(draggableDropped).className) {
                // callback (onDragFinished) 가 비동기 작업일 경우
                // e.dataTransfer.getData 에서 값이 바르게 전달이 안되므로
                // 이벤트 발생 시 미리 변수에 값을 담아둔다
                let transferredData = e.dataTransfer.getData('text/html');
                let isExchanged;

                if (parentElement.onDragFinished && typeof parentElement.onDragFinished == 'function') {
                    isExchanged = await parentElement.onDragFinished(draggableDragged, draggableDropped)
                } else {
                    isExchanged = true;
                }
                if (isExchanged) {
                    draggableDragged.innerHTML = this.innerHTML;
                    draggableDropped.innerHTML = transferredData;
                }
                draggableDropped = null;
                draggableDragged = null;
            }
        }

        return false;
    }
}

/**
 * pc용
 * drag over handler
 * - 예제에서 preventDefault 를 추가해 줘야 정상 작동 한다고 하여 추가
 * @param event
 */
function handleDragOver(event) {
    // prevent default to allow drop
    $(event.currentTarget).addClass('active')
    let e = (event.originalEvent || event);
    e.preventDefault();
}

function handleTouchStart(parentElement) {
    return function (event) {
        if (this == event.currentTarget
            && event.target.parentElement.tagName != 'A' //exception
        ) {
            $('.draggable-handle').remove();
            if (draggableDropped) return;
            draggableDragged = this;
            let targetPoint = event.originalEvent.targetTouches[0];
            $draggableHandle = $(this.cloneNode(true));

            let bounds = draggableDragged.getBoundingClientRect();
            let offsetX = targetPoint.clientX - bounds.x;
            let offsetY = targetPoint.clientY - bounds.y;
            $draggableHandle.addClass('draggable-handle')
            $draggableHandle.attr({
                'offset-x': offsetX, 'offset-y': offsetY,
            })
            $draggableHandle.css({
                position: 'fixed',
                width: bounds.width + 'px',
                left: (targetPoint.clientX - offsetX) + 'px',
                top: (targetPoint.clientY - offsetY) + 'px',
                opacity: '0.5'
            })
            $(parentElement).append($draggableHandle)
            let e = (event.originalEvent || event);
            e.preventDefault();
        }
    }
}

function handleTouchMove(event) {
    if (!draggableDragged) return;
    if ($draggableHandle) {
        let targetPoint = event.originalEvent.targetTouches[0];
        $draggableHandle.css({
            left: (targetPoint.clientX - $draggableHandle.attr('offset-x')) + 'px',
            top: (targetPoint.clientY - $draggableHandle.attr('offset-y')) + 'px',
        })
    }
    let e = (event.originalEvent || event);
    e.preventDefault();
    e.stopPropagation();
}

function handleTouchEnd(parentElement) {
    return async function (event) {
        const $draggableItems = $draggableItemMap[parentElement.attr('class') ?? 'default']
        $('.draggable-handle').remove();
        if (draggableDropped) {
            return;
        }
        if ($draggableHandle) $draggableHandle.remove()
        $draggableHandle = null;
        if (!draggableDragged) {
            return;
        }

        function getParentNode(element) {
            let parentNode = element.parentNode;
            if(parentElement.hasClass('mobile'))  {
                parentNode = parentNode.parentNode
            }
            return parentNode;
        }

        let touchPoint = event.originalEvent.changedTouches[0];
        let touchX = touchPoint.clientX;
        let touchY = touchPoint.clientY;
        for (let i = 0; i < $draggableItems.length; ++i) {
            let item = $draggableItems.eq(i);
            if (item.attr('draggable-index') != draggableDragged.getAttribute('draggable-index')) {
                let offset = item.offset();
                offset = {
                    ...offset, right: offset.left + item.width(), bottom: offset.top + item.height(),
                }
                offset.top -= window.scrollY;
                offset.bottom -= window.scrollY;
                offset.right -= window.scrollX;
                offset.left -= window.scrollX;
                if (offset.left < touchX && offset.right > touchX && offset.top < touchY && offset.bottom > touchY) {
                    draggableDropped = item.get(0);

                    if (getParentNode(draggableDragged).className === getParentNode(draggableDropped).className) {
                        // callback (onDragFinished) 가 비동기 작업일 경우
                        // e.dataTransfer.getData 에서 값이 바르게 전달이 안되므로
                        // 이벤트 발생 시 미리 변수에 값을 담아둔다
                        let transferredData = draggableDragged.innerHTML;
                        let isExchanged;

                        if (parentElement.onDragFinished && typeof parentElement.onDragFinished == 'function') {
                            isExchanged = await parentElement.onDragFinished(draggableDragged, draggableDropped)
                        } else {
                            isExchanged = true;
                        }
                        if (isExchanged) {
                            draggableDragged.innerHTML = draggableDropped.innerHTML;
                            draggableDropped.innerHTML = transferredData;
                        }
                        draggableDropped = null;
                        draggableDragged = null;
                        return;
                    }
                }
            }
        }
        draggableDragged = null;
        draggableDropped = null;
    }
}

jQuery.prototype.initDraggable = async function (input) {
    this.onDragFinished = input.onDragFinished;
    // $draggableItemMap[] = []
    let $draggableItems = this.find('.draggable-item');
    for (let i = 0; i < $draggableItems.length; ++i) {
        $draggableItems.eq(i).attr({
            'draggable-index': i,
        });
        $draggableItems.eq(i).parent().unbind('dragstart')
        $draggableItems.eq(i).parent().bind('dragstart', (event) => {
            if (!$(event.target).hasClass('draggable-item')) {
                let e = (event.originalEvent || event);
                e.preventDefault();
            }
        })
    }
    $draggableItems.unbind('dragstart')
    $draggableItems.unbind('dragend')
    $draggableItems.unbind('dragover')
    $draggableItems.unbind('drop')
    $draggableItems.unbind('touchstart')
    $draggableItems.unbind('touchmove')
    $draggableItems.unbind('touchend')
    $draggableItems.bind('dragstart', handleDragStart);
    $draggableItems.bind('dragend', handleDragEnd);
    $draggableItems.bind('dragover', handleDragOver);
    $draggableItems.bind('drop', handleDrop(this));
    $draggableItems.bind('touchstart', handleTouchStart(this));
    $draggableItems.bind('touchmove', handleTouchMove);
    $draggableItems.bind('touchend', handleTouchEnd(this));
    $draggableItems.css({
        'cursor': 'grab',
    })
    $draggableItemMap[this.attr('class') ?? 'default'] = $draggableItems;
}
