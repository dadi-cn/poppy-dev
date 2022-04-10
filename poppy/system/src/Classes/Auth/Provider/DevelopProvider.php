<?php

namespace Poppy\System\Classes\Auth\Provider;

use Poppy\System\Models\PamAccount;

/**
 * 开发认证
 */
class DevelopProvider extends PamProvider
{
    /**
     * @inheritDoc
     */
    public function retrieveById($identifier)
    {
        /** @var PamAccount $user */
        $user = $this->createModel()->newQuery()->find($identifier);
        if ($user && $user->type !== PamAccount::TYPE_DEVELOP) {
            return null;
        }
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function retrieveByCredentials(array $credentials)
    {
        $credentials['type'] = PamAccount::TYPE_DEVELOP;

        return parent::retrieveByCredentials($credentials);
    }
}