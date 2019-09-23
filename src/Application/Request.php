<?php namespace Poppy\Framework\Application;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Poppy\Framework\Classes\Resp;

/**
 * Request
 */
abstract class Request extends FormRequest
{
	/**
	 * format errors
	 * @param Validator $validator validator
	 * @return array
	 */
	protected function formatErrors(Validator $validator)
	{
		$error    = [];
		$messages = $validator->getMessageBag();
		foreach ($messages->all('<li>:message</li>') as $message) {
			$error[] = $message;
		}

		return $error;
	}

	/**
	 * response
	 * @param array $errors errors
	 * @return array|JsonResponse|RedirectResponse|Response|Redirector
	 */
	public function response(array $errors)
	{
		$error = implode(',', $errors);

		return Resp::error($error, null, $this->request->all());
	}
}