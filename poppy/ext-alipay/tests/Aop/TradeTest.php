<?php

namespace Poppy\Extension\Alipay\Tests\Aop;

use Poppy\Extension\Alipay\Aop\Request\AlipayTradeAppPayRequest;
use Poppy\Extension\Alipay\Aop\Request\AlipayTradeQueryRequest;
use Poppy\Extension\Alipay\Tests\AlipayBaseTest;
use Poppy\Framework\Helper\UtilHelper;
use Throwable;

class TradeTest extends AlipayBaseTest
{
	public function testAppPay()
	{
		$aop = $this->client();

		$request = new AlipayTradeAppPayRequest();
		$request->setNotifyUrl('http://www.testdomain.com/abc/123');
		$amount = rand(1, 20) + round(0, 99) * 0.01;

		$data = [
			'out_trade_no'    => $this->genNo('SBAppPay'),
			'subject'         => '电竞 App 充值',
			'timeout_express' => '30m',
			'total_amount'    => UtilHelper::formatDecimal($amount),
			'product_code'    => 'QUICK_MSECURITY_PAY',
		];

		$request->setBizContent(json_encode($data));

		$app = $aop->sdkExecute($request);
		$this->assertIsString($app);
	}

	public function testTradeQuery()
	{
		$aop = $this->client();
		// refund in alipay
		$request = new AlipayTradeQueryRequest();
		$data    = [
			'out_trade_no' => 'CHARGE202010130121569668785',       // 商户订单号
		];

		$request->setBizContent(json_encode($data));
		try {
			$result = $aop->execute($request);
			$node   = str_replace('.', '_', $request->getApiMethodName()) . '_response';

			/**
			 * {
			 *     "alipay_trade_query_response":{
			 *         "code":"10000",
			 *         "msg":"Success",
			 *         "buyer_logon_id":"teq***@sandbox.com",
			 *         "buyer_pay_amount":"0.00",
			 *         "buyer_user_id":"2088622954786207",
			 *         "buyer_user_type":"PRIVATE",
			 *         "invoice_amount":"0.00",
			 *         "out_trade_no":"CHARGE202010130121569668785",
			 *         "point_amount":"0.00",
			 *         "receipt_amount":"0.00",
			 *         "send_pay_date":"2020-10-13 01:22:08",
			 *         "total_amount":"1.00",
			 *         "trade_no":"2020101322001486200501132532",
			 *         "trade_status":"TRADE_SUCCESS"
			 *     },
			 *     "alipay_cert_sn":"d614b1b36152c6e6910257680180b4cf",
			 *     "sign":"YTIErxnJkC53wn77GpcN82MiRIc/pEFF/k3XH8GpiNCi7MOEF9IeZKL+KB8REZHz+HittCxYGvWDe9VkvH1uDeDgn7B+7Tmwgh7c7s1E0gb9fPCavCNc/x91rSFDQjfvHsUiixDjrsBtSWYIolnl8peUXu1r/l8OlpjwlYfTnKHgfqly7/7N3rPBpNzeWGqaoGjGYzF9KUeLEwVVB/Z91dywyVsS9uzP6nu29GK1tpSLXtO74mf79fNGYEm0EcNtOhePtEm8fOMWj8uOZEJkITurCySKxjDnY9fectxHQ5frwDVezP3+C4fuwb4ucUJ3EkMuxIWurgDAJsZHpEbC8g=="
			 * }
			 */
			$this->assertEquals('10000', data_get($result, "{$node}.code"));
		} catch (Throwable $e) {
			$this->fail($e->getMessage());
		}


	}
}