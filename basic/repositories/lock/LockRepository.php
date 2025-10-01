<?php

/**
 * Репозиторий для работы с замками и кодами доступа через API Sciener.
 * Включает операции добавления, получения и удаления замков и паролей.
 *
 * @author Vladislav
 * @email corchagin.vlad2005@yandex.ru
 * @create date 2023-08-01 09:52:03
 * @modify date 2023-08-01 09:52:03
 */
declare(strict_types=1);

namespace app\repositories\lock;

use Yii;
use app\entities\user\User;
use app\entities\user\ClientId;
use app\dto\lock\LockDto;
use app\entities\lock\LockInit;
use app\entities\passCode\PassCode;
use app\repositories\NotFoundException;
use app\repositories\Hydrator;
use app\entities\passCode\KeyboardPwdId;
use app\entities\passCode\KeyboardPwd;
use app\entities\lock\KeyId;
use app\entities\lock\LockId;
use app\entities\lock\LockData;

class LockRepository
{
    /**
     * @var Hydrator Объект для преобразования данных в сущности
     */
    private $hydrator;

    /**
     * Конструктор класса
     *
     * @param Hydrator $hydrator объект для гидрирования сущностей
     */
    public function __construct(Hydrator $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Добавление записи замка в базу данных
     *
     * @param LockInit $lock сущность с данными замка
     */
    public function addLock(LockInit $lock): void
    {
        Yii::$app->db->createCommand()->insert('lock', [
            'lockId' => $lock->getLockId()->getValue(),
            'keyId' => ($lock->getKeyId() !== null) ? $lock->getKeyId()->getValue() : null,
            'clientId' => $lock->getClientId()->getValue(),
            'lockData' => $lock->getLockData()->getValue(),
        ])->execute();
    }

    /**
     * Получение всех замков по ID клиента из базы
     *
     * @param ClientId $clientId идентификатор клиента
     * @return LockInit[] массив сущностей замков
     */
    public function getLocksByClientId(ClientId $clientId): array
    {
        $result = \Yii::$app->db->createCommand('SELECT * FROM `lock` WHERE `clientId`=:clientId')
            ->bindValue(':clientId', $clientId->getValue())
            ->queryAll();

        $res = [];
        foreach ($result as $user) {
            $dto = new LockDto();
            $dto->load($user);

            // Создаем сущность LockInit из DTO через гидратор
            $res[] = $this->hydrator->hydrate(LockInit::class, [
                'clientId' => new ClientId($dto->clientId),
                'lockData' => new LockData($dto->lockData),
                'lockId' => new LockId($dto->lockId),
                'keyId' => ($dto->keyId !== null) ? new KeyId($dto->keyId) : null,
            ]);
        }

        return $res;
    }

    /**
     * Получение одного замка по его идентификатору
     *
     * @param LockId $lockId идентификатор замка
     * @return LockInit сущность замка
     * @throws NotFoundException если замок не найден
     */
    public function getLockByLockId(LockId $lockId): LockInit
    {
        $result = \Yii::$app->db->createCommand('SELECT * FROM `lock` WHERE `lockId`=:lockId')
            ->bindValue(':lockId', $lockId->getValue())
            ->queryOne();

        if (!$result) {
            throw new NotFoundException("Нет такого замка");
        }

        $dto = new LockDto();
        $dto->load($result);

        // Гидрируем данные в сущность LockInit
        return $this->hydrator->hydrate(LockInit::class, [
            'clientId' => new ClientId($dto->clientId),
            'lockData' => new LockData($dto->lockData),
            'lockId' => new LockId($dto->lockId),
            'keyId' => ($dto->keyId !== null) ? new KeyId($dto->keyId) : null,
        ]);
    }

    /**
     * Инициализация замка через внешний API Sciener
     *
     * @return LockInit сущность инициализированного замка
     * @throws NotFoundException при ошибке API
     */
    public function LockInit()
    {
        $url = 'https://api.sciener.com/v3/lock/initialize';

        // Текущая дата/время в формате миллисекунд
        $date = new \DateTimeImmutable();
        $time = (int)$date->format('Uv');

        // Инициализация cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('ContentType:application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'clientId'    => clientId,      // ID клиента (жестко вшит в код)
            'accessToken' => accessToken,      // Токен доступа (лучше получать параметром)
            'lockData'    => lockData, // Сложные данные о замке
            'date'        => $time,
        ]));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Выполнение запроса и закрытие cURL
        $response = curl_exec($ch);
        curl_close($ch);

        // Декодируем json-ответ
        $response = json_decode($response);

        // Проверка на ошибку в ответе API
        if (isset($response->errcode) && $response->errcode !== 0) {
            throw new NotFoundException("errcode => ".$response->errcode
                ." errmsg => ".$response->errmsg);
        }

        // Преобразование ответа в сущность LockInit
        return $this->hydrator->hydrate(LockInit::class, [
            'clientId' => new ClientId(clientId),
            'lockData' => new LockData(lockData),
            'lockId' => new LockId($response->lockId),
            'keyId' => new KeyId($response->keyId),
        ]);
    }

    /**
     * Получение кодов доступа для замка через API
     *
     * @param string $lockId ID замка
     * @param string $startDate дата начала периода кодов
     * @param string $endDate дата окончания периода кодов
     * @return PassCode сущность с кодом доступа
     * @throws NotFoundException при ошибке API
     */
    public function getPassCode(
        $lockId,
        $startDate,
        $endDate
    ) {
        $url = 'https://api.sciener.com/v3/keyboardPwd/get';

        // Форматируем текущее время в миллисекундах
        $date = new \DateTimeImmutable();
        $time = $date->format('Uv');

        $data = array(
            'clientId'         => clientId,  // ID клиента
            'accessToken'      => accessToken,  // Токен доступа
            'lockId'           => $lockId,
            'keyboardPwdType'  => 3,    // Тип клавиатурного пароля
            'keyboardPwdVersion' => 4,    // Версия пароля
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'date'             => $time,
        );

        // Инициализация и выполнение cURL POST запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        // Проверка на ошибку в ответе
        if (isset($response->errcode) && $response->errcode !== 0) {
            throw new NotFoundException("errcode => ".$response->errcode
                ." errmsg => ".$response->errmsg);
        }

        // Возвращаем сущность PassCode с полученными данными
        return $this->hydrator->hydrate(PassCode::class, [
            'keyboardPwd' => new KeyboardPwd($response->keyboardPwd),
            'keyboardPwdId' => new KeyboardPwdId((int)$response->keyboardPwdId),
            'lockId' => new LockId($lockId),
        ]);
    }

    /**
     * Добавление нового кода доступа через API
     *
     * @param User $user пользователь с токеном и ClientId
     * @param string $lockId ID замка
     * @param string $keyboardPwd новый пароль
     * @param string $date дата запроса
     * @param int $addType тип добавления
     * @param string $startDate начало действия
     * @param string $endDate конец действия
     * @return KeyboardPwdId ID добавленного кода
     * @throws NotFoundException при ошибке API
     */
    public function addPassCode(
        User $user,
        $lockId,
        $keyboardPwd,
        $date,
        $addType,
        $startDate,
        $endDate
    ) {
        $url = 'https://api.sciener.com/v3/keyboardPwd/add';
        $data = array(
            'clientId'    => $user->getClientId()->getValue(),
            'accessToken' => $user->getAccessToken()->getValue(),
            'lockId'      => $lockId,
            'keyboardPwd' => $keyboardPwd,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'addType'     => $addType,
            'date'        => $date,
        );

        // Отправка запроса через cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        // Проверка на ошибку API
        if (isset($response->errcode) && $response->errcode !== 0) {
            throw new NotFoundException("errcode => ".$response->errcode
                ." errmsg => ".$response->errmsg);
        }

        // Возвращаем объект с ID добавленного пароля
        return new KeyboardPwdId($response);
    }

    /**
     * Удаление кода доступа через API
     *
     * @param string $clientId ID клиента
     * @param string $accessToken токен доступа
     * @param string $lockId ID замка
     * @param int $keyboardPwdId ID пароля для удаления
     * @param string $date дата запроса
     * @param int $deleteType тип удаления
     * @throws NotFoundException при ошибке API
     */
    public function deletePassCode(
        $clientId,
        $accessToken,
        $lockId,
        $keyboardPwdId,
        $date,
        $deleteType
    ) {
        // Внимание! URL запроса некорректный, должен быть точка удаления, например '/keyboardPwd/delete'
        $url = 'https://api.sciener.com/v3/keyboardPwd/add';

        $data = array(
            'clientId'      => $clientId,
            'accessToken'   => $accessToken,
            'lockId'        => $lockId,
            'keyboardPwdId' => $keyboardPwdId,
            'deleteType'    => $deleteType,
            'date'          => $date,
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        // Проверка ответа на ошибки
        if (isset($response->errcode) && $response->errcode !== 0) {
            throw new NotFoundException("errcode => ".$response->errcode
                ." errmsg => ".$response->errmsg);
        }
    }

    /**
     * Получение списка замков пользователя через API
     *
     * @param User $user пользователь с clientId и accessToken
     * @return array ответ API с данными замков
     * @throws NotFoundException при ошибке API
     */
    public function LockList(User $user): array
    {
        // Время в миллисекундах для timestamp запроса
        $futureTimestamp = (time() + 1) * 1000;

        $url = 'https://euapi.sciener.com/v3/lock/list';
        $data = array(
            'clientId'    => $user->getClientId()->getValue(),
            'accessToken' => $user->getAccessToken()->getValue(),
            'lockData'    => lockData, // статичные данные о замке
            'date'        => $futureTimestamp,
            'pageNo'      => 1,
            'pageSize'    => 1000,
        );

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        // Проверка на ошибку в ответе
        if (isset($response['errcode']) && $response['errcode'] !== 0) {
            throw new NotFoundException("errcode => ".$response['errcode']
                ." errmsg => ".$response['errmsg']);
        }

        return $response;
    }

    /**
     * Удаление замка через API
     *
     * @param string $clientId ID клиента
     * @param string $accessToken токен доступа
     * @param string $lockId ID замка для удаления
     * @param string $date дата запроса удаления
     * @throws NotFoundException при ошибке API
     */
    public function LockDelete($clientId, $accessToken, $lockId, $date)
    {
        $url = 'https://api.sciener.com/v3/lock/delete';
        $data = array(
            'clientId' => $clientId,
            'accessToken' => $accessToken,
            'lockId' => $lockId,
            'date' => $date,
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);

        if (isset($response['errcode']) && $response['errcode'] !== 0) {
            throw new NotFoundException("errcode => ".$response['errcode']
                ." errmsg => ".$response['errmsg']);
        }
    }
}
