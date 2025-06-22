<?php
  require('function.php');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ダンジョンクエスト</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1 class="title">ゲーム「ダンジョンクエスト」</h1>
  <div class="wrap">
    <!-- セッションの中身が無いか確認 -->
    <?php if (empty($_SESSION)) { ?>
      <h2 class="title02">GAME START ?</h2>
      <form class="startForm" method="post">
        <input type="submit" name="start" value="▶︎ゲームスタート！">
      </form>
    <?php } else { ?>
      <h2 class="monsterName"><?php echo $_SESSION['monster']->getName() . 'が現れた!!'; ?></h2>
      <div class="gameArea">
        <div class="imgArea">
          <img class="monsterImg" src="<?php echo $_SESSION['monster']->getImg(); ?>" alt="monster">
        </div>
        <p class="monsterHp">モンスターのHP:<?php echo $_SESSION['monster']->getHp(); ?></p>
        <p>倒したモンスターの数: <?php echo $_SESSION['knockDownCount']; ?></p>
        <p><?php echo $_SESSION['human']->getName(); ?>の残りのHP:<?php echo $_SESSION['human']->getHp(); ?></p>
        <div class="logFlex">
          <div class="log">
            <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
          </div>
          <form method="post" class="command">
            <div class="inputWrap">
              <input type="submit" name="attack" value="▶︎攻撃">
              <input type="submit" name="heal" value="▶︎回復">
            </div>
            <div class="inputWrap">
              <input type="submit" name="magic" value="▶︎魔法">
              <input type="submit" name="escape" value="▶︎逃げる">
            </div>
            <input type="submit" name="restart" value="▶︎ゲームリスタート">
          </form>
        </div>
      </div>
    <?php } ?>

  </div>
  
</body>
</html>