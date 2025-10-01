<?php

/** @var yii\web\View $this */

$this->title = 'add code';
?>
<a href="/add" class="">добавить замок</a>
<a href="/get" class="">получить код замка</a>
<p class="">Я создан создовать</p>
<form method="POST" action="/lock-init">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

    <input type="submit" value="добавить">
</form>