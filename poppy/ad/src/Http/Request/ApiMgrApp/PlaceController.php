<?php

namespace Poppy\Ad\Http\Request\Backend;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Poppy\Ad\Action\Place;
use Poppy\Ad\Models\AdPlace;
use Poppy\Ad\Models\Filters\AdPlaceFilter;
use Poppy\Ad\Models\Policies\AdPlacePolicy;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrPage\Http\Request\Backend\BackendController;

/**
 * 广告位管理
 */
class PlaceController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        self::$permission = AdPlacePolicy::getPermissionMap();
    }

    /**
     * 广告位列表
     * @return Factory|View
     */
    public function index()
    {
        $input = input();

        $items = AdPlace::filter($input, AdPlaceFilter::class)->paginateFilter($this->pagesize);

        return view('py-ad::backend.place.index', [
            'items' => $items,
        ]);
    }

    /**
     * 创建/编辑广告位
     * @param null $id 广告位ID
     * @return Factory|JsonResponse|RedirectResponse|Response|Redirector|View
     */
    public function establish($id = null)
    {
        $Place = $this->action();
        if (is_post()) {
            if ($Place->establish(input(), $id)) {
                return Resp::success('添加广告位成功', 'reload_opener|1');
            }

            return Resp::error($Place->getError());
        }

        $id && $Place->init($id) && $Place->share();

        return view('py-ad::backend.place.establish');
    }

    /**
     * 删除广告位
     * @param int $id 广告位ID
     * @return JsonResponse|RedirectResponse|Response|Redirector
     */
    public function delete($id)
    {
        $Place = $this->action();
        if ($Place->delete($id)) {
            return Resp::success('删除广告位成功', '_reload|1');
        }

        return Resp::error($Place->getError());
    }

    /**
     * 广告位Action
     * @return Place
     */
    private function action(): Place
    {
        return (new Place())->setPam($this->pam);
    }
}
