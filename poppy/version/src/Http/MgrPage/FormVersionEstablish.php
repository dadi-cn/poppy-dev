<?php

namespace Poppy\Version\Http\MgrPage;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Models\SysConfig;
use Poppy\Version\Action\Version;
use Poppy\Version\Models\SysAppVersion;
use function input;
use function is_post;
use function sys_setting;

class FormVersionEstablish extends FormWidget
{
    use PamTrait;

    public $ajax = true;

    protected $platform;

    private $id;

    /**
     * @var SysAppVersion
     */
    private $item;

    /**
     * 设置id
     * @param $id
     * @return $this
     * @throws ApplicationException
     */
    public function setId($id)
    {
        $this->id = $id;
        if ($id) {
            $this->item = SysAppVersion::find($id);

            if (!$this->item) {
                throw  new ApplicationException('无版本信息');
            }
        }
        return $this;
    }

    /**
     * @param mixed $platform
     */
    public function setPlatform($platform): FormVersionEstablish
    {
        $this->platform = $platform;

        return $this;
    }

    public function handle()
    {
        $Version = new Version();
        if (is_post()) {
            if (input('is_cover')) {
                $Version->allowCopy();
            }
            if (!$Version->establish(input(), input('id'))) {
                return Resp::error($Version->getError());
            }
            return Resp::success('操作成功', '_top_reload|1');
        }

        $this->id && $Version->init($this->id);
    }

    public function data(): array
    {
        $default = [
            'is_cover' => SysConfig::NO,
        ];
        if ($this->id) {
            return array_merge($default, [
                'id'           => $this->item->id,
                'title'        => $this->item->title,
                'platform'     => $this->item->platform,
                'description'  => $this->item->description,
                'is_upgrade'   => $this->item->is_upgrade,
                'download_url' => $this->item->download_url,
            ]);
        }
        return $default;
    }

    public function form()
    {
        if ($this->id) {
            $this->hidden('id', 'ID');
        }
        $this->hidden('platform', $this->platform)->default($this->platform);
        $this->text('title', '版本号')->rules([
            Rule::required(),
        ]);
        if (sys_setting('py-version::setting.is_upload')) {
            $this->file('download_url', '下载地址')->rules([
                Rule::nullable(),
            ])->file()->exts(['apk', 'ipa'])->help('最大上传文件大小 ' . EnvHelper::maxUploadSize());
        }
        else {
            $this->text('download_url', '下载地址')->rules([
                Rule::required(),
                Rule::url(),
            ]);
        }


        $this->textarea('description', '描述')->rules([
            Rule::required(),
        ]);
        $this->switch('is_upgrade', '是否强制升级');
        $this->switch('is_cover', '覆盖新版文件');
    }
}
