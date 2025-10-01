<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\filters\VerbFilter;
use app\entities\lock\LockId;
use app\repositories\Hydrator;
use yii\filters\AccessControl;
use app\entities\user\ClientId;
use yii\web\NotFoundHttpException;
use app\repositories\NotFoundException;
use app\repositories\lock\LockRepository;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction', // Стандартный обработчик ошибок Yii
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction', // Генерация капчи для форм
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null, // Фиксированный код в тестовом окружении
            ],
        ];
    }

    /**
     * Отображение главной страницы
     *
     * @return string Представление 'index'
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Отображение страницы для добавления замка
     *
     * @return string Представление 'add'
     */
    public function actionAddPage()
    {
        return $this->render('add');
    }

    /**
     * Инициализация замка через репозиторий и сохранение его в базе
     *
     * @return string Представление 'lock-id' с ID замка
     * @throws \Throwable при ошибках инициализации
     */
    public function actionLockInit()
    {
        $repo = new LockRepository(new Hydrator());
        try {
            $lock = $repo->LockInit(); // Инициализация замка через API
            $repo->addLock($lock);     // Сохранение замка в базу
            // Отображение страницы с ID нового замка
            return $this->render('lock-id', ['lockId' => $lock->getLockId()->getValue()]);
        } catch (\Throwable $th) {
            // Перебрасываем исключение дальше
            throw $th;
        }
    }

    /**
     * Получение всех замков для конкретного клиента и отображение их
     *
     * @return string Представление 'get' с отрендеренным списком замков
     */
    public function actionGetPage()
    {
        $repo = new LockRepository(new Hydrator());
        $locks = $repo->getLocksByClientId(new ClientId(clientId)); // Жёстко заданный clientId
        $htmlLocks = '';
        foreach ($locks as $lock) {
            $content = [
                'lockId' => $lock->getLockId()->getValue()
            ];
            // Отрисовка частичного шаблона 'lock' для каждого замка
            $htmlLocks .= $this->renderPartial('lock', $content);
        }
        // Отображение страницы с HTML списком замков
        return $this->render('get', ['htmlLocks' => $htmlLocks]);
    }

    /**
     * Обработка POST запроса для выбора даты по замку
     * и отображение страницы выбора даты
     */
    public function actionGetDate()
    {
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            if (isset($post['lockId'])) {
                $repo = new LockRepository(new Hydrator());
                $lock = $repo->getLockByLockId(new LockId((int)$post['lockId']));
                $content = [
                    'lockId' => $lock->getLockId()->getValue()
                ];
                // Рендер страницы выбора даты для указанного замка
                return $this->render('date', $content);
            }
        }
        // Если пришли данные постом без lockId — вывести отладочную информацию
        var_dump(\Yii::$app->request->post());
        throw new NotFoundHttpException();
    }

    /**
     * Обработка POST запроса для получения кодов доступа к замку по дате
     */
    public function actionGetPassCode()
    {
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            // Проверка наличия необходимых параметров
            if (isset($post['lockId'], $post['start_time'], $post['end_time'])) {
                // Конвертация выбранных дат в миллисекунды
                $startTimestamp = strtotime($post['start_time']);
                $millisecondsStart = $startTimestamp * 1000;
                $endTimestamp = strtotime($post['end_time']);
                $millisecondsEnd = $endTimestamp * 1000;

                // var_dump($millisecondsStart.'     '.$millisecondsEnd);

                $repo = new LockRepository(new Hydrator());
                $lock = $repo->getLockByLockId(new LockId((int)$post['lockId']));
                try {
                    // var_dump($lock->getLockId()->getValue());
                    // Получение кода доступа для замка на указанный период
                    $passCode = $repo->getPassCode($lock->getLockId()->getValue(), $millisecondsStart, $millisecondsEnd);
                } catch (NotFoundException $th) {
                    throw $th;
                }
                // Можно вернуть или отрисовать passCode, сейчас закомментировано
                return $this->render('pass-code', ['passCode' => $passCode->getKeyboardPwd()->getValue()]);
            }
        }
        // Для отладки: вывод POST данных
        // var_dump(\Yii::$app->request->post());
        throw new NotFoundHttpException();
    }

    /**
     * Аутентификация пользователя: вход в систему
     *
     * @return Response|string Перенаправление или рендер формы входа
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome(); // Если уже залогинен — редирект на главную
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(); // При успешном логине возвращаем на предыдущую страницу
        }

        // Очистка пароля перед показом формы
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Выход пользователя из системы
     *
     * @return Response Перенаправление на главную после логаута
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
