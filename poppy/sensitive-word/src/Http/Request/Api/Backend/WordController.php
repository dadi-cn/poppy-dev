<?php

namespace Poppy\SensitiveWord\Http\Request\Api\Backend;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\MgrPage\Http\Request\Backend\BackendController;
use Poppy\SensitiveWord\Action\Word;
use Poppy\SensitiveWord\Http\MgrApp\FormSensWordEstablish;
use Poppy\SensitiveWord\Http\MgrApp\GridSensitiveWord;
use Poppy\SensitiveWord\Models\SysSensitiveWord;
use Throwable;

/**
 * 敏感词控制器
 */
class WordController extends BackendController
{
    /**
     * 列表
     * @return Response|JsonResponse|RedirectResponse|Resp
     * @throws ApplicationException
     * @throws Throwable
     */
    public function index()
    {
        $grid = new GridWidget(new SysSensitiveWord());
        $grid->setLists(GridSensitiveWord::class);
        return $grid->resp();
    }

    /**
     * 创建
     * @return Response|JsonResponse|RedirectResponse|Resp
     */
    public function establish()
    {
        $form = new FormSensWordEstablish();
        return $form->resp();
    }

    /**
     * 删除
     * @param $id
     * @return Response|JsonResponse|RedirectResponse|Resp
     * @throws Exception
     */
    public function delete($id = null)
    {
        $Word = $this->action();
        $id   = input('id', (array) $id);
        if (!$id) {
            $id = (array) input('_batch');
        }
        if (!$Word->delete($id)) {
            return Resp::error($Word->getError());
        }
        return Resp::success('删除成功', 'motion|grid:reload');
    }

    /**
     *
     * @return Word
     */
    private function action(): Word
    {
        return (new Word())->setPam($this->pam);
    }
}