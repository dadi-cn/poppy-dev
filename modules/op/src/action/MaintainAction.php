<?php

namespace Op\Action;

use Mail;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Mail\MaintainMail;
use Throwable;
use Validator;

/**
 * App 版本
 */
class MaintainAction
{
	use AppTrait;
	/**
	 * 发送邮件
	 * @param string $title   标题
	 * @param string $content 邮件主体内容
	 * @param string $mail    邮件接收者
	 * @return bool
	 */
	public function sendMail($mail, $title, $content): bool
	{
		$validator = Validator::make(compact('title', 'content', 'mail'), [
			'title'   => Rule::required(),
			'content' => Rule::required(),
			'mail'    => Rule::required(),
		], [], [
			'title'   => '邮件标题',
			'content' => '邮件内容',
			'mail'    => '接收人',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		try {
			$mails = StrHelper::separate(',', $mail);
			Mail::to($mails)->send(new MaintainMail($title, $content));
			return true;
		} catch (Throwable $e) {
			return $this->setError($e->getMessage());
		}
	}
}