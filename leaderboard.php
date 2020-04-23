<?php
/**
 * Leaderboard
 * Author: Ehsan Sabet (ehsan.sabet@hotmail.com)
 */

require dirname( __FILE__ ) . '/vendor/autoload.php';
require_once './configs.php';

use Firebase\JWT\JWT;
use Gap\SDP\Api;

$leaderboardType =  $_GET['type'] ?? 'all';
$userToken = $_GET['t'] ?? null;
try{
	$data = JWT::decode($userToken, JWT_SECRET_KEY, array('HS256'));
}catch (\Exception $e) {
	header('HTTP/1.1 401 Unauthorized', true, 401);
	return;
}
$chat_id = $data->cid;

try {
	$gap = new Api( GAP_ROBOT_TOKEN );
} catch ( Exception $e ) {
	throw new \Exception( 'an error was encountered' );
}

$table = $gap->leaderBoard($chat_id, $leaderboardType);
$types = [
    'all' => '🏆 جدول کلی قهرمانان',
    'month' => '🥇 جدول قهرمانان این ماه',
    'week' => '🏅 جدول قهرمانان این هفته',
    'day' => '🎖 جدول قهرمانان امروز'
];
$title = $types[$leaderboardType] ?? '';
$game_name = GAME_TITLE;

if(!is_array($table)){
	$topLeaders = [];
}else{
	$topLeaders = $table['topLeaders'];
	if($table['currentPlayer']['rank'] > 100){
		$table['currentPlayer']["current_user"] = true;
		$table['currentPlayer']["nickname"] .= " <span class='your-rank'>رتبه شما: " .$table['currentPlayer']["rank"] . "</span>";
		$table['currentPlayer']["rank"] = '-';
			$topLeaders[] = $table['currentPlayer'];
	}
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	<meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, minimum-scale=1 user-scalable=yes">
    <title>Gap Games</title>
	<style>
	*,article,blockquote,body,dd,div,dl,dt,fieldset,figure,footer,form,h1,h2,h3,h4,h5,h6,head,header,html,input,label,li,ol,p,pre,section,td,th,ul{margin:0;padding:0;font-family:Vazir,sans-serif}body{direction:rtl;text-align:right;background:#ebf5f7;direction:rtl;height:auto;font:500 13px/38px Vazir,sans-serif}table{border-collapse:collapse;border-spacing:0;padding:0;margin:0}fieldset,img{border:0;max-width:100%}address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:400}li,ol,ul{list-style:none}caption,th{text-align:left}q:after,q:before{content:''}*{outline:0}strong{font-weight:700}em{font-style:italic}a img{border:none;max-width:100%}p{direction:rtl}a{text-decoration:none}.clear{clear:both}.right{float:right}.left{float:left}.strong{font-weight:700}article,aside,figure,footer,header,main,nav,section{display:block}.ltr{direction:ltr;text-align:left}body,html{font-family:Vazir,sans-serif;width:100%;direction:rtl;text-align:right;color:#000}#leaderboard{background:#ebf5f7;padding:15px 10px}#leaderboard .title{color:#c15b78;text-align:center;font:bold 33px/50px Vazir,sans-serif;padding:10px 0;margin-bottom:10px}#leaderboard .item{background:#fff;margin-bottom:15px;box-shadow:0 8px 15px 0 rgba(0,0,0,.1);border-radius:10px;padding:15px 100px 15px 20px;-webkit-transition:.25s;-moz-transition:.25s;-o-transition:.25s;transition:.25s;position:relative}#leaderboard .item.active{background:#5bb0ba}#leaderboard .number{width:24px;height:24px;border-radius:20%;position:absolute;top:0;bottom:0;right:15px;margin:auto 0;float:right;text-align:center;color:#fff;font:bold 13px/24px Vazir,sans-serif;background-color:#c15b78}#leaderboard .thumbnail{background:url(thumbnail.jpg) no-repeat center center;background-size:100%;position:absolute;top:0;bottom:0;right:50px;margin:auto 0;width:56px;height:56px;border-radius:50%;overflow:hidden}#leaderboard .thumbnail img{display:block;width:100%}#leaderboard .item.active .name{color:#fff}#leaderboard .name{color:#000;font:bold 14px/20px Vazir,sans-serif}#leaderboard .info{float:right;padding:5px 15px 5px 20px;max-width:90%;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;box-sizing:border-box}#leaderboard .item.active .info{color:#fff}#leaderboard .id{direction:ltr;color:#666;font:12px/20px Vazir,sans-serif}#leaderboard .score{position:absolute;top:0;bottom:0;margin:auto 0;height:30px;font:15px/30px Vazir,sans-serif;color:#c15b78;left:20px;z-index:999;padding-right:5px}#leaderboard .item.active .score{color:#fff}.game-title{text-align:center;color:#870a30;font-size:medium}#leaderboard .your-rank{display:block;font-size:smaller;color:#999}
	</style>
	<link href="https://cdn.rawgit.com/rastikerdar/vazir-font/v24.2.0/dist/font-face.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="leaderboard">
	<div class="title"><?php echo $title; ?><p class="game-title"><?php echo $game_name; ?></p></div>
	<?php foreach ($topLeaders as $item) { ?>
		<div class="item <?php echo isset($item['current_user']) ? 'active' : '';?>">
			<div class="number"><?php echo $item['rank']; ?></div>
			<div class="thumbnail">
				<?php if(!empty($item['avatar']['128'])) { ?>
					<img src="<?php echo $item['avatar']['128']; ?>" >
				<?php } ?>
			</div>
			<div class="info">
				<div class="name"><?php echo $item['nickname']; ?></div>
			</div>
			<div class="score"><?php echo $item['score']; ?></div>
			<div class="clear"></div>
		</div>
	<?php } ?>
</div>
</body>
</html>