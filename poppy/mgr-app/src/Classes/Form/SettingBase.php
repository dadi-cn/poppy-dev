<?php

namespace Poppy\MgrApp\Classes\Form;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Core\Classes\Contracts\SettingContract;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Exceptions\FormException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function app;
use function input;
use function is_post;

abstract class SettingBase extends FormWidget
{
    use KeyParserTrait;

    /**
     * 是否显示标题
     * @var string
     */
    protected string $title = '';

    /**
     * 定义分组
     * @var string
     */
    protected string $group = '';

    /**
     * @return ?Response|JsonResponse|RedirectResponse|Resp
     * @throws FormException
     */
    public function handle()
    {
        if (is_post()) {
            $Setting = app(SettingContract::class);
            $all     = input();
            $this->items('model')->each(function (FormItem $field) use ($Setting, $all) {
                $value   = $all[$field->getName()] ?? '';
                $fullKey = $this->group . '.' . $field->getName();
                $class   = __CLASS__;
                if (!$this->keyParserMatch($fullKey)) {
                    throw new FormException("Key {$fullKey} Not Match At Group `{$this->group}` In Class `{$class}`");
                }
                $Setting->set($fullKey, $value);
            });
            return Resp::success('更新配置成功');
        }
        return null;
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function data(): array
    {
        $Setting = app(SettingContract::class);
        $data    = [
            // 分组, 用于获取分组
            '_group' => $this->group
        ];
        foreach ($this->items('model') as $field) {
            $data[$field->getName()] = $Setting->get($this->group . '.' . $field->getName());
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }
}
