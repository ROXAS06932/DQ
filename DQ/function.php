<?php



ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

session_start();

// モンスター格納用変数
$monsters = array();

// 性別クラス
class Sex
{
  const MAN = 1;
  const WOMAN = 2;
  const OKAMA = 3;
}

// 抽象クラス（生き物クラス）
abstract class Creature
{
  // プロパティをセット
  protected $name;
  protected $hp;
  protected $attackMin;
  protected $attackMax;

  // 抽象メソッドの定義
  abstract public function sayCry();

  // メソッドの定義
  // 情報取得メソッド
  public function setName($str)
  {
    $this->name = $str;
  }
  public function getName()
  {
    return $this->name;
  }
  public function setHp($num)
  {
    $this->hp = $num;
  }
  public function getHp()
  {
    return $this->hp;
  }

  // 攻撃メソッド
  public function attack($targetObj)
  {
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    // クリティカル処理
    if(!mt_rand(0,9)) {
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName() . 'のクリティカルヒット!!');
    }
    $targetObj->setHp($targetObj->getHp() - $attackPoint);
    History::set($attackPoint . 'ポイントのダメージ!');
  }
}

// 人間クラス生成（サブクラス）
  class Human extends Creature
  {
    // プロパティのセット
    protected $sex;
    protected $healMin;
    protected $healMax;
    protected $magicAttackMin;
    protected $magicAttackMax;
    protected $healCount;
    protected $maxHp;

    // コンストラクタ
    public function __construct($name, $sex, $hp, $attackMin, $attackMax, $healMin, $healMax, $magicAttackMin, $magicAttackMax, $healCount)
    {
      $this->name = $name;
      $this->sex = $sex;
      $this->hp = $hp;
      $this->maxHp = $hp;
      $this->attackMin = $attackMin;
      $this->attackMax = $attackMax;
      $this->healMin = $healMin;
      $this->healMax = $healMax;
      $this->magicAttackMin = $magicAttackMin;
      $this->magicAttackMax = $magicAttackMax;
      $this->healCount = $healCount;
    }
    
    // メソッドの定義
    public function setSex($num)
    {
      $this->sex = $num;
    }
    public function getSex()
    {
      return $this->sex;
    }
    // ダメージリアクション
    public function sayCry()
    {
      History::set($this->name . 'が叫ぶ!');
      // 性別によってリアクションを変える
      switch($this->sex){
        case Sex::MAN:
          History::set('「ぐはっ!!」');
          break;
        case Sex::WOMAN:
          History::set('「きゃっ!!」');
          break;
        case Sex::OKAMA:
          History::set('「もっと!♡」');
          break;
      }
    }

    // コマンド対象切替
    // 回復
    public function heal($targetObj)
{
  $healPoint = mt_rand($this->healMin, $this->healMax);
  if ($this->healCount > 0) {
    $newHp = $targetObj->getHp() + $healPoint;
    if ($newHp > $this->maxHp) {
      $healPoint = $this->maxHp - $targetObj->getHp();
      $newHp = $this->maxHp;
    }
    $targetObj->setHp($newHp);
    History::set($healPoint . 'ポイント授かった!');
    $this->healCount -= 1;
  } else {
    History::set('もう神に祈りは届かない。');
  }
}

    public function magicAttack($targetObj)
    {
      $magicAttackPoint = mt_rand($this->magicAttackMin, $this->magicAttackMax);
      $magicCount = 10;
      $targetObj->setHp($targetObj->getHp() - $magicAttackPoint);
      History::set($magicAttackPoint . 'ポイントのダメージ!');
    }
  }

  // モンスタークラス
  class Monster extends Creature
  {
    // プロパティ
    protected $img;

    // コンストラクタ
    public function __construct($name, $hp, $img, $attackMin, $attackMax)
    {
      $this->name = $name;
      $this->hp = $hp;
      $this->img = $img;
      $this->attackMin = $attackMin;
      $this->attackMax = $attackMax;
    }

    // ゲッター
    public function getImg()
    {
      if(empty($this->img)){
        return 'img/no-img.png';
      }
      return $this->img;
    }
    public function sayCry()
    {
      History::set($this->name);
      if($this->hp <= 0){
        History::set('「グォおおお!!」');
      }else{
        History::set('「ガウッ！！！」');
      }
    }

    public function attack($targetObj){
      parent::attack($targetObj);
    }
  }

  // 魔法を使えるモンスタークラス
  class MagicMonster extends Monster
  {
    // プロパティ
    private $magicAttack;

    // コンストラクタ
    function __construct($name, $hp, $img, $attackMin, $attackMax, $magicAttackMin, $magicAttackMax)
    {
      parent::__construct($name, $hp, $img, $attackMin, $attackMax);
      $this->magicAttackMin = $magicAttackMin;
      $this->magicAttackMax = $magicAttackMax;
    }

    // オーバーライド
    public function attack($targetObj)
    {
      $magicAttackPoint = mt_rand($this->magicAttackMin, $this->magicAttackMax);
      if(!mt_rand(0,4)){
        History::set($this->name . 'が呪文を唱えた!!');
        $targetObj->setHp($targetObj->getHp() - $magicAttackPoint);
        History::set($magicAttackPoint . 'ポイントのダメージを受けた!');
      }else{
        parent::attack($targetObj);
      }
    }
  }

  interface HistoryInterface
  {
    public static function set($str);
    public static function clear();
  }

  // 履歴管理クラス
  class History implements HistoryInterface
  {
    public static function set($str)
    {
      if(empty($_SESSION['history'])) $_SESSION['history'] = '';
      $_SESSION['history'] .= $str . '<br>';
    }
    public static function clear()
    {
      unset($_SESSION['history']);
    }
  }


  // インスタンス生成
  $human[] = new Human('光の戦士　', Sex::MAN, 1000, 40, 150, 30, 100, 70, 150,3);
  $human[] = new Human('女騎士　', Sex::WOMAN, 700, 30, 120, 50, 200, 100, 150,10);
  $human[] = new Human('オカマ　', Sex::OKAMA, 2000, 10, 200, 1, 500, 50, 200,50);
  $monsters[] = new Monster('フランケン　', 100, 'img/monster01.png', 20, 40);
  $monsters[] = new Monster('ドラキュリー　', 200, 'img/monster03.png', 30, 50);
  $monsters[] = new Monster('フランケンNEO　', 500, 'img/monster02.png', 20, 60, 50, 100);
  $monsters[] = new Monster('ドラキュラ男爵　', 400, 'img/monster04.png', 50, 80, 60, 120);
  $monsters[] = new Monster('スカルフェイス　', 150, 'img/monster05.png', 30, 60);
  $monsters[] = new Monster('毒ハンド　', 100, 'img/monster06.png', 10, 30);
  $monsters[] = new Monster('泥ハンド　', 120, 'img/monster07.png', 20, 30);
  $monsters[] = new Monster('血のハンド　', 180, 'img/monster08.png', 30, 50);

  function createMonster()
  {
    global $monsters;
    $monster = $monsters[mt_rand(0,7)];
    History::set($monster->getName() . 'が現れた!');
    $_SESSION['monster'] = $monster;
  }

  function createHuman()
  {
    global $human;
    $human = $human[mt_rand(0,2)];
    $_SESSION['human'] = $human;
  }

  function init()
  {
    History::clear();
    History::set('初期化します!');
    $_SESSION['knockDownCount'] = 0;
    createHuman();
    createMonster();
  }

  function gameOver()
  {
    $_SESSION = array();
  }

  //1.post送信されていた場合、それがアタックなのかスタートなのか、そしてそれらの中に値が入っていれば”true”無ければ”false”を入れる
if (!empty($_POST)) {
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  $healFlg = (!empty($_POST['heal'])) ? true : false;
  $magicFlg = (!empty($_POST['magic'])) ? true : false;
  $escapeFlg = (!empty($_POST['escape'])) ? true : false;
  error_log('POSTされた！');

  //スタートを押した場合
  if ($startFlg) {
    //横のログに文字を入れる
    History::set('ゲームスタート！');
    //そして”init”メソッドで初期化する
    init();
  } else {
    //攻撃するを押した場合
    if ($attackFlg) {
      $_SESSION['history'] = array();
      History::set($_SESSION['human']->getName() . 'の攻撃！');
      //"attack"メソッドの引数に攻撃対象を設定する事で、状態異常などの時に違う処理を書くことが出来るようになる
      $_SESSION['human']->attack($_SESSION['monster']);
      $_SESSION['monster']->sayCry();

      //モンスターが攻撃するメソッドを先に用意したので、"-> + メソッド名（引数）　"と呼び出すだけで勇者に攻撃をする。
      if ($_SESSION['monster']->getHp() > 0) {
        History::set($_SESSION['monster']->getName() . 'の攻撃！');
        //人が叫ぶ（ダメージリアクション）
        $_SESSION['human']->sayCry();

        $_SESSION['monster']->attack($_SESSION['human']);
      }

      //自分のHPが０以下になったらゲームオーバー
      if ($_SESSION['human']->getHp() <= 0) {
        gameOver(); //セッションを空にするメソッド
      } else {
        //モンスターのHPが0以下になったら、別のモンスターを出現させる
        if ($_SESSION['monster']->getHp() <= 0) {
          History::set($_SESSION['monster']->getName() . 'を倒した！');
          createMonster();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount'] + 1;
        }
      }
    } elseif ($healFlg) {

      $_SESSION['history'] = array();
      History::set($_SESSION['human']->getName() . 'が神に祈りを捧げた！');
      $_SESSION['human']->heal($_SESSION['human']);


      if ($_SESSION['monster']->getHp() > 0) {
        History::set($_SESSION['monster']->getName() . 'の攻撃！');
        //人が叫ぶ（ダメージリアクション）
        $_SESSION['human']->sayCry();

        $_SESSION['monster']->attack($_SESSION['human']);
      }

      //自分のHPが０以下になったらゲームオーバー
      if ($_SESSION['human']->getHp() <= 0) {
        gameOver(); //セッションを空にするメソッド
      }
    } elseif ($magicFlg) {
      $_SESSION['history'] = array();
      History::set($_SESSION['human']->getName() . 'の悪魔の囁き');
      $_SESSION['human']->magicAttack($_SESSION['monster']);
      $_SESSION['monster']->sayCry();

      //モンスターが攻撃するメソッドを先に用意したので、"-> + メソッド名（引数）　"と呼び出すだけで勇者に攻撃をする。
      if ($_SESSION['monster']->getHp() > 0) {
        History::set($_SESSION['monster']->getName() . 'の攻撃！');
        //人が叫ぶ（ダメージリアクション）
        $_SESSION['human']->sayCry();

        $_SESSION['monster']->attack($_SESSION['human']);
      }

      //自分のHPが０以下になったらゲームオーバー
      if ($_SESSION['human']->getHp() <= 0) {
        gameOver(); //セッションを空にするメソッド
      } else {
        //モンスターのHPが0以下になったら、別のモンスターを出現させる
        if ($_SESSION['monster']->getHp() <= 0) {
          History::set($_SESSION['monster']->getName() . 'を倒した！');
          createMonster();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount'] + 1;
        }
      }
    } elseif($escapeFlg) { //逃げるを押した場合
      $_SESSION['history'] = array();
      History::set('逃げた！');
      createMonster();
    }else{ //ゲームリスタートを押した場合
      init();
    }
  }
  
}
?>