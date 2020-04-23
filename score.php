<?php
/**
 * Score
 * Author: Ehsan Sabet (ehsan.sabet@hotmail.com)
 */

require dirname( __FILE__ ) . '/vendor/autoload.php';
require_once './configs.php';

use Firebase\JWT\JWT;
use Gap\SDP\Api;

header('Content-type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

$userToken = $_GET['t'] ?? null;
try{
	$data = JWT::decode($userToken, JWT_SECRET_KEY, array('HS256'));
}catch (\Exception $e) {
	header('HTTP/1.1 401 Unauthorized', true, 401);
	echo json_encode([
		'status' => 401,
		'error' => [
			'message' => "توکن ارسالی صحیح نمی‌باشد",
		]
	]);
	return;
}
$chat_id = $data->cid;

try {
	$gap = new Api( GAP_ROBOT_TOKEN );
} catch ( Exception $e ) {
	throw new \Exception( 'an error was encountered' );
}

switch($method){
	case 'POST':{
		$score = (int) $_POST['score'] ?? null;
		if(!$score || !is_numeric($score)){
			header('HTTP/1.1 400 Bad Request', true, 400);
			echo json_encode([
				'status' => 400,
				'error' => [
					'message' => "اطلاعات ارسالی صحیح نمی‌باشد",
				]
			]);
			return;
		}
		$result = $gap->setGameData($chat_id, 'high_score', $score);
		header("HTTP/1.1 200 OK");
		echo json_encode([
			'status' => 200,
			'data' => [
				'score' => $score,
			]
		]);
		return;
	}
	case 'GET':{
		try{
			$result = $gap->getGameData($chat_id, 'high_score');
		}catch ( Exception $e ) {
			header('HTTP/1.1 400 Bad Request', true, 400);
			echo json_encode([
				'status' => 400,
				'error' => [
					'message' => "اطلاعات ارسالی صحیح نمی‌باشد",
				]
			]);
			return;
		}
		$score = $result['value'];
		if(!empty($score) || $score === 0){
			header("HTTP/1.1 200 OK");
			echo json_encode([
				'status' => 200,
				'data' => [
					'score' => $score,
				]
			]);
			return;
		}
		break;
	}
}

header('HTTP/1.1 400 Bad Request', true, 400);
echo json_encode([
	'status' => 400,
	'error' => [
		'message' => "اطلاعات ارسالی صحیح نمی‌باشد",
	]
]);