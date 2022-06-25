<?php

namespace Poppy\MgrApp\Classes\Widgets;

use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Form\SettingBase;
use Poppy\MgrApp\Classes\Traits\UseQuery;

/**
 * 设置
 */
final class SettingWidget
{

    use CoreTrait, UseQuery;

    public function resp(string $path)
    {
        $id       = 'poppy.mgr-app.settings';
        $query    = input('_query');
        $service  = $this->coreModule()->services()->get($id);
        $hooks    = sys_hook($id);
        $strForms = $hooks[$path]['forms'] ?? [];
        $forms    = collect();
        collect($strForms)->map(function ($form_class) use ($forms) {
            $form = app($form_class);
            if (!($form instanceof SettingBase)) {
                throw new ApplicationException('设置表单需要继承 `SettingBase` Class');
            }

            $forms->put(md5($form_class), $form);
        });


        if ($this->queryHas($query, 'submit')) {
            $key = input('_key');
            if (!$key) {
                return Resp::error('请传递标识');
            }
            /** @var SettingBase $cur */
            $cur = $forms->offsetGet($key);
            return $cur->resp();
        }

        // 当前的所有表单
        $fms    = collect();
        $models = collect();
        collect($forms)->each(function (SettingBase $form) use ($fms, $models, $query) {
            $form->initWidget();
            $formKey = md5(get_class($form));
            if ($this->queryHas($query, 'data')) {
                $models->put($formKey, $form->model());
            }
            if ($this->queryHas($query, 'frame')) {
                $fms->put($formKey, $form->frame());
            }
        });

        $struct = [];
        if ($this->queryHas($query, 'data')) {
            $struct = array_merge($struct, [
                'models' => $models->toArray(),
            ]);
        }
        if ($this->queryHas($query, 'frame')) {
            $groups = collect();
            collect($hooks)->map(function ($item, $key) use ($groups) {
                $groups->push([
                    'path'  => route_url('py-mgr-app:api.home.setting', [$key], [], false),
                    'title' => $item['title'],
                ]);
            });
            $struct = array_merge($struct, [
                'type'   => 'setting',
                'title'  => $service['title'],
                'path'   => route_url('py-mgr-app:api.home.setting', [$path], [], false),
                'groups' => $groups->toArray(),
                'forms'  => $fms,
            ]);
        }
        return Resp::success(input('_query') ?: '', $struct);
    }
}
