<?php
$dbconn = pg_connect("") or die('Could not connect: ' . pg_last_error());

if (isset($_POST["mode"])) {
    if ($_POST["mode"] == "posting") {

        if (strlen($_POST["takamari"])>0) {
          $takamari = $_POST["takamari"];
        } else {
          $takamari = "";
        }

        if (strlen($_POST["userid"])>0) {
            $userid = $_POST["userid"];
        } else {
            $userid = "";
        }

        unset($message);

        if (strlen($takamari)>0 && strlen($userid)>0){
            try {
                if (is_uploaded_file($_FILES['img']['tmp_name'])) { //画像アップロード有無
                    try { //以下画像スパム対策の例外処理
                        if (!isset($_FILES['img']['error']) || !is_int($_FILES['img']['error'])) {
                            throw new RuntimeException('パラメータが不正です');
                        }

                        switch ($_FILES['img']['error']) {
                        case UPLOAD_ERR_OK: // OK
                            break;
                        case UPLOAD_ERR_NO_FILE:   // ファイル未選択
                            throw new RuntimeException('ファイルが選択されていません');
                        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
                        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過
                            throw new RuntimeException('ファイルサイズが大きすぎます');
                        default:
                            throw new RuntimeException('その他のエラーが発生しました');
                        }

                        if ($_FILES['img']['size'] > 3000000) {
                            throw new RuntimeException('ファイルサイズが大きすぎます');
                        }

                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        if (!$ext = array_search(
                            $finfo->file($_FILES['img']['tmp_name']),
                            array(
                            'gif' => 'image/gif',
                            'jpeg' => 'image/jpeg',
                            'png' => 'image/png',
                            ), true)) {
                                throw new RuntimeException('ファイル形式が不正です');
                        }

                        //画像書き込み準備
                        $img_data=$_FILES["img"]['tmp_name'];
                        $data = file_get_contents($img_data);
                        $escaped = pg_escape_bytea($data);
                        //下の$imgsqlは必ずtakamaruにINSERTした後に実行すること
                        $imgsql = "INSERT INTO takamaruimg (takamaruid, img, type) VALUES (currval('takamaru_serialid_seq'), '" . $escaped . "', '" . $ext . "');";
                        $gazouari = 1; //サムネイル有りフラグ

                    } catch (RuntimeException $e) {
                        throw $e;
                    }

                } else {
                    $gazouari = 0; //サムネイル無しフラグ
                }

            $sql = "INSERT INTO takamaru (takamari, userid, gazouarinasi) VALUES ('" . $takamari . "', '" . $userid . "', '" . $gazouari . "');";
            $res = pg_query($sql) or die('Query failed: ' . pg_last_error());
            //画像書き込み
            if ($gazouari == 1) {
                $resimg = pg_query($imgsql) or die('Query failed: ' . pg_last_error());
            }

            $message .= "takamariを投稿しました！<br />";

            } catch (RuntimeException $e) {
                return $e->getMessage(); //ここでキャッチ
            }
        } else {
                $message .= "投稿するtakamariを入力してください。<br />";
        }
    } else {
        $message .= "不正な投稿操作です。<br />";
    }
} else {
    $message .= "不正な投稿操作です。<br />";
}
return $message;

?>
