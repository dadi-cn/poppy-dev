<?php
declare(strict_types = 1);

namespace Poppy\Area\Commands;

use Illuminate\Console\Command;
use Poppy\Area\Models\PyArea;
use Poppy\Core\Redis\RdsDb;

class InitCommand extends Command
{
    protected $name = 'py-area:init';

    private $rds;

    public function handle()
    {
        $this->rds = new RdsDb();

        $this->initProvince();
        $this->initCity();
        $this->initCounty();

        $this->info('Clear Temp Cache Data ....');
        $this->rds->del([$this->ckProvince(), $this->ckCity()]);
        $this->info('Clear Temp Cache Data Success');
    }

    public function initProvince(): void
    {
        $this->info('Init Province Data ....');
        $path      = poppy_path('poppy.area', 'resources/def/province.json');
        $content   = app('files')->get($path);
        $provinces = json_decode($content, true);

        foreach ($provinces as $pro) {
            if (!PyArea::where('code', $pro['id'])->exists()) {
                PyArea::create([
                    'code'     => $pro['id'],
                    'title'    => $pro['name'],
                    'level'    => PyArea::LEVEL_PROVINCE,
                    'children' => '',
                ]);
            }
        }
        $kv = PyArea::whereRaw('right(code, 10) = "0000000000"')->pluck('id', 'code');
        $this->rds->hMSet($this->ckProvince(), $kv->toArray());
        $this->info('Init Province Data Success');
    }

    public function initCity()
    {
        $this->info('Init City Data ....');
        $path      = poppy_path('poppy.area', 'resources/def/city.json');
        $content   = app('files')->get($path);
        $provinces = json_decode($content, true);
        foreach ($provinces as $province_code => $city) {
            $provinceId = $this->rds->hGet($this->ckProvince(), $province_code);
            $insert     = [];
            foreach ($city as $ci) {
                $insert[] = [
                    'code'      => $ci['id'],
                    'parent_id' => $provinceId,
                    'title'     => $ci['name'],
                    'level'     => PyArea::LEVEL_CITY,
                    'children'  => '',
                ];
            }
            if (PyArea::where('parent_id', $provinceId)->exists()) {
                continue;
            }
            if (count($insert)) {
                PyArea::where('id', $provinceId)->update([
                    'has_child' => 1,
                ]);
                PyArea::insert($insert);
            }
        }
        $kv = PyArea::whereRaw('right(code, 8) = "00000000"')->where('parent_id', '!=', 0)->pluck('id', 'code');
        $this->rds->hMSet($this->ckCity(), $kv->toArray());
        $this->info('Init City Data Success');
    }

    public function initCounty()
    {
        $this->info('Init County Data ....');
        $path    = poppy_path('poppy.area', 'resources/def/county.json');
        $content = app('files')->get($path);
        $cities  = json_decode($content, true);
        foreach ($cities as $city_code => $counties) {
            $cityId = $this->rds->hGet($this->ckCity(), $city_code);
            $insert = [];
            foreach ($counties as $ci) {
                $insert[] = [
                    'code'      => $ci['id'],
                    'parent_id' => $cityId,
                    'title'     => $ci['name'],
                    'level'     => PyArea::LEVEL_COUNTY,
                    'children'  => '',
                ];
            }
            if (PyArea::where('parent_id', $cityId)->exists()) {
                continue;
            }
            if (count($insert)) {
                PyArea::where('id', $cityId)->update([
                    'has_child' => 1,
                ]);
                PyArea::insert($insert);
            }
        }
        $this->info('Init County Data Success');
    }

    private function ckProvince(): string
    {
        return 'py-area:import-province';
    }

    private function ckCity(): string
    {
        return 'py-area:import-city';
    }
}