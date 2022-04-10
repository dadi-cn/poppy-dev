<?php

namespace Poppy\System\Classes\Api\Sign;

use Poppy\Framework\Helper\ArrayHelper;

/**
 * 后台用户认证
 */
class DefaultApiSignProvider extends DefaultBaseApiSign
{
    /**
     * @inerhitDoc
     */
    public function sign(array $params, $type = 'user'): string
    {
        $token  = jwt_token();
        $params = $this->except($params);
        ksort($params);
        $kvStr    = ArrayHelper::toKvStr($params);
        $signLong = md5(md5($kvStr) . $token);
        return $signLong[1] . $signLong[3] . $signLong[15] . $signLong[31];
    }
}