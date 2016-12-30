<?php
$dbconn = pg_connect("") or die('Could not connect: ' . pg_last_error());

//モード確認
//if (isset($_GET["mode"])) {
//    $mode = $_GET["mode"];
//} else {
//    unset($mode);
//}
//if ($mode == "post") {
//    $message = include 'post.php';
//}

if (isset($_POST["mode"])) {
  $mode = $_POST["mode"];
}

if ($mode == "posting") {
  $message = include 'post.php';
}

$shabon_rand = array("bg0", "bg1", "bg2", "bg3");

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="せつめいぶん">
    <meta name="author" content="Ishibashi Seminer Summer 2015 Group 4">

    <title>Takamatter</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">
    <link href="css/bootstrap-lightbox.css" rel="stylesheet">
</head>

<body id="top">

<!-- ローディング -->
<div class="loadingWrap">
	<img src="images/logo.png"><p>Loading...</p>
</div>

<!-- ナビゲーションバー -->
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="takamatter-header">
      <a style="display:block; margin-top:10px; height:70px;" href="javascript:location.reload();"><img src="images/logo.png" style="height:100%; width:auto;"></a>
    </div>
    <ul class="takamatter-menu">
      <li class="write-button">
        <a href="javascript:void(0)" data-toggle="modal" data-target="#takamari-form">
            <img style="height:100%; width:auto;" src="images/pen.png">
        </a>
      </li>
      <li class="refresh-button">
        <a href="javascript:location.reload();">
            <img style="height:100%; width:auto;" src="images/refresh.png">
        </a>
      </li>
    </ul>
  </div>
</nav>

<!-- メイン表示 -->
<div class="main">
<?php echo $message ?>

<?php
$query='SELECT serialid, takamari, userid, gazouarinasi, takamarubutton FROM takamaru ORDER BY random() LIMIT 18;';
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

  while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {

      //各シャボン玉
      echo "<div class=\"col-xs-6 col-sm-4 col-md-3 col-lg-2 square\">\n";
        echo "<div class=\"maru-wrap\" style=\"margin-right:" . mt_rand(0, 25) . "px; margin-top:" . mt_rand(0, 40) . "px;\">";
          echo "<div class=\"maru unique" . $line[serialid] . " " . $shabon_rand[mt_rand(0, 3)] . "\">
                  <p>" . $line[takamari] . "</p>
                  <a href=\"javascript:void(0)\" data-toggle=\"modal\" data-target=\"#takamari" . $line[serialid] . "\"></a>
                </div>";
        echo "</div>
          </div>";

      //各ポップアップ部分
      echo "<div id=\"takamari" . $line[serialid] . "\" class=\"modal fade\">";
          echo "<div class=\"modal-dialog\">
              <div class=\"modal-content\">
                  <div class=\"modal-header\">
                      <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>";
                      echo "<h4 class=\"modal-title\">" . $line[takamari] . "</h4>";
                  echo "</div>
                  <div class=\"modal-body\">";
                      echo "<div class=\"takamari-main\">";
                      if ($line[gazouarinasi] == 't') {
                        echo "<img src=\"img.php?id=" . $line[serialid] . "\">";
                        echo "<span class=\"cnt" . $line[serialid] . "\">" . $line[takamarubutton] . "</span></div>";
                      } else {
                        echo "画像なし";
                        echo "<span class=\"cnt" . $line[serialid] . "\">" . $line[takamarubutton] . "</span></div>";
                      }

                      echo "<p style=\"text-align:right;\">by @" . $line[userid] . "</p>";
                      echo "<p class=\"takamatta-button-wrap\">
                      <a class=\"btn btn-primary takamatta-button\" href=\"javascript:void(0)\" data-dismiss=\"modal\" onclick=\"takamattawa(" . $line["serialid"] . ", 'cnt" . $line["serialid"] . "')\">高まったわああああ！！</a>
                      </p>";
                  echo "</div>";
                  //<div class=\"modal-footer\">";
                  //    echo "<span>by @" . $line[userid] . "</span>";
                  //echo "</div>";
              echo "</div>
          </div>
      </div>";

    }
?>
</div><!-- /main -->

<!-- フッター -->
<!--<footer class="footer">
  <div class="container">
    <p class="text-muted">Copyright (C) 2015 Ishibashi Seminer Group 4. Unauthorized copying prohibited.</p>
  </div>
</footer>-->


<!-- 投稿フォーム -->
<div id="takamari-form" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="./index.php" method="post" enctype="multipart/form-data">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">高まりを投稿する</h4>
        </div>
        <div class="modal-body form-group">
            <p>高まり:<br />
            <input type="text" class="write-textarea form-control" name="takamari"></p>

            <?php
            if (isset($userid)) {
                echo "<input class=\"write-textarea\" type=\"hidden\" name=\"userid\" value=\"" . $userid . "\">";
            } else {
                echo "<p>ユーザーID: <input class=\"write-textarea form-control\" type=\"text\" name=\"userid\"></p>";
            }
            ?>

            <p>画像: <input type="file" name="img" size="30"></p>

            <INPUT TYPE="hidden" NAME="mode" VALUE="posting">

            <?php
            if (isset($userid)) {
                echo "<p><H6>@" . $userid . "としてログインしています。</H6></p><p><H6><a href=\"./index.php?mode=logout\">ログアウト</a></H6></p>";
            } else {
                echo "<p><H6>アカウントをお持ちですか? <a href=\"./index.php?mode=login\">ログイン</a></H6></p>";
            }
            ?>
        </div>
        <div class="modal-footer">
              <input class="btn btn-primary" type="submit" value="投稿">
        </div>
      </form>
    </div>
  </div>
</div><!-- /takamari-form-->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.smoothScroll.js"></script>
<script src="js/jqfloat.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>-->
<script type="text/javascript">
//すべての要素が読み込み終わったら、ローディング画面を非表示にする
  $(window).load(function(){
    $(".loadingWrap").fadeOut();
  });

  $('.maru').jqFloat({
  });

  //いいねボタン
  function takamattawa(id,cls) {
    // 現在表示されている投票数から１カウントアップ
    new_count = Number(document.getElementsByClassName(cls)[0].innerHTML) + 1;
    $.ajax({
      type: 'post',
      url: 'takamattawa.php',
      data: {
        'id': id,
        'count': new_count
      },
      success: function(data){
        // OKが戻ってきたらHTMLにカウントアップした値をセット
        if(data == "OK") {
          document.getElementsByClassName(cls)[0].innerHTML = new_count;
        }
      }
    });

    function deleteAnime() {
      $(".unique"+id).removeClass("anime");
    }

    $(".unique"+id).removeClass("bg0");
    $(".unique"+id).removeClass("bg1");
    $(".unique"+id).removeClass("bg2");
    $(".unique"+id).removeClass("bg3");
    $(".unique"+id).addClass("anime");
    $(".unique"+id).addClass("hide-text");

    window.setTimeout( deleteAnime, 800 );

}
</script>
</body>
</html>
