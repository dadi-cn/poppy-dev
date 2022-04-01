<?php

namespace Poppy\System\Tests\Configuration;

use Poppy\Framework\Application\TestCase;

class ConfigurationTest extends TestCase
{

    public function testSettings()
    {
        $hooks = sys_hook('poppy.mgr-page.settings');
        foreach ($hooks as $forms) {
            collect($forms['forms'])->map(function ($form_class) {
                $this->detectForm($form_class);
            });
        }
    }

    public function testEmailSettings()
    {
        if (sys_setting('py-system::mail.driver') !== 'smtp') {
            self::assertTrue(true);
        }
        else {
            $form_class = 'Poppy\MgrPage\Http\MgrPage\FormMailStore';
            $this->detectForm($form_class);
        }
    }

    public function testUploadSettings()
    {
        $hooks = sys_hook('poppy.system.upload_type');
        if (sys_setting('py-system::picture.save_type') === 'aliyun') {
            $form_class = $hooks['aliyun']['setting'];
            $this->detectForm($form_class);
        }
        else {
            self::assertTrue(true);
        }
    }

    private function detectForm($form_class)
    {
        if (!class_exists($form_class)) {
            return;
        }
        $objForm = new $form_class();
        if (!method_exists($objForm, 'form')) {
            return;
        }
        $objForm->form();
        if (!property_exists($objForm, 'group') && property_exists($objForm, 'fields')) {
            return;
        }
        $group = $objForm->getGroup();
        collect($objForm->fields())->each(function ($formField) use ($group) {
            if (!$formField->column()){
                return;
            }
            $key = $group . '.' . $formField->column();
            if (in_array('required', $formField->getRules(), true)) {
                $this->assertNotEmpty(sys_setting($key), "设置项" . $formField->label() . " ($key) 必须设置");
            }
            elseif (in_array($group, ['py-system::mail', 'py-aliyun-oss::oss'])) {
                $this->assertNotEmpty(sys_setting($key), "设置项" . $formField->label() . " ($key) 必须设置");
            }
            else {
                $this->assertTrue(true);
            }
        });
    }
}