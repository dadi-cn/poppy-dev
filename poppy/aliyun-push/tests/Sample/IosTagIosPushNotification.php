<?php

namespace Poppy\AliyunPush\Tests\Sample;

use Illuminate\Notifications\Notification;
use Poppy\AliyunPush\Channels\AliPushChannel;
use Poppy\AliyunPush\Contracts\AliPushChannel as AliPushChannelContract;
use Poppy\Framework\Exceptions\FakerException;


class IosTagIosPushNotification extends Notification implements AliPushChannelContract
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
            'broadcast_type'    => 'tag',
            'device_type'       => 'ios|notice',
            'title'             => 'Notice[Tag:ios].' . py_faker()->sentence,
            'content'           => 'Content.' . py_faker()->sentences(3, true),
            'registration_tags' => 'ios',
        ];
    }
}
