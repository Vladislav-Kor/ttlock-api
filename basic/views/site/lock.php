<?php

/** @var yii\web\View $this */

$this->title = 'get code';
?>
<p><?php echo $lockId ?></p>
<form method="POST" action="/input-date-for-lock-id">
<input type="hidden" name="lockId" value="<?php echo $lockId ?>" />
<input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
<input type="submit" value="Выбрать">
</form>