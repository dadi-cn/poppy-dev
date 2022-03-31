<?php

namespace Poppy\MgrApp\Http\Request\ApiMgrApp;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\System\Models\PamAccount;
use function py_container;

/**
 * 后台初始化控制器
 */
abstract class BackendController extends Controller
{
    use PoppyTrait;

    /**
     * 用户信息
     * @var ?PamAccount
     */
    protected ?PamAccount $pam;

    public function __construct()
    {
        parent::__construct();
        py_container()->setExecutionContext('api');
        $this->middleware(function ($request, $next) {
            $this->pam = $request->user();
            return $next($request);
        });
    }
}