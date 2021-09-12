<?php

declare(strict_types = 1);

namespace Poppy\AliyunPush\Classes\Sender;

/**
 * 发送的消息
 */
class PushMessage
{


    const DEVICE_TYPE_ANDROID = 'ANDROID';
    const DEVICE_TYPE_IOS     = 'IOS';

    const PUSH_TYPE_MESSAGE = 'MESSAGE';
    const PUSH_TYPE_NOTICE  = 'NOTICE';

    const TARGET_DEVICE  = 'DEVICE';
    const TARGET_ACCOUNT = 'ACCOUNT';
    const TARGET_ALIAS   = 'ALIAS';
    const TARGET_TAG     = 'TAG';
    const TARGET_ALL     = 'ALL';

    const TARGET_VALUE_ALL     = 'ALL';


    /**
     * 设备类型
     * @var string
     */
    private $deviceType;
    /**
     * @var string 标题
     */
    private $title;
    /**
     * Android推送时通知的内容/消息的内容；iOS消息/通知内容
     * @var string
     */
    private $body;
    /**
     * 推送类型
     * @var string
     */
    private $pushType;
    /**
     * 推送目标
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $extParameters = '';
    /**
     * @var array 附加的推送消息
     */
    private $query = [
        'base'    => [],
        'android' => [],
        'ios'     => [],
    ];
    /**
     * 推送 Target 值
     * @var string
     */
    private $targetValue;

    /**
     * @return string
     */
    public function getDeviceType(): string
    {
        return $this->deviceType;
    }

    /**
     * @param string $deviceType
     */
    public function setDeviceType(string $deviceType): void
    {
        $this->deviceType = $deviceType;
    }

    /**
     * @return string
     */
    public function getExtParameters(): string
    {
        return $this->extParameters;
    }

    /**
     * @param string $extParameters
     */
    public function setExtParameters(string $extParameters): void
    {
        $this->extParameters = $extParameters;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getPushType()
    {
        return $this->pushType;
    }

    /**
     * @param mixed $pushType
     */
    public function setPushType($pushType): self
    {
        $this->pushType = $pushType;
        return $this;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target): self
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetValue()
    {
        return $this->targetValue;
    }

    /**
     * @param mixed $targetValue
     */
    public function setTargetValue($targetValue): self
    {
        $this->targetValue = $targetValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     */
    public function setQuery(array $query): void
    {
        $this->query = $query;
    }


}