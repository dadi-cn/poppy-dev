<?php namespace Poppy\Framework\Application;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Poppy\Framework\Classes\Resp;

abstract class Request extends FormRequest
{
	protected function formatErrors(Validator $validator)
	{
		$error    = [];
		$messages = $validator->getMessageBag();
		foreach ($messages->all('<li>:message</li>') as $message) {
			$error[] = $message;
		}

		return $error;
	}

	public function response(array $errors)
	{
		$error = implode(',', $errors);

		return Resp::error($error, null, $this->request->all());
	}
}
