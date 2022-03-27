<?php

namespace Poppy\MgrPage\Classes\Setting;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Form\FormSettingBase;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Throwable;

/**
 * 设置
 */
class SettingView
{
    /**
     * @return string
     */
    public function render(string $path, $index = 0)
    {
        $index = (int) $index;
        try {
            $hooks     = sys_hook('poppy.system.settings');
            $group     = $hooks[$path]['group'] ?? '';
            $groupHook = collect();
            collect($hooks)->each(function ($hook, $key) use ($groupHook, $group) {
                $hooksGroup = $hook['group'] ?? '';
                if ($hooksGroup === $group) {
                    $groupHook->put($key, $hook);
                }
            });
            $forms = collect($groupHook[$path]['forms'])->map(function ($form_class) {
                $form = app($form_class);
                if (!($form instanceof FormSettingBase)) {
                    throw new ApplicationException('设置表单需要继承 `FormSettingBase` Class');
                }
                return $form;
            });
            if (is_post()) {
                /** @var FormSettingBase $cur */
                $cur = $forms->offsetGet($index);
                return $cur->render();
            }

            if (input('_skeleton')) {
                $hk = [];
                collect($groupHook)->each(function ($item, $key) use (&$hk) {
                    $hk[] = [
                        'title' => $item['title'],
                        'key'   => $key,
                        'url'   => route_url('py-mgr-page:backend.home.setting', [$key]),
                    ];
                });
                $fm = [];
                collect($forms)->each(function (FormWidget $form) use (&$fm) {
                    $form->plainSkeleton();
                    $fm[] = $form->render();
                });
                return Resp::success('获取成功', [
                    'type'  => 'setting',
                    'hooks' => $hk,
                    'forms' => $fm,
                ]);
            }

            return view('py-mgr-page::backend.tpl.settings', [
                'hooks' => $groupHook,
                'forms' => $forms,
                'index' => $index,
                'cur'   => $forms->offsetGet($index),
                'path'  => $path,
            ]);
        } catch (Throwable $e) {
            return Resp::error($e->getMessage());
        }
    }
}
