<?php

namespace Poppy\Sms\Http\MgrPage;

use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Form\FormSettingBase;
use function route;
use function sys_hook;

class FormSettingSms extends FormSettingBase
{
    protected $group = 'py-sms::sms';

    protected $withContent = true;

    /**
     * @throws ApplicationException
     */
    public function form()
    {
        $sendTypes = sys_hook('poppy.sms.send_type');

        $types['local'] = '本地';
        foreach ($sendTypes as $key => $desc) {
            $types[$key] = $desc['title'];
        }

        $this->radio('send_type', '发送方式')->options($types)->rules([
            Rule::string(),
            Rule::required(),
        ])->default('local')->help('选择本地则文件存储在日志中, 需要自行查看');
        $this->text('sign', '默认签名')->rules([
            Rule::nullable(),
        ]);

        foreach ($sendTypes as $desc) {
            if (isset($desc['setting'])) {
                $url  = route($desc['route']);
                $link = <<<Link
<a class="J_iframe" href="$url" data-height="600"><i class="fa fa-cogs"></i> {$desc['title']}设置</a>
Link;
                $this->html($link, $desc['title']);
            }
        }
    }
}
