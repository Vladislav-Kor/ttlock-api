<?php

/** @var yii\web\View $this */

$this->title = 'add code';
?>
<a href="/add" class="">страница создания</a>
<a href="/get" class="">страница получение</a>
<p>Выберите время</p>
<form method="POST" action="/get-pass-code">
    <label for="start_time">Начальное время (в миллисекундах):</label>
    <input type="time" id="start_time" name="start_time" required>
    <br>
    <label for="end_time">Конечное время (в миллисекундах):</label>
    <input type="time" id="end_time" name="end_time" required>
    <br>
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    <input type="hidden" name="lockId" value="<?php echo $lockId ?>"/>

    <input type="submit" value="Отправить">
  </form>