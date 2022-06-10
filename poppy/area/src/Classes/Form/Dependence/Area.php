<?php

namespace Poppy\Area\Classes\Form\Dependence;

use Poppy\Area\Models\SysArea;
use Poppy\MgrApp\Classes\Form\Field\Select;
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
        $select     = new Select('city', '城市选择');
        $provinceId = (int) ($this->values[0] ?? 0);
        if (!$provinceId) {
            $select->placeholder('请选择省份');
        }
        else {
            $cities = SysArea::kvCityId($provinceId);
            $select->options($cities)->placeholder('请选择城市');
        }
        return parent::dropField($select->struct());
    }


}
