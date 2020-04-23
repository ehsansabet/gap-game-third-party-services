<?php
/**
 * Callback
 * Author: Ehsan Sabet (ehsan.sabet@hotmail.com)
 */

require dirname( __FILE__ ) . '/vendor/autoload.php';
require_once './configs.php';

use Firebase\JWT\JWT;
use Gap\SDP\Api;

try {
	$gap = new Api( GAP_ROBOT_TOKEN );
} catch ( Exception $e ) {
	throw new \Exception( 'an error was encountered' );
}

$data	= isset( $_POST['data'] ) ? $_POST['data'] : null;
$type	= isset( $_POST['type'] ) ? $_POST['type'] : null;
$chat_id = isset( $_POST['chat_id'] ) ? (int) $_POST['chat_id'] : null;
$from	= isset( $_POST['from'] ) ? $_POST['from'] : null;

$replyKeyboard = $gap->replyKeyboard([
	[
		["/start" => "🔙 بازگشت"]
	]
]);
/******* Help *******/
if($type == 'text' && $data == "💡 راهنما"){
	$helpMessage = "متن راهنما می‌تواند در این قسمت قرار بگیرد.";
	return $gap->sendText($chat_id, $helpMessage, $replyKeyboard);
}
/******* About *******/
if($type == 'text' && $data == "ℹ️ درباره‌ما"){
	$image = './super-mario.jpg';
	$helpMessage = "متن درباره ما در این قسمت قرار می‌گیرد." . PHP_EOL . "شما در این قسمت می‌توانید متن، عکس یا ویدیو قرار دهید و یا حتی بصورت کلی این قسمت را حذف کنید.";
	$inlineKeyboard = [
		[
			["text" => "👨‍💻 کانال من در گپ", "url" => "https://gap.im/sabet"]
		]
	];
	return $gap->sendImage($chat_id, $image, $helpMessage, $replyKeyboard, $inlineKeyboard);
}

$payload = array(
	"cid" => $chat_id,
);

$gameUserToken = JWT::encode($payload, JWT_SECRET_KEY);
$gameUrl = GAME_URL;
$leaderBoardUrl = GAME_LEADERBOARD_URL;

$message = "برای شروع بازی روی یکی از دکمه ها کلیک کنید.";
$inlineKeyboard = [
	[
		[
			'text' => '🏁 شروع بازی', 
			'url' => $gameUrl . '?' . http_build_query(['t' => $gameUserToken]),
			'open_in' => 'webview_with_header'
		],
	],[
		[
			'text' => '🥇  قهرمانان این ماه', 
			'url' => $leaderBoardUrl . '?' . http_build_query(['type' => 'month', 't' => $gameUserToken]),
			'open_in' => 'webview'
		],[
			'text' => '🏆 جدول کلی قهرمانان', 
			'url' => $leaderBoardUrl . '?' . http_build_query(['t' => $gameUserToken]),
			'open_in' => 'webview'
		],
	],[
		[
			'text' => '🎖 قهرمانان امروز', 
			'url' => $leaderBoardUrl . '?' . http_build_query(['type' => 'day', 't' => $gameUserToken]),
			'open_in' => 'webview'
		],[
			'text' => '🏅 قهرمانان این هفته', 
			'url' => $leaderBoardUrl . '?' . http_build_query(['type' => 'week', 't' => $gameUserToken]),
			'open_in' => 'webview'
		],
	],[
		['text' => "🎲 سایر بازی‌ها", 'url' => 'https://gap.im/game'],
		['text' => "👨‍💻 بازی بسازید", 'url' => 'https://gap.im/gamecenter']
	]
];

$replyKeyboard = $gap->replyKeyboard([
	[
		["💡 راهنما" => "💡 راهنما"],
		["ℹ️ درباره‌ما" => "ℹ️ درباره‌ما"]
	]
]);

$gap->sendText($chat_id, $message, $replyKeyboard, $inlineKeyboard);