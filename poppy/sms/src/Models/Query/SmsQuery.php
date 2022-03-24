<?php

namespace Poppy\Sms\Models\Query;

use Illuminate\Support\Collection;
use Poppy\MgrApp\Classes\Grid\Query\QueryCustom;
use Poppy\Sms\Action\Sms;

class SmsQuery extends QueryCustom
{
    private Sms $sms;


    public function __construct()
    {
        $this->sms = new Sms();
    }

    public function get(): Collection
    {
        $templates = $this->sms->getTemplates();
        if ($this->scope) {
            return $templates->where('scope', $this->scope->value)->values();
        }
        return $templates->values();
    }
}