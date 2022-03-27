<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;

use Illuminate\Support\Str;

/**
 * Class QRCode.
 */
class QRCode extends AbstractDisplayer
{
    public function display($formatter = null, $width = 150, $height = 150)
    {
        $content = $this->getValue();

        if ($formatter instanceof \Closure) {
            $content = call_user_func($formatter, $content, $this->row);
        }

        $img = sprintf(
            "https://api.qrserver.com/v1/create-qr-code/?size=%sx%s&data=%s",
            $width, $height, $content
        );

        $id = 'qr-'.Str::random();
        $dialogWidth = $width + 50;
        $dialogHeight = $width + 120;
        return <<<HTML
<script type="text/tmplate" id="$id">
<div style="text-align:center">
    <img src="$img" style="max-height:{$height}px;max-width:{$width}px;" title="二维码"/>
</div>
</script>
<a href="javascript:void(0);" class="J_dialog" data-element="#$id" data-width="$dialogWidth" data-height="$dialogHeight">
    <i class="fa fa-qrcode"></i>
</a>&nbsp
HTML;
    }
}
