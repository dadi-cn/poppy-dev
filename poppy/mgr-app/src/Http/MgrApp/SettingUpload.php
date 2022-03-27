<?php

namespace Poppy\MgrApp\Http\Setting;

use Poppy\Core\Module\Repositories\ModulesPath;
use Poppy\Framework\Validation\Rule;
use function sys_hook;

class SettingUpload extends SettingBase
{

    protected string $title = '上传配置';

    protected string $group = 'py-system::picture';

    /**
     * Build a form here.
     */
    public function form()
    {
        $uploadTypes = sys_hook('poppy.system.upload_type');
        $types       = [];
        foreach ($uploadTypes as $key => $desc) {
            $types[$key] = $desc['title'];
        }
        $this->radio('save_type', '存储位置')->options($types)->rules([
            Rule::string(),
            Rule::required(),
        ])->default('default')->help('选择本地则文件存储在本地');

        foreach ($uploadTypes as $desc) {
            if (isset($desc['path'])) {
                $arrPath = ModulesPath::parse($desc['path']);
                $this->actions('actions', '配置')->page($desc['title'], $arrPath['path'], $arrPath['type']);
            }
        }
    }
}
