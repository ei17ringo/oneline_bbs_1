<?php

  //echo 'POST送信された！';
  //データベースに接続
  // ステップ1.db接続
  $dsn = 'mysql:dbname=oneline_bbs;host=localhost';
    
  // 接続するためのユーザー情報
  $user = 'root';
  $password = '';

  // DB接続オブジェクトを作成
  $dbh = new PDO($dsn,$user,$password);

  // 接続したDBオブジェクトで文字コードutf8を使うように指定
  $dbh->query('SET NAMES utf8');

  //GET送信が行われたら、編集処理を実行
  // $action = $_GET['action'];
  // var_dump($action);
  $editname='';
  $editcomment = '';
  $id = '';
  if (isset($_GET['action']) && ($_GET['action'] == 'edit')){
    //編集したいデータを取得するSQL文を作成（SELECT文）
    $selectsql = 'SELECT * FROM `posts` WHERE `id`='.$_GET['id'];


    //SQL文を実行
    $stmt = $dbh->prepare($selectsql);
    $stmt->execute();

    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

    $editname = $rec['nickname'];
    $editcomment = $rec['comment'];
    $id = $rec['id'];
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'delete')){
    $deletesql = "DELETE FROM `posts` WHERE `id`=".$_GET['id'];
  
    //SQL文を実行
    $stmt = $dbh->prepare($deletesql);
    $stmt->execute();

  }

  //POST送信が行われたら、下記の処理を実行
  //テストコメント
  if(isset($_POST) && !empty($_POST)){

    var_dump($_POST);

    if (isset($_POST['update'])){
      //Update文を実行
      $sql = "UPDATE `posts` SET `nickname`='".$_POST['nickname']."',`comment`='".$_POST['comment'];
      $sql .= "',`created`=now() WHERE `id`=".$_POST['id'];

    }else{
      //Insert文を実行
      //SQL文作成(INSERT文)
      $sql = "INSERT INTO `posts`(`nickname`, `comment`, `created`) ";
      $sql .= " VALUES ('".$_POST['nickname']."','".$_POST['comment']."',now())";

    }

    //var_dump($sql);
    //INSERT文実行
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
  }

  //SQL文作成(SELECT文)
  $sql = 'SELECT * FROM `posts` ORDER BY `created` DESC';
  
  //SQL文実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();

  $posts = array();

  //var_dump($stmt);
  while(1){

    //実行結果として得られたデータを表示
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

    if($rec == false){
      break;
    }

    $posts[]=$rec;
    // echo $rec['id'];
    // echo $rec['nickname'];
    // echo $rec['comment'];
    // echo $rec['created'];


  }
    //データベースから切断
    $dbh=null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">

</head>
<body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#page-top"><span class="strong-title"><i class="fa fa-comment-o"></i> Oneline bbs</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
<!--                   <li class="hidden">
                      <a href="#page-top"></a>
                  </li>
                  <li class="page-scroll">
                      <a href="#portfolio">Portfolio</a>
                  </li>
                  <li class="page-scroll">
                      <a href="#about">About</a>
                  </li>
                  <li class="page-scroll">
                      <a href="#contact">Contact</a>
                  </li> -->
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>
  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
 
    <form action="bbs.php" method="post">
      <div class="form-group">
            <div class="input-group">
              <input type="text" name="nickname" class="form-control"
                       id="validate-text" placeholder="nickname" value="<?php echo $editname; ?>" required>

              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
            
      </div>
      <div class="form-group">
            <div class="input-group" data-validate="length" data-length="4">
              <textarea type="text" class="form-control" name="comment" id="validate-length" placeholder="comment" required><?php echo $editcomment; ?></textarea>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
      </div>

      <?php if ($editname == ''){ ?>
      <button type="submit"　name="insert"  class="btn btn-primary col-xs-12" disabled>つぶやく</button>
      <?php }else{ ?>
      <input type="hidden" name="id" value="<?php echo $id;?>">
      <button type="submit" name="update" class="btn btn-primary col-xs-12" disabled>変更する</button>
      <?php } ?>
    </form>

      </div>

      <div class="col-md-8 content-margin-top">

        <div class="timeline-centered">

        <?php
        foreach ($posts as $post) { ?>

        <article class="timeline-entry">

            <div class="timeline-entry-inner">
                <a href="bbs.php?action=edit&id=<?php echo $post['id'];?>">
                  <div class="timeline-icon bg-success">
                      <i class="entypo-feather"></i>
                      <i class="fa fa-flag"></i>
                  </div>
                </a>
                
                <div class="timeline-label">
                    <h2><a href="#"><?php echo $post['nickname'];?></a> 
                      <?php
                          //一旦日時型に変換
                          $created = strtotime($post['created']);

                          //書式を変換
                          $created = date('Y/m/d',$created);                          
                      ?>

                      <span><?php echo $created;?></span>
                    </h2>
                    <p><?php echo $post['comment'];?></p>
                    <a href="bbs.php?action=delete&id=<?php echo $post['id'];?>"><i class="fa fa-trash fa-lg"></i></a>
                </div>
            </div>

        </article>

        <?php
        }
        ?>
        <article class="timeline-entry begin">

            <div class="timeline-entry-inner">

                <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                    <i class="entypo-flight"></i> +
                </div>

            </div>

        </article>

      </div>

    </div>
  </div>





  
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/form.js"></script>

</body>
</html>



