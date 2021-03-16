<?php

namespace Poppy\AliyunPush\Tests\Sample;

use Illuminate\Notifications\Notification;
use Poppy\AliyunPush\Channels\AliPushChannel;
use Poppy\AliyunPush\Contracts\AliPushChannel as AliPushChannelContract;
use Poppy\Framework\Exceptions\FakerException;


class AndroidMessageNotification extends Notification implements AliPushChannelContract
{

    /**
     * Get the notification's delivery channels.
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [AliPushChannel::class];
    }

    /**
     * @return array|mixed
     * @throws FakerException
     */
    public function toAliPush()
    {
        return [
            'broadcast_type'   => 'device',
            'device_type'      => 'android|message',
            'title'            => 'Message.' . py_faker()->sentence,
            'content'          => '{"a":"b"}',
            'registration_ids' => config('poppy.aliyun-push.registration_ids'),
            'extra'            => [
                'key1' => py_faker()->sentence,
                'key2' => py_faker()->words(3, true),
            ],
        ];
    }
}
