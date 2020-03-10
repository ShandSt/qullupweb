<?php
class Exchange {
	private $nominal = 1;

	function __construct()
	{
		$xmlToday = $this->getDataFromBank(date('d/m/Y'));
		$dataToday = $this->parseExchange($xmlToday);

		if(!empty($_POST['dollars'])) {
			$result = array(
				'type' => 'success',
				'chy' => $_POST['dollars'] * round($dataToday['CNY']/$this->nominal, 2)
			);
		}
		else {
			$result = array(
				'type' => 'false',
			);
		}

		print json_encode($result);
	}

	private function validPost($usd) {
	}

	private function parseExchange($xml) {
		$pattern = "#<Valute ID=\"([^\"]+)[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>([^<]+)#i";
		preg_match_all($pattern, $xml, $out, PREG_SET_ORDER);
		$exchange = array();

		foreach($out as $cur)
		{
			if($cur[2] == 156) {
				$exchange['CNY'] = str_replace(",",".",$cur[4]);

				$nominal = explode("<Nominal>", $cur[0]);
				$nominal = explode("</Nominal>", $nominal[1]);
				$this->nominal = $nominal[0];
			}
		}
		return $exchange;
	}

	private function getDataFromBank($date) {
		$text = @file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp?date_req=$date");
		if ( $text ) {
			$text = mb_convert_encoding($text, 'UTF-8', "Windows-1251");
			return $text;
		} else {
			return false;
		}
	}
}

new Exchange();
