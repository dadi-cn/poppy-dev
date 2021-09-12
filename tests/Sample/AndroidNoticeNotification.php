<?php

namespace Poppy\AliyunPush\Tests\Sample;

use Illuminate\Notifications\Notification;
use Poppy\AliyunPush\Channels\AliPushChannel;
use Poppy\AliyunPush\Contracts\AliPushChannel as AliPushChannelContract;
use Poppy\Framework\Exceptions\FakerException;


class AndroidNoticeNotification extends Notification implements AliPushChannelContract
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
     * @return array
     * @throws FakerException
     */
    public function toAliPush(): array
    {
        return [
            'broadcast_type'   => 'device',
            'device_type'      => 'android|notice;',
            'title'            => 'Notice.' . py_faker()->sentence,
            'content'          => 'Content.' . py_faker()->sentences(3, true),
            'registration_ids' => config('poppy.aliyun-push.registration_ids'),
            'extra'            => [
                'key1' => '',
                'key2' => '',
            ],
        ];
    }
}
