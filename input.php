<?php

// CSRF対策1/7 別のよく似たサイトに飛ばせないようにするために、合言葉をセットして、最後の処理までそれを確認し続ける。最終的に合言葉は削除する。
session_start();

//バリデーション
require 'validation.php';

//クリックジャッキング対策
header('X-FRAME-OPTIONS:DENY');

//XSS対策 echoかけてるものは全部これを適用させている
function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//同じファイル内で画面遷移を可能にするために、条件分岐を利用している。これは条件分岐の核
$pageFlag = 0;

//validation.phpのvalidationメソッドを使用するため、その変数を、改めてこちら側で引数を渡しながら定義する必要がある。
$errors = validation($_POST);

if (!empty($_POST['btn_confirm']) && empty($errors)) {
  $pageFlag = 1;
}
if (!empty($_POST['btn_submit'])) {
  $pageFlag = 2;
}

?>
<!DOCTYPE html>
<html lang="ja">

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <title>Demo</title>
</head>

<body>


  <?php if ($pageFlag === 0) : ?>
    <form method="POST" action="">
      <!-- CSRF対策2 もしTokenを持ってなかったら持たせてあげる -->
      <?php
        if (!isset($_SESSION['csrfToken'])) {
          $csrfToken = bin2hex(random_bytes(24));
          $_SESSION['csrfToken'] = $csrfToken;
        }
        $token = $_SESSION['csrfToken'];
        ?>

      <?php if (!empty($errors) && !empty($_POST['btn_confirm'])) : ?>

        <!-- バリデーション -->
        <!-- 配列である$errorsを一つずつ取り出して、箇条書きで表示させる -->
        <?php echo '<ul>'; ?>
        <?php
            foreach ($errors as $error) {
              echo '<li>' . $error . '<?li>';
            }
            ?>
        <?php echo '</ul>'; ?>
      <?php endif; ?>
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="your_name">氏名</label>
              <!-- valueの値が複雑になっているのは、確認画面から戻ってきたときに値を保持しておくため、POSTで状態を渡したままにしている -->
              <input type="text" class="form-control" id="your_name" name="your_name" value="<?php if (!empty($_POST['your_name'])) {
                                                                                                  echo h($_POST['your_name']);
                                                                                                } ?>" required>
            </div>


            <div class="form-group">
              <label for="email">メールアドレス</label>
              <!-- テストの際には、バックエンドのバリーデーション処理のテストのために、input type＝'text'にしとく。フロント側でバリデーションをかけないでおくとテストがしやすい。テストが終わったら元に戻す -->
              <input type="email" class="form-control" id="email" name="email" value="<?php if (!empty($_POST['email'])) {
                                                                                          echo h($_POST['email']);
                                                                                        } ?>" required>
            </div>

            <div class="form-group">
              <label for="url">ホームページ</label>
              <input type="url" class="form-control" id="url" name="url" value="<?php if (!empty($_POST['url'])) {
                                                                                    echo h($_POST['url']);
                                                                                  } ?>">
            </div>

            <div class="form-check form-check-inline">
              性別
              <input type="radio" class="form-check-input" id="gender1" name="gender" value="1" <?php
                                                                                                  // value='0'にしたらempty関数で弾かれるので、1にずらした
                                                                                                  if (!empty($_POST['gender']) && $_POST['gender'] === '1') {
                                                                                                    echo 'checked';
                                                                                                  } ?>>
              <label class="form-check-label" for="gender1">男性</label>

              <input type="radio" class="form-check-input" id="gender2" name="gender" value="2" <?php if (!empty($_POST['gender']) && $_POST['gender'] === '2') {
                                                                                                    echo 'checked';
                                                                                                  } ?>>
              <label class="form-check-label" for="gender2">女性</label>
            </div>


            <div class="form-group">
              <label for="age">年齢</label>
              <select class="form-control" id="age" name="age">
                <option value="" selected>選択してください</option>
                <option value="1">〜19歳</option>
                <option value="2">20~29歳</option>
                <option value="3">30〜39歳</option>
                <option value="4">40〜49歳</option>
                <option value="5">50〜59歳</option>
                <option value="6">60歳〜</option>
              </select>
            </div>

            <div class="form-group">
              <label for="contact">お問い合わせ内容</label>
              <textarea class="form-control" id="contact" name="contact" rows="3"><?php if (!empty($_POST['contact'])) {
                                                                                      echo h($_POST['contact']);
                                                                                    } ?></textarea>
            </div>

            <div class="form-check">
              <input class="form-check-input" id="caution" type="checkbox" name="caution" value="1">
              <label for="caution">注意事項にチェックする</label>
            </div>

            <input class="btn btn-info" type="submit" name="btn_confirm" value="確認">
            <!-- csrf対策3 最後まで状態を保持し、確認し続けるため-->
            <input type="hidden" name="csrf" value="<?php echo $token; ?>">
    </form>
  <?php endif; ?>


  <?php if ($pageFlag === 1) : ?>
    <?php
      // var_dump($_POST['csrf']);
      // var_dump($_SESSION['csrfToken']);
      ?>
    <!-- csrf対策4 確認-->
    <?php if ($_POST['csrf'] === $_SESSION['csrfToken']) : ?>
      <form method="POST" action="">

        氏名
        <?php echo h($_POST['your_name']); ?>
        <br>
        メールアドレス
        <?php echo h($_POST['email']); ?>
        <br>
        ホームページ
        <?php echo h($_POST['url']); ?>
        <br>
        性別
        <?php
            if ($_POST['gender'] === '1') {
              echo '男性';
            }
            if ($_POST['gender'] === '2') {
              echo '女性';
            }
            ?>
        <br>
        年齢
        <?php
            if ($_POST['age'] === '1') {
              echo '〜19歳';
            }
            if ($_POST['age'] === '2') {
              echo '20〜29歳';
            }
            if ($_POST['age'] === '3') {
              echo '30〜39歳';
            }
            if ($_POST['age'] === '4') {
              echo '40〜49歳';
            }
            if ($_POST['age'] === '5') {
              echo '50〜59歳';
            }
            if ($_POST['age'] === '6') {
              echo '60歳〜';
            }
            ?>
        <br>
        お問い合わせ内容
        <?php echo h($_POST['contact']); ?>
        <br>
        <input type="submit" name="back" value="戻る">
        <input type="submit" name="btn_submit" value="送信">

        <!-- 状態を維持しておくために、同じname属性を持つinputTypeに入れておく -->
        <input type="hidden" name="your_name" value="<?php echo h($_POST['your_name']); ?>">
        <input type="hidden" name="email" value="<?php echo h($_POST['email']); ?>">
        <input type="hidden" name="url" value="<?php echo h($_POST['url']); ?>">
        <input type="hidden" name="gender" value="<?php echo h($_POST['gender']); ?>">
        <input type="hidden" name="age" value="<?php echo h($_POST['age']); ?>">
        <input type="hidden" name="contact" value="<?php echo h($_POST['contact']); ?>">
        <!-- csrf対策5 -->
        <input type="hidden" name="csrf" value="<?php echo $_POST['csrf']; ?>">
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($pageFlag === 2) : ?>
    <!-- csrf対策6 -->
    <?php if ($_POST['csrf'] === $_SESSION['csrfToken']) : ?>
      送信完了
      <!-- csrf対策7 -->
      <?php unset($_SESSION['csrfToken']); ?>
    <?php endif; ?>

    </div>
    </div>
    </div>
    
  <?php endif; ?>


  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</body>

</html>
