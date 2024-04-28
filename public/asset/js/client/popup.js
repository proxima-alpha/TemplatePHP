

/**
 * error popup 열기 기능
 * @param className
 * @param response
 * @param status
 * @param requestOrError
 */
async function openPopupErrors(className, response, status, requestOrError) {
    let style = `
        <style>
        .${className} .popup {
            width: 500px;
        }

        .${className} .popup-inner .error-message-wrap {
            padding: 20px 0;
        }

        .${className} .popup-inner .button-wrap {
            margin-top: 20px;
        }

        .${className} .popup-inner .button-wrap .button {
            min-width: 100px;
            padding: 10px 20px;
            margin: 0 10px;
        }
        
        @media (max-width: 840px) {
            .${className} .popup {
                width: calc(100% - 20px);
            }
        }
        </style>`
    let hasMessage = false;
    let html = `
        <div class="error-message-wrap">`
    if (status == 'success' || status >= 200 && status < 300) {
        if (response.messages) {
            for (let key in response.messages) {
                let message = response.messages[key];
                html += `<div>${message}</div>`
                hasMessage = true;
            }
        }
        if (response.message) {
            html += `<div>${response.message}</div>`
            hasMessage = true;
        }
    } else {
        let message = requestOrError;
        try {
            let errorObject = JSON.parse(response.responseText);
            if (errorObject.message) {
                message = errorObject.message
            }
        } catch (e) {
            // do nothing
        }
        if (message) {
            html += `<div>${message}</div>`
            hasMessage = true;
        }
    }
    html += `
        </div>
        <div class="button-wrap controls">
            <a href="javascript:closePopup('${className}')" class="button ok button-fill">OK</a>
        </div>`;
    if (hasMessage) {
        openPopup({
            className: className,
            style: style,
            html: html,
        });
    }
}

async function openImagePopup(id) {
    let className = 'popup-image-detail';
    let style = `
    <style>
    .${className} .popup-inner .image-wrap * {
        width: 100%;
    }
    </style>`
    let html = `<div class="image-wrap">
            <img src='/file/${id}'/>
        </div>`;
    openPopup({
        className: className,
        style: style,
        html: html,
    })
}


async function openVideoPopup(id, type = 'image', mime_type) {
    let className = 'popup-image-detail';
    let style = `
    <style>
    .${className} .popup-inner .image-wrap * {
        width: 100%;
    }
    </style>`
    let html = `<div class="image-wrap">
        <video controls>
            <source src="/file/${id}" type="${mime_type}">
        </video>
        </div>`;
    openPopup({
        className: className,
        style: style,
        html: html,
    })
}

async function openPopupMessage(message) {
    let className = `popup-message`;
    let style = `
        <style>
        .${className} .popup {
            width: 500px;
        }

        .${className} .popup-inner .error-message-wrap {
            padding: 20px 0;
        }

        .${className} .popup-inner .button-wrap {
            margin-top: 20px;
        }

        .${className} .popup-inner .button-wrap .button {
            min-width: 100px;
            padding: 10px 20px;
            margin: 0 10px;
        }
        
        @media (max-width: 840px) {
            .${className} .popup {
                width: calc(100% - 20px);
            }
        }
        </style>`;
    let html = `
    <div class="error-message-wrap">
        <div>${message}</div>
    </div>
    <div class="button-wrap controls">
        <a href="javascript:closePopup('${className}')" class="button cancel button-fill">${lang('confirm')}</a>
    </div>`;
    openPopup({
        className: className,
        style: style,
        html: html,
    })
}
