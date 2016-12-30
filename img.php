<?php
$id = $_GET['id'];
$dbconn = pg_connect("") or die('Could not connect: ' . pg_last_error());

$sql = 'SELECT img,type FROM takamaruimg WHERE takamaruid=' . $id . ';';
$res = pg_query($sql);
pg_close($dbconn);

if (!empty($res)) {
  $data = pg_fetch_row($res);
  header('Content-Type: image/'.$data[1]);
  $img_data = pg_unescape_bytea($data[0]);
  print"${img_data}";
} else {
  return "Error";
}
?>
