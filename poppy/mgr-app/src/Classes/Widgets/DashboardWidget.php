<?php

namespace Poppy\MgrApp\Classes\Widgets;

use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Dashboard\PanelBase;
use Poppy\MgrApp\Classes\Traits\UseQuery;
use Poppy\MgrApp\Classes\Traits\UseScopes;

/**
 * 用于同一个 Scope 下 / 无 Scope 下的面型表单配置
 */
abstract class DashboardWidget
{

    use CoreTrait, UseQuery, UseScopes;


    protected string $title = '单页配置';


    public function __construct()
    {
        $this->scopes = collect();
    }

    abstract public function panels(): array;


    public function resp()
    {
        $query = input('_query');

        if ($this->queryHas($query, 'submit')) {
            $key = input('_key');

            /** @var PanelBase $panel */
            $panel = collect($this->panels())->first(function (PanelBase $form) use ($key) {
                return $form->key() === $key;
            });

            if (is_null($panel)) {
                return Resp::error('错误的仪表盘定义');
            }

            $input = collect(input())->except([
                '_query',
            ])->toArray();
            return $panel->resp($input);
        }

        // 当前的所有表单
        $panels = collect();
        collect($this->panels())->each(function (PanelBase $base) use ($panels, $query) {
            $attr = [
                '_key' => $base->key(),
            ];
            $attr = array_merge($attr, $base->struct($query));
            $panels->push($attr);
        });

        $struct = [
            'type'   => 'dashboard',
            'title'  => $this->title,
            'scope'  => $this->getCurrentScope() ? $this->getCurrentScope()->value : '',
            'scopes' => $this->getScopesStruct(),
            'panels' => $panels->toArray(),
        ];
        return Resp::success(input('_query') ?: '', $struct);
    }
}
