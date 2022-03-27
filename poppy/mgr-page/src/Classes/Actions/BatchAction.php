<?php

namespace Poppy\MgrPage\Classes\Actions;

use Illuminate\Http\Request;
use function __;

abstract class BatchAction extends GridAction
{
    /**
     * @var string
     */
    public $selectorPrefix = '.grid-batch-action-';

    /**
     * @inheritDoc
     */
    public function actionScript()
    {
        $warning = __('No data selected!');

        return <<<SCRIPT
        var key = $.admin.grid.selected();
        
        if (key.length === 0) {
            $.admin.toastr.warning('{$warning}', '', {positionClass: 'toast-top-center'});
            return ;
        }
        
        Object.assign(data, {_key:key});
SCRIPT;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function retrieveModel(Request $request)
    {
        if (!$key = $request->get('_key')) {
            return false;
        }

        $modelClass = str_replace('_', '\\', $request->get('_model'));

        if (is_string($key)) {
            $key = explode(',', $key);
        }

        if ($this->modelUseSoftDeletes($modelClass)) {
            return $modelClass::withTrashed()->findOrFail($key);
        }

        return $modelClass::findOrFail($key);
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->addScript();

        $modalId = '';

        if ($this->interactor instanceof \Poppy\MgrPage\Classes\Actions\Interactor\Form) {
            $modalId = $this->interactor->getModalId();

            if ($content = $this->html()) {
                return $this->interactor->addElementAttr($content, $this->selector);
            }
        }

        return sprintf(
            "<a href='javascript:void(0);' class='%s' %s>%s</a>",
            $this->getElementClass(),
            $modalId ? "modal='{$modalId}'" : '',
            $this->name()
        );
    }
}
