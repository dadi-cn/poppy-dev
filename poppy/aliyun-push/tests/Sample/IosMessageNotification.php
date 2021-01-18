<?php namespace Poppy\AliyunPush\Tests\Sample;

use Illuminate\Notifications\Notification;
use Poppy\AliyunPush\Channels\AliPushChannel;
use Poppy\AliyunPush\Contracts\AliPushChannel as AliPushChannelContract;
use Poppy\Framework\Exceptions\FakerException;


class IosMessageNotification extends Notification implements AliPushChannelContract
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
            'device_type'      => 'ios|message',
            'title'            => 'Message.' . py_faker()->sentence,
            'content'          => 'Content.' . py_faker()->sentences(3, true),
            'registration_ids' => config('poppy.aliyun-push.registration_ids'),
            'extras'           => [
                'key1' => '',
                'key2' => '',
            ],
        ];
    }
}
