<?php
//パスワードを記録したファイルの場所
echo __FILE__;
///Applications/MAMP/htdocs/form/mainte/test.php

echo '<br>';
//実際のパスワード（暗号化をかけとく）
echo(password_hash('pass123',PASSWORD_BCRYPT));
//$2y$10$w/dBDHG7fFDJv/Qak2x0bOaRAJQ5O9i0BSCTF1.ox.ceLXdBOzy7i
