<?php

namespace App\Helpers;

use mysql_xdevapi\Exception;

final class HtmlHelper
{
    public static function getPagination($pagination, $pagination_link): string
    {
        $data = [];
        if (isset($pagination)) {
            $data['pagination'] = $pagination;
        }
        if (isset($pagination_link)) {
            if (Utils::endsWith($pagination_link, '/')) {
                $pagination_link = substr_replace($pagination_link, '', -1);
            }
            $data['pagination_link'] = $pagination_link;
        }
        return View("pagination", $data);
    }

    public static function getReply($topic_id, $input): string
    {
        $data = [];
        $data['topic_id'] = $topic_id;
        if (isset($input)) {
            $pagination = $input['pagination'];
            if (isset($pagination['page']) &&
                isset($pagination['per-page']) &&
                isset($pagination['total']) &&
                isset($pagination['total-page'])) {
                $data['pagination'] = [
                    'page' => $pagination['page'],
                    'per-page' => $pagination['per-page'],
                    'total' => $pagination['total'],
                    'total-page' => $pagination['total-page'],
                ];
            }
            if (isset($input['array'])) {
                $data['array'] = $input['array'];
            }
        }
        return View("reply", $data);
    }

    public static function covertNewline($string)
    {
        return str_replace("\n", '<br/>', $string);
    }

    public static function showDataEmpty($data, $height = null): bool
    {
        if (!isset($data) || is_array($data) && sizeof($data) == 0) {
            self::showMessage('err_empty_folder', 'No data available.', $height);
            return false;
        }
        return true;
    }

    public static function showMessage($icon, $text, $height = null): void
    {
        $style = '';
        if (isset($height)) {
            $style = ' style="height : ' . $height . 'px; line-height: ' . ($height - 2) . 'px;"';
        }
        echo '<div class="no-data-box"' . $style . '>
                <div class="no-data-wrap">
                    <img src="/asset/images/icon/' . $icon . '.png">
                    <span>' . $text . '</span>
                </div>
            </div>';
    }

    private static function getDefaultTranslationKeys(): array
    {
        return [
            //common
            'confirm', 'cancel', 'edit', 'delete',
            // user popup
            'username', 'type', 'name', 'email',
            // popup control buttons
            'message_popup_delete',
        ];
    }

    public static function setTranslations(array $translations = [], string $setKey = 'Service'): void
    {
        if (sizeof($translations) == 0) return;
        $result = '
        <script type="text/javascript">
        // set common translations
        translations = {
            ...translations,
            ';
        foreach ($translations as $key) {
            $result .= "'" . $key . "' : \"" . lang($setKey . '.' . $key) . "\",\n";
        }
        $result .= '}
        </script>
        ';
        echo $result;
    }

    public static function setTranslationsAdmin()
    {
        $defaultTranslations = HtmlHelper::getDefaultTranslationKeys();
        return HtmlHelper::setTranslations($defaultTranslations);
    }

    public static function setTranslationsClient()
    {
        $defaultTranslations = array_merge(HtmlHelper::getDefaultTranslationKeys(), [
            // login
            'password', 'register', 'password_forget', 'login',
        ]);
        return HtmlHelper::setTranslations($defaultTranslations);
    }

    public static function toDateString($dateString)
    {
        if (!isset($dateString)) return '';
        try {
            $date = strtotime($dateString);
            return date("Y-m-d", $date);
        } catch (Exception $e) {
            return '';
        }
    }

    public static function secToString($sec)
    {
        if (!$sec) return '0:00';
        try {
            $minutes = floor($sec / 60);
            $seconds = $sec % 60;
            return $minutes . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT);
        } catch (Exception $e) {
            return '0:00';
        }
    }

    public static function getPaginationLink($pagination_link, $number, $params = null, $pagination_key = null): string
    {
        if (isset($params) && $pagination_key) {
            $param_string = '';
            $prefix = '';
            foreach ($params as $key => $value) {
                if ($key == $pagination_key) {
                    $param_string .= $prefix . $key . "=" . $number;
                } else {
                    $param_string .= $prefix . $key . "=" . $value;
                }
                $prefix = "&";
            }
            return $pagination_link . "?" . $param_string;
        } else {
            return $pagination_link . "/" . $number;
        }
    }

    public static function getImageUploader($key, $file_id, $view_mode = 'input'): string
    {
        $html = '<div class="uploader image ' . $key . '">';
        if ($view_mode == 'input') {
            if (isset($file_id)) {
                $html .=
                    '<div class="upload-item"
                         style="background: url(\'/file/' . $file_id . '\') no-repeat center;font-size: 0;background-size: cover;">
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedImageFile(\'' . $key . '\',\'' . $file_id . '\',\'image/png,image/jpg\')"
                               class="button delete-image black">
                                <img src="/asset/images/icon/cancel_white.png"/>
                            </a>
                        </div>
                    </div>';
            } else {
                $html .=
                    '<div class="upload-item-add"
                         style="background: url(\'/asset/images/icon/plus_circle_big.png\') no-repeat center; font-size: 0;">
                        <label for="' . $key . '-file" class="button"></label>
                        <input type="file" name="file" id="' . $key . '-file"
                               onchange="onFileUpload(this, \'' . $key . '\');"
                               accept="image/png,image/jpg"/>
                    </div>';
            }
        } else {
            if (isset($file_id)) {
                $html .=
                    '<div class="upload-item button"
                        style="background: url(\'/file/' . $file_id . '\') no-repeat center;font-size: 0;background-size: cover;"
                        onclick="openImagePopup(\'' . $file_id . '\')">
                        Slider #' . $file_id . '
                    </div>';
            } else {
                return '';
            }
        }
        $html .= '</div>';
        return $html;
    }

    public static function getSlickUploader($key, $files, $view_mode = 'view', $accept = null): string
    {
        $html = '';
        if ($view_mode == 'input') {
            $html .=
                '<div class="slider-wrap">
                    <div class="slick uploader ' . $key . '">';
            if (isset($files)) {
                foreach ($files as $index => $file) {
                    if ($file['type'] == 'image') {
                        $html .=
                            '<div class="slick-item draggable-item upload-item" draggable="true"
                            style="background: url(\'' . $file['relative_path'] . '\') no-repeat center; background-size: cover; font-size: 0;">
                            Slider #' . $file['id'] . '
                            <input hidden type="text" name="id" value="' . $file['id'] . '">
                            <div class="upload-item-hover">
                                <a href="javascript:deleteUploadedSlickFile( \'' . $key . '\', ' . $file['id'] . ')"
                                   class="button delete-image black">
                                    <img src="/asset/images/icon/cancel_white.png"/>
                                </a>
                            </div>
                        </div>';
                    } else {
                        $html .=
                            '<div class="slick-item draggable-item upload-item" draggable="true">
                            Slider #' . $file['id'] . '
                            <input hidden type="text" name="id" value="' . $file['id'] . '">
                            <video preload="metadata">
                                <source src="' . $file['relative_path'] . '">
                            </video>
                            <div class="upload-item-hover">
                                <a href="javascript:deleteUploadedSlickFile( \'' . $key . '\', ' . $file['id'] . ')"
                                   class="button delete-image black">
                                    <img src="/asset/images/icon/cancel_white.png"/>
                                </a>
                            </div>
                        </div>';
                    }
                }
            }
            $html .=
                '<div class="slick-item upload-item-add"
                     style="background: url(\'/asset/images/icon/plus_circle_big.png\') no-repeat center; font-size: 0;">
                    <label for="artist_preview-file" class="button"></label>
                    <input type="file" name="file" id="artist_preview-file"
                           onchange="onFileUpload(this, \'' . $key . '\');"
                           accept="' . $accept . '"/>
                </div>';
            $html .=
                '</div>
            </div>';
        } else {
            $html .=
                '<div class="slider-wrap">
                        <div class="slick">';
            if (isset($files)) {
                foreach ($files as $index => $file) {
                    if ($file['type'] == 'image') {
                        $html .=
                            '<div class="slick-item button"
                            style="background: url(\'' . $file['relative_path'] . '\') no-repeat center; background-size: cover; font-size: 0;"
                            onclick="openImagePopup(' . $file['id'] . ')">
                            Slider # ' . $file['id'] . ' 
                        </div>';
                    } else {
                        $html .=
                            '<div class="slick-item button">
                            <video preload="metadata">
                                <source src="' . $file['relative_path'] . '">
                            </video>
                        </div>';
                    }
                }
            }
            $html .=
                '</div>
        </div>';
        }
        return $html;
    }

    public static function getGrpahicSettingSlick($files, $isAdmin = false): string
    {
        $html =
            '<div class="content-wrap-inner slider-wrap ' . ($isAdmin ? 'lines-horizontal' : '') . '">
                    <div class="slick">';
        if (isset($files)) {
            foreach ($files as $index => $file) {
                if ($file['type'] == 'image') {
                    $html .=
                        '<div class="slick-item button"
                        style="background: url(\'' . $file['relative_path'] . '\') no-repeat center; background-size: cover; font-size: 0;"
                        onclick="openImagePopup(' . $file['id'] . ')">
                        Slider # ' . $file['id'] .
                        '</div>';
                } else {
                    $html .=
                        '<div class="slick-item button">
                            <video preload="metadata" muted>
                                <source src="' . $file['relative_path'] . '" >
                            </video>';
                    if (!$isAdmin) {
                        $html .= '<p class="time-string">' . HtmlHelper::secToString($file['time']) . '</p>';
                    }
                    $html .= '</div>';
                }
            }
        }
        $html .=
            '</div>
        </div>';
        return $html;
    }

    public static function getArtistSlick($items, $image_file_key, $lang = 'ko', $isAdmin = false): string
    {
        $html =
            '<div class="content-wrap-inner slider-wrap ' . ($isAdmin ? 'lines-horizontal' : '') . '">
                <div class="slick">';
        if (isset($items)) {
            foreach ($items as $index => $item) {
                $url = isset($item[$image_file_key]) ? '/file/' . $item[$image_file_key] : '/asset/images/custom/object.svg';
                $html .=
                    '<div class="slick-item">
                        <div class="image-item-wrap">
                            <div class="image-item" style="background: url(\'' . $url . '\') no-repeat center; background-size: cover; font-size: 0;"></div>
                        </div>
                        <div class="text-item-wrap">
                            <p class="item-title">' . ($lang == 'ko' ? $item['name'] : $item['name_en']) . '</p>
                            <p class="item-content">' . ($lang == 'ko' ? $item['job'] : $item['job_en']) . '</p>
                        </div>
                    </div>';
            }
        }
        $html .=
            '</div>
        </div>';
        return $html;
    }

    public static function getProjectSlick($items, $image_file_key, $lang = 'ko', $isAdmin = false): string
    {
        $html =
            '<div class="content-wrap-inner slider-wrap ' . ($isAdmin ? 'lines-horizontal' : '') . '">
                <div class="slider-wrap">
                    <div class="slick">';
        if (isset($items)) {
            foreach ($items as $index => $item) {
                $url = isset($item[$image_file_key]) ? '/file/' . $item[$image_file_key] : '/asset/images/custom/object.svg';
                $html .=
                    '<div class="slick-item">';
                if (!$isAdmin) {
                    $html .= '<a href="/project/' . $item['id'] . '/view">';
                }
                $html .= '
                        <div class="image-item-wrap">
                            <div class="image-item" style="background: url(\'' . $url . '\') no-repeat center; background-size: cover; font-size: 0;"></div>
                        </div>
                        <div class="text-item-wrap">
                            <p class="item-title">' . ($lang == 'ko' ? $item['title'] : $item['title_en']) . '</p>
                            <p class="item-date">' . HtmlHelper::toDateString($item['start_date']) . ' ~ ' . HtmlHelper::toDateString($item['end_date']) . '</p>
                            <p class="item-content">' . ($lang == 'ko' ? $item['content'] : $item['content_en']) . '</p>
                        </div>';
                if (!$isAdmin) {
                    $html .= '</a>';
                }
                $html .= '</div>';
            }
        }
        $html .=
            '
            </div>
        </div>
    </div>';
        return $html;
    }

    public static function getProjectItem($items, $image_file_key): string
    {
        $html = '<div class="content-wrap-inner slider-wrap">';
        if (isset($items)) {
            foreach ($items as $index => $item) {
                $url = isset($item[$image_file_key]) ? '/file/' . $item[$image_file_key] : '/asset/images/custom/object.svg';
                $html .=
                    '<div class="content-item">
                        <div class="image-item-wrap">
                            <div class="image-item" style="background: url(\'' . $url . '\') no-repeat center; background-size: cover; font-size: 0;"></div>
                        </div>
                        <div class="text-item-wrap">
                            <p class="item-title">' . $item['title'] . '</p>
                            <p class="item-date">' . HtmlHelper::toDateString($item['start_date']) . ' ~ ' . HtmlHelper::toDateString($item['end_date']) . '</p>
                            <p class="item-content">' . $item['content'] . '</p>
                        </div>
                    </div>';
            }
        }
        $html .=
            '</div>';
        return $html;
    }

    public static function getArtistRow($target, $items, $lang = 'ko', $view_mode = 'input'): string
    {
        ServerLogger::log($lang);
        $html = '';
        $html .=
            '<div class="row-uploader ' . $target . '">';
        if ($view_mode == 'input') {
            foreach ($items as $index => $item) {
                $url = isset($item['profile_id']) ? '/file/' . $item['profile_id'] : '/asset/images/custom/object.svg';
                $html .=
                    '<div class="draggable-item row-uploader-item" draggable="true">
                    <input hidden class="editable" type="text" name="id" value="' . $item['id'] . '">
                    <div class="profile" style=" background: url(\'' . $url . '\'); background-size: cover; font-size: 0;"></div>
                    <div class="info-wrap">
                        <p class="name">' . ($lang == 'ko' ? $item['name'] : $item['name_en']) . '</p>
                        <p>' . ($lang == 'ko' ? $item['job'] : $item['job_en']) . '</p>
                        <p>' . ($lang == 'ko' ? $item['introduction'] : $item['introduction_en']) . '</p>
                    </div>
                    <div class="upload-item-hover">
                        <a href="javascript:deleteUploadedArtistFile( \'' . $target . '\', ' . $item['id'] . ')"
                           class="button delete-image black">
                            <img src="/asset/images/icon/cancel_white.png"/>
                        </a>
                    </div>
                </div>';
            }
        } else {
            foreach ($items as $index => $item) {
                $url = isset($item['profile_id']) ? '/file/' . $item['profile_id'] : '/asset/images/custom/object.svg';
                $html .=
                    '<div class="row-uploader-item">
                    <input hidden type="text" name="id" value="' . $item['id'] . '">
                    <div class="profile" style=" background: url(\'' . $url . '\'); background-size: cover; font-size: 0;"></div>
                    <div class="info-wrap">
                        <p class="name">' . ($lang == 'ko' ? $item['name'] : $item['name_en']) . '</p>
                        <p>' . ($lang == 'ko' ? $item['job'] : $item['job_en']) . '</p>
                        <p>' . ($lang == 'ko' ? $item['introduction'] : $item['introduction_en']) . '</p>
                    </div>
                </div>';
            }
        }
        $html .=
            '</div>';
        return $html;
    }

    public static function getRewardRow($target, $items, $view_mode = 'input'): string
    {
        $html = '<div class="row-uploader ' . $target . '">';
        $option = $view_mode == 'input' ? '' : 'readonly';
        foreach ($items as $index => $item) {
            $html .=
                '<div class="draggable-item row-uploader-item" ' . ($view_mode == 'input' ? 'draggable="true"' : '') . '>';
            if (isset($item['id'])) {
                $html .= '<input hidden class="editable" type="text" name="id" value="' . $item['id'] . '">';
            }
            $html .= '
                <div class="tab-box">
                    <div class="tab-button-wrap">
                        <a class="button ko active" onclick="clickTab(this,\'ko\')">한국어</a>
                        <a class="button en" onclick="clickTab(this,\'en\')">English</a>
                    </div>
                    <div class="tab-wrap ko active">
                        <div class="input-wrap">
                            <p class="input-title">' . lang('Service.title') . '</p>
                            <input type="text" name="title" class="editable under-line" value="' . $item['title'] . '" ' . $option . '/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title">' . lang('Service.content') . '</p>
                            <textarea class="editable" name="content" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)" ' . $option . '>' . $item['content'] . '</textarea>
                        </div>
                    </div>
                    <div class="tab-wrap en">
                        <div class="input-wrap">
                            <p class="input-title">' . lang('Service.title') . '</p>
                            <input type="text" name="title_en" class="editable under-line" value="' . $item['title_en'] . '" ' . $option . '/>
                        </div>
                        <div class="input-wrap">
                            <p class="input-title">' . lang('Service.content') . '</p>
                            <textarea class="editable" name="content_en" onkeydown="resizeInputPopupTextarea(this)"
                                      onkeyup="resizeInputPopupTextarea(this)" ' . $option . '>' . $item['content_en'] . '</textarea>
                        </div>
                    </div>
                </div>
                <div class="line"></div>
                <div class="input-wrap price">
                    <p class="input-title">' . lang('Service.price') . '</p>
                    <input type="number" name="price" class="editable under-line" value="' . $item['price'] . '" ' . $option . '/>
                    <p class="description">KRW</p>
                </div>
                <div class="column">
                    <div class="input-wrap">
                        <p class="input-title">' . lang('Service.stock_count') . '</p>
                        <input type="number" name="total_count" class="editable under-line" value="' . $item['total_count'] . '" ' . $option . '/>
                    </div>
                    <div class="input-wrap">
                        <p class="input-title">' . lang('Service.available_count') . '</p>
                        <input type="number" name="limited_count" class="editable under-line" value="' . $item['limited_count'] . '" ' . $option . '/>
                    </div>
                </div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }
}
