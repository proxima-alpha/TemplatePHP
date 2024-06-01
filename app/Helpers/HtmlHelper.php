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
        return HtmlHelper::setTranslations(HtmlHelper::getDefaultTranslationKeys());
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

    public static function getSingleMediaUploader($key, $file, $view_mode = 'input'): string
    {
        $html = '<div class="uploader media ' . $key . '">';
        if ($view_mode == 'input') {
            if (isset($file)) {
                $html .=
                    '<div class="upload-item"
                         style="background: url(\'' . $file['relative_path'] . '\') no-repeat center;font-size: 0;background-size: cover;">
                        <div class="size-text">' . $file['width'] . 'X' . $file['height'] . '</div>
                        <div class="upload-item-hover">
                            <a href="javascript:deleteUploadedImageFile(\'' . $key . '\',\'' . $file['id'] . '\',\'image/png,image/jpg\')"
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
            if (isset($file)) {
                $html .=
                    '<div class="upload-item button"
                        style="background: url(\'' . $file['relative_path'] . '\') no-repeat center;font-size: 0;background-size: cover;"
                        onclick="openImagePopup(\'' . $file['id'] . '\')">
                        <div class="size-text">' . $file['width'] . 'X' . $file['height'] . '</div>
                        Slider #' . $file['id'] . '
                    </div>';
            } else {
                return '';
            }
        }
        $html .= '</div>';
        return $html;
    }

    public static function getMultiMediaUploader($key, $files, $view_mode = 'view', $accept = null): string
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
                            <div class="size-text">' . $file['width'] . 'X' . $file['height'] . '</div>
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
                            <div class="size-text">' . $file['width'] . 'X' . $file['height'] . '</div>
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
                    <label for="' . $key . '-file" class="button"></label>
                    <input type="file" name="file" id="' . $key . '-file"
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
                            <div class="size-text">' . $file['width'] . 'X' . $file['height'] . '</div>
                            Slider # ' . $file['id'] . ' 
                        </div>';
                    } else {
                        $html .=
                            '<div class="slick-item button">
                            <div class="size-text">' . $file['width'] . 'X' . $file['height'] . '</div>
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

    private static function getSlickHtml($items, $getSlickItemHtml): string
    {
        $html = '
        <div class="content-wrap-inner slider-wrap lines-horizontal">
            <div class="slider-wrap">
                <div class="slick">';
        if (isset($getSlickItemHtml) && is_callable($getSlickItemHtml)) {
            if (isset($items)) {
                foreach ($items as $index => $item) {
                    $html .= $getSlickItemHtml($item);
                }
            }
        }
        $html .= '
                </div>
            </div>
        </div>';
        return $html;
    }

    private static function getProjectItem($item, $image_file_key, $lang,)
    {
        $url = isset($item[$image_file_key]) ? '/file/' . $item[$image_file_key] : '/asset/images/custom/object.svg';
        return '<div class="image-item-wrap">
                    <div class="image-item" style="background: url(\'' . $url . '\') no-repeat center; background-size: cover; font-size: 0;"></div>
                </div>
                <div class="text-item-wrap">
                    <p class="item-title">' . ($lang == 'ko' ? $item['title'] : $item['title_en']) . '</p>
                    <p class="item-date">' . HtmlHelper::toDateString($item['start_date']) . ' ~ ' . HtmlHelper::toDateString($item['end_date']) . '</p>
                    <p class="item-content">' . ($lang == 'ko' ? $item['content'] : $item['content_en']) . '</p>
                </div>';
    }

    public static function getProjectSlick($items, $image_file_key, $lang = 'ko'): string
    {
        return HtmlHelper::getSlickHtml($items, function ($item) use ($lang, $image_file_key) {
            return '<div class="slick-item">' .
                HtmlHelper::getProjectItem($item, $image_file_key, $lang) .
                '</div>';
        });
    }

    public static function getProjectContent($items, $image_file_key, $lang = 'ko'): string
    {
        $html = '<div class="content-wrap-inner">';
        if (isset($items)) {
            foreach ($items as $index => $item) {
                $html .= '<div class="content-item">' .
                    HtmlHelper::getProjectItem($item, $image_file_key, $lang) .
                    '</div>';
            }
        }
        $html .=
            '</div>';
        return $html;
    }

    public static function getPaymentChannel($pg)
    {
        switch ($pg) {
            case 'html5_inicis' :
                return 'inicis';
            default:
                return $pg;
        }
    }
}
