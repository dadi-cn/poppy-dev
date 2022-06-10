<?php

namespace Poppy\Area\Classes\Form\Dependence;

use Poppy\Area\Models\SysArea;
use Poppy\MgrApp\Classes\Form\FormDependence;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;

final class Area extends FormDependence
{

    use UseOptions;

    protected array $type = [
        'select.options',
        'multi-select.options',
    ];

    public function attr(): array
    {
        $type = $this->params['type'] ?? '';
        switch ($type) {
            case 'country':
                $countries = SysArea::kvCountry();
                $this->options($countries);
                break;
            case 'province':
                $provinces = SysArea::kvProvinceId();
                $this->options($provinces);
                break;
            default:
                break;
        }
        return (array) $this->fieldAttr();
    }

    public function field(): array
    {
        return parent::field();
    }


}
