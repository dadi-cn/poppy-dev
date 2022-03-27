<?php

namespace Poppy\MgrPage\Classes\Form;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Poppy\Core\Classes\Contracts\SettingContract;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Exceptions\FormException;
use Poppy\System\Models\PamAccount;
use Response;
use function app;

abstract class FormSettingBase extends FormWidget
{
    use KeyParserTrait, PamTrait;

    /**
     * 是否设置用户
     * @var bool
     */
    public $ajax = true;
    /**
     * 是否 Inbox
     * @var bool
     */
    public $inbox = false;
    /**
     * @var PamAccount
     */
    protected $user;
    /**
     * 是否显示标题
     * @var string
     */
    protected $title = '';

    /**
     * 是否包含框架内容
     * @var bool
     */
    protected $withContent = false;

    /**
     * 定义分组
     * @var string
     */
    protected $group = '';

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\Response|JsonResponse|Redirector|RedirectResponse|Resp|Response
     * @throws FormException
     */
    public function handle(Request $request)
    {
        $Setting = app(SettingContract::class);
        $all     = $request->all();
        foreach ($all as $key => $value) {
            if (is_null($value)) {
                $value = '';
            }
            $fullKey = $this->group . '.' . $key;
            $class   = __CLASS__;
            if (!$this->keyParserMatch($fullKey)) {
                throw new FormException("Key {$fullKey} Not Match At Group `{$this->group}` In Class `{$class}`");
            }
            $Setting->set($fullKey, $value);
        }

        return Resp::success('更新配置成功');
    }

    /**
     * @return array
     */
    public function data(): array
    {
        $Setting = app(SettingContract::class);
        $data    = [];
        foreach ($this->fields() as $field) {
            if (Str::startsWith($field->column(), '_')) {
                continue;
            }
            $data[$field->column()] = $Setting->get($this->group . '.' . $field->column());
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
