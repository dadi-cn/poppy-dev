<?php

namespace Poppy\Sms\Http\Request\Api\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\MgrPage\Http\Request\Backend\BackendController;
use Poppy\Sms\Action\Sms;
use Poppy\Sms\Http\Form\FormSmsEstablish;
use Poppy\Sms\Http\Grid\GridSms;
use Poppy\Sms\Models\Query\SmsQuery;
use Throwable;

/**
 * 短信控制器
 */
class SmsController extends BackendController
{

    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:py-sms.global.manage',
        ];
    }

    /**
     * @return JsonResponse|RedirectResponse|Resp|Response
     * @throws ApplicationException
     * @throws Throwable
     */
    public function index()
    {
        $Sms = new GridWidget(SmsQuery::class);
        $Sms->setLists(GridSms::class);
        return $Sms->resp();
    }


    /**
     * 短信模板c2e
     * @return JsonResponse|RedirectResponse|Response
     */
    public function establish()
    {
        return (new FormSmsEstablish())->resp();
    }

    /**
     * 删除短信模板
     * @param string $id id
     * @return JsonResponse|RedirectResponse|Resp|Response
     */
    public function delete(string $id)
    {
        $Sms = $this->action();
        if (!$Sms->destroy($id)) {
            return Resp::error($Sms->getError());
        }

        return Resp::success('操作成功', 'motion|grid:reload');
    }

    /**
     * @return Sms
     */
    private function action(): Sms
    {
        return new Sms();
    }
}