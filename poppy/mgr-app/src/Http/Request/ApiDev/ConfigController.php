<?php

namespace Poppy\MgrApp\Http\Request\ApiDev;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Form\SettingBase;

class ConfigController extends DevelopController
{
    private array $errors = [];

    public function check()
    {
        $hooks = sys_hook('poppy.mgr-app.settings');
        foreach ($hooks as $forms) {
            collect($forms['forms'])->map(function ($form_class) {
                $this->detectForm($form_class);
            });
        }
        if (!count($this->errors)) {
            return Resp::success('检测通过');
        }
        return Resp::error('配置提示', $this->errors);
    }

    private function detectForm($form_class)
    {
        if (!class_exists($form_class)) {
            return;
        }
        /** @var SettingBase $objForm */
        $objForm = new $form_class();
        if (!method_exists($objForm, 'form')) {
            return;
        }
        $objForm->form();
        if (!property_exists($objForm, 'group') && property_exists($objForm, 'fields')) {
            return;
        }
        $group = $objForm->getGroup();
        collect($objForm->items())->each(function ($formField) use ($group) {
            /** @var $formField FormItem */
            if (!$formField->name) {
                return;
            }
            $key = $group . '.' . $formField->name;
            if (in_array('required', $formField->rules, true)) {
                $this->errors[$key] = "设置项 `" . $formField->label . "` ($key) 必须设置";
            }
            elseif (in_array($group, ['py-system::mail', 'py-aliyun-oss::oss'])) {
                $this->errors[$key] = "设置项 `" . $formField->label . "` ($key) 必须设置";
            }
        });
    }
}