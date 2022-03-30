<?php

namespace Poppy\Ad\Http\Request\MgrApp;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Poppy\Ad\Action\Ad;
use Poppy\Ad\Models\AdContent;
use Poppy\Ad\Models\AdPlace;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrPage\Http\Request\Backend\BackendController;

/**
 * 广告管理
 */
class ContentController extends BackendController
{

    public function __construct()
    {
        parent::__construct();
        self::$permission = [
            'global' => 'backend:py-ad.place.manage',
        ];
    }

    /**
     * 广告列表
     * @return Factory|View
     */
    public function index()
    {
        $place_id = input('place_id');

        $items = AdContent::where('place_id', $place_id)
            ->orderBy('list_order')
            ->paginate($this->pagesize);
        $items->appends(input());

        return view('py-ad::backend.content.index', [
            'items' => $items,
        ]);
    }

    /**
     * 创建/编辑广告
     * @param null $id 广告ID
     * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
     */
    public function establish($id = null)
    {
        $Ad = $this->action();

        $input    = input();
        $place_id = $input['place_id'];
        $place    = AdPlace::find($place_id);
        $info     = '名称：' . $place->title . ' [ 宽度：' . $place->width . 'px , 高度：' . $place->height . 'px ]';

        if (is_post()) {
            if ($Ad->establish($input, $id)) {
                return Resp::success('添加广告成功', '_location|'
                    . route_url(
                        'ad:backend.content.index',
                        ['place_id' => $place_id]
                    ));
            }

            return Resp::error($Ad->getError());
        }

        $id && $Ad->init($id) && $Ad->share();

        return view('py-ad::backend.content.establish', [
            'info'     => $info,
            'place_id' => $place_id,
        ]);
    }

    /**
     * 删除广告
     * @param int $id 广告ID
     * @return JsonResponse|RedirectResponse|Response|Redirector
     */
    public function delete($id)
    {
        $Place = $this->action();
        if ($Place->delete($id)) {
            return Resp::success('删除广告成功', '_reload|1');
        }

        return Resp::error($Place->getError());
    }

    /**
     * 开启/关闭 广告
     * @param int $id 活动ID
     * @return JsonResponse|RedirectResponse|Response|Redirector
     */
    public function toggle($id)
    {
        $Ad = $this->action();
        if ($Ad->toggle($id)) {
            return Resp::success('操作成功', '_reload|1');
        }

        return Resp::error($Ad->getError());
    }

    /**
     * 广告Action
     * @return Ad()
     */
    private function action(): Ad
    {
        return (new Ad())->setPam($this->pam);
    }
}
