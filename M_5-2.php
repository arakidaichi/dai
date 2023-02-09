<?php
    // 4-1 データベースへの接続
    $dsn='データベース名';
    $user='ユーザー名';
    $password="パスワード";
    $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //4-2 CREATE文：データベース内にテーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "created_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);

    $edit_id = "";
    $edit_name = "";
    $edit_comment = "";

  //4-5 INSERT文：データを入力（データレコードの挿入）
  //データベースにテーブルをつくりましたが、まだ何もデータが入っていません。
  //INSERT文 で、データ（レコード）を登録してみましょう。
if(isset($_POST["new_btn"])){
  if (!isset($_POST["edit_id"]) && isset($_POST["name"]) && isset($_POST["comment"])){
    $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, pass) VALUES (:name, :comment, :pass)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass = $_POST["pass"]; 
    $sql -> execute();
  }
}

  //4-8  DELETE文：入力したデータレコードを削除
  //データベースのテーブルに登録したデータレコードは、DELETE文 で削除する事が可能です。
  //ここでは、id の値が 2 の データレコードを削除してみましょう。
if(isset($_POST["del_btn"])){
    $id = $_POST["del_id"];
    $pass = $_POST["pass"];
    $sql = 'SELECT * FROM tbtest WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll(); 
    
    foreach ($results as $row){
        if($row['pass'] == $pass){
            $sql = 'delete from tbtest where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }else{
            echo "パスワードが間違っています。";
        }
    }
}


  //4-7 UPDATE文：入力されているデータレコードの内容を編集
  //bindParamの引数（:nameなど）は4-2でどんな名前のカラムを設定したかで変える必要がある。
if(isset($_POST["edit_btn"])){

    $id = $_POST["edit_id"];
    $sql = 'SELECT * FROM tbtest WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll(); 
  
    foreach ($results as $row){
      $edit_id = $row['id'];
      $edit_name = $row['name'];
      $edit_comment = $row['comment'];
    }
}

//編集！！！！！！！
if(isset($_POST["new_btn"])){
  if (isset($_POST["edit_id"]) && isset($_POST["name"]) && isset($_POST["comment"])){
    $id = $_POST["edit_id"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass = $_POST["pass"]; 
    $sql = 'UPDATE tbtest SET name=:name,comment=:comment,pass=:pass WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
  }
}

//全消去！！！！！
if(isset($_POST["del_all"])){
    $pass = $_POST["pass"];
    if($pass = "delete"){
        $sql = 'DROP TABLE tbtest';
        $stmt = $pdo->query($sql);
    }
}


?>

 <!DOCTYPE html>
<html lang="ja">

<!--3-1-1【フォーム：「名前」「コメント」の入力と「送信」ボタンが1つあるフォームを作成】-->

<head>
    <meta charset="UTF-8">
    <title>mission_5-1-5</title>
</head>
<body style="background-color: mintcream">
<font size="3" face="serif">
<table>
    
<!-------------------->
<form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post"> 
    <tr><td>📋 名前　　　　　</td>        <td><input type="text" name="pre_name" size="30" placeholder="表示名" value="<?= $edit_name ?>" ></td></tr>    
    <tr><td>🖊 コメント　　　</td>    <td><input type="text" name="pre_comment" size="30" placeholder="コメント" value="<?= $edit_comment ?>" ></td>
                                    <input type="hidden" name="pre_edit_id" value="<?= $edit_id ?>">
                                <td><input type="submit" name="pre_new_btn" value="送信する"></td></tr>
</form>

<?php
    if(isset($_POST["pre_new_btn"])){  //投稿ボタンが押されたら
        if($_POST["pre_name"] && $_POST["pre_comment"] && $_POST["pre_edit_id"]){
            $pre_name = $_POST["pre_name"];
            $pre_comment = $_POST["pre_comment"];
            $pre_edit_id = $_POST["pre_edit_id"];
            echo "【名前】"."$pre_name"."　【コメント】"."$pre_comment"."　【編集対象番号】"."$pre_edit_id"."<br>";
            echo "👇パスワードの変更をお願いします！👇";
            ?>
            <form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
                <input type="hidden" name="name" value="<?= $pre_name ?>">
                <input type="hidden" name="comment" value="<?= $pre_comment ?>">
                <input type="hidden" name="edit_id" value="<?= $pre_edit_id ?>">
                <tr><td>🔒 パスワード　</td>    <td><input type="text" name="pass" size="30" placeholder="パスワードを設定してください" value="" ></td>
                    <td><input type="submit" name="new_btn" value="送信する"></td></tr>
            </form>
            <?php
            echo "<br>";
            
        }elseif($_POST["pre_name"] && $_POST["pre_comment"] ){
            $pre_name = $_POST["pre_name"];
            $pre_comment = $_POST["pre_comment"];
            ?>
        <form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
            <input type="hidden" name="name" value="<?= $pre_name ?>">
            <input type="hidden" name="comment" value="<?= $pre_comment ?>">
            <tr><td></td><td><?php echo "【名前】"."$pre_name"."　【コメント】"."$pre_comment";?></td></tr>
            <tr><td></td><td>👇パスワードを入力してください！</td></tr>
            <tr><td>🔒 パスワード　</td>    <td><input type="text" name="pass" size="30" placeholder="パスワードを設定してください" value="" ></td>
                <td><input type="submit" name="new_btn" value="設定完了"></td></tr>
        </form>
        <?php
        }
    }        
?>




<!--消去！！！！！！！！！！！！-->
<form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
    <tr><td>📃 消去　　　　</td>        <td><input type="text" name="pre_del_id" size="30" placeholder="消去対象番号"></td>  
        <td><input type="submit" name="pre_del_btn" value="送信する"></td></tr>
</form>

<?php
    if(isset($_POST["pre_del_btn"])){  //消去ボタンが押されたら
        if($_POST["pre_del_id"] ){
            $pre_del_id = $_POST["pre_del_id"];
            ?>
            <form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
                <input type="hidden" name="del_id" value="<?= $pre_del_id ?>">
                <tr><td></td><td><?php echo "【消去対象番号】"."$pre_del_id";?></td></tr>
                <tr><td></td><td>👇パスワードを入力してください！</td></tr>
                <tr><td>🔒 パスワード　</td>    <td><input type="text" name="pass" size="30" placeholder="パスワードを入力してください" value="" ></td>
                    <td><input type="submit" name="del_btn" value="削除する"></td></tr>
            </form>
            <?php
        }
    }        
?>



<!--「編集」！！！！！！！！！！！！！！！！！！！-->
<form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
    <tr><td>📄 編集　　</td>        <td><input type="text" name="pre_edit_id" size="30" placeholder="編集対象番号" value="<?= $edit_id ?>"></td>
        <td><input type="submit"  name="pre_edit_btn" value="送信する"></td></tr>
</form>

<?php
    if(isset($_POST["pre_edit_btn"])){  //編集ボタンが押されたら
        if($_POST["pre_edit_id"] ){
            $pre_edit_id =  $_POST["pre_edit_id"];
            ?>
            <form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
                <input type="hidden" name="edit_id" value="<?= $pre_edit_id ?>">
                <tr><td></td><td><?php echo "【編集対象番号】"."$pre_edit_id";?></td></tr>
                <tr><td></td><td>👇パスワードを入力してください！</td></tr>
                <tr><td>🔒 パスワード　</td>    <td><input type="text" name="pass" size="30" placeholder="パスワードを入力してください" value="" ></td>
                    <td><input type="submit" name="edit_btn" value="編集する"></td></tr>
            </form>
            <?php
        }
    }        
?>



<!--全消去！！！！！-->
<form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
    <tr><td>🔵 全消去　　　</td>
        <td><input type="submit"  name="pre_del_all" value="全消去"></td></tr>
</form>

<?php
    if(isset($_POST["pre_del_all"])){
        ?>
        <tr><td></td><td>👇パスワードを入力してください！</td></tr>
        <form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
        <tr><td>🔒 パスワード　</td>    <td><input type="text" name="pass" size="30" placeholder="パスワードを入力してください" value="" ></td>
            <td><input type="submit" name="del_all" value="全消去する"></td></tr>
        </form>
        <?php
    }        
?>

</table>
</body>
</html>
<!------------------>

<?php

$sql = "CREATE TABLE IF NOT EXISTS tbtest"
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "created_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,"
. "pass TEXT"
.");";
$stmt = $pdo->query($sql);
    
echo "<hr>";
$sql = 'SELECT * FROM tbtest';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
  foreach ($results as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['created_at'].'<br>';
    echo "<hr>";
  }


?>
  </table>
</body>
</html>


 <!--
 
【参考リンク】
・filter_input関数
https://magazine.techacademy.jp/magazine/46224
・空欄に対する対処
https://detail.chiebukuro.yahoo.co.jp/qa/question_detail/q12253216697

 -->
 