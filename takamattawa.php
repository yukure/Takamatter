<?php
$id = $_POST['id']; //ファイル名
$count = $_POST['count']; //投票数
$cookie_name = "takamatta_".$id; //クッキー名（ファイル毎）

// ------------------------------------------------
// クッキーが有効であれば何もしない
// ------------------------------------------------
if( isset($_COOKIE[$cookie_name]) ) {
// "NG"という文字列をJavascript側に返す
echo "NG";

} else {

// DB UPDATE
$dbconn = pg_connect("") or die('Could not connect: ' . pg_last_error());

$sql = "UPDATE takamaru SET takamarubutton=" . $count . " WHERE serialid=" . $id . ";";
$res = pg_query($sql) or die('Query failed: ' . pg_last_error());

setcookie($cookie_name, $count, time()+2); // 2秒有効のクッキーを設定

// "OK"という文字列をJavascript側に返す
echo "OK";
}
?>
