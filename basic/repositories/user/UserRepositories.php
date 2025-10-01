<?php

/**
 * Репозиторий пользователя для работы с токенами и данными пользователя.
 *
 * Содержит методы для получения access token от внешнего API и загрузки пользователя по ID из базы данных.
 *
 * @author Vladislav
 * @email corchagin.vlad2005@yandex.ru
 * @create date 2023-08-01 09:52:03
 * @modify date 2023-08-01 09:52:03
 */

declare(strict_types=1);

namespace app\repositories\user;

use app\dto\user\UserDto;
use app\entities\user\Uid;
use app\entities\user\User;
use app\entities\user\Scope;
use app\entities\user\UserId;
use app\repositories\Hydrator;
use app\entities\user\ClientId;
use app\entities\user\UserName;
use app\entities\user\ExpiresIn;
use app\entities\user\AccessToken;
use app\entities\user\clientSecret;
use app\entities\user\RefreshToken;
use app\entities\user\UserPassword;
use app\repositories\NotFoundException;

class TockenUserRepository
{
    /**
     * Экземпляр класса Hydrator для преобразования массива данных в объекты сущностей.
     *
     * @var Hydrator
     */
    private $hydrator;

    /**
     * Конструктор репозитория.
     *
     * @param Hydrator $hydrator Инъекция зависимостей для гидратора сущностей.
     */
    public function __construct(Hydrator $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Получение access token от внешнего API Sciener.
     *
     * Отправляет POST-запрос с параметрами client_id, client_secret, логином и паролем.
     * Возвращает объект с данными ответа в случае успешного запроса.
     * Если сервер вернул ошибку (errcode), выбрасывает исключение NotFoundException.
     *
     * @return object Ответ API с access token и дополнительными данными.
     * @throws NotFoundException В случае ошибки, возвращенной API.
     */
    public function GetAccessToken()
    {
        $url = 'https://api.sciener.com/oauth2/token';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => client_id,
            'client_secret' => client_secret,
            'username' => 'servicebook_admin',
            'password' => password,
        ]));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Выполняем запрос и декодируем JSON-ответ
        $response = json_decode(curl_exec($ch));

        curl_close($ch);

        // Проверяем, содержит ли ответ API ошибку
        if (isset($response['errcode'])) {
            throw new NotFoundException(
                "errcode => " . $response['errcode']
                    . " errmsg => " . $response['errmsg']
            );
        }

        // Возвращаем успешный ответ
        return $response;
    }

    /**
     * Поиск и получение пользователя по его ID из базы данных.
     *
     * Выполняет SQL-запрос, создает DTO и преобразует его в объект User через гидратор.
     * Если пользователь не найден, выбрасывает исключение NotFoundException.
     *
     * @param mixed $id ID пользователя.
     * @return User Объект пользователя.
     * @throws NotFoundException Если пользователь с таким ID не существует.
     */
    public function getById($id)
    {
        // Выполняем SQL-запрос для поиска пользователя по ID
        $result = \Yii::$app->db->createCommand('SELECT * FROM `users` WHERE `id`=:id')
            ->bindValue(':id', $id)
            ->queryOne();

        // Если пользователь не найден, выбрасываем исключение
        if (!$result) {
            throw new NotFoundException('Пользователь не найден');
        }

        // Заполняем DTO данными пользователя из БД
        $dto = new UserDto();
        $dto->load($result);

        // Создаем объект User и гидрируем его значениями из DTO
        return $this->hydrator->hydrate(User::class, [
            'id' => new UserId($dto->id),
            'access_token' => new AccessToken($dto->access_token),
            'uid' => new Uid($dto->uid),
            'expires_in' => new ExpiresIn($dto->expires_in),
            'scope' => new Scope($dto->scope),
            'refresh_token' => new RefreshToken($dto->refresh_token),
            'password' => new UserPassword($dto->password),
            'username' => new UserName($dto->username),
            'clientId' => new ClientId($dto->clientId),
            'clientSecret' => new clientSecret($dto->clientSecret)
        ]);
    }
}
