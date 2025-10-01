<?php

/**
 * Репозиторий для работы с замками и кодами доступа через API Sciener.
 *
 * @autor Vladislav
 * @email corchagin.vlad2005@yandex.ru
 * @create date 2023-08-01 09:52:03
 * @modify date 2023-08-01 09:52:03
 */

declare(strict_types=1);

namespace app\repositories\lock;

use app\entities\lock\LockInit;
use app\entities\passCode\PassCode;
use app\repositories\NotFoundException;
use app\entities\passCode\KeyboardPwdId;

class LockRepository
{
    /**
     * Инициализация замка через API
     *
     * @return LockInit объект с данными инициализации замка
     * @throws NotFoundException при ошибке в ответе API
     */
    public function LockInit(): LockInit
    {
        $url = 'https://api.sciener.com/v3/lock/init';
        // Данные для POST запроса на инициализацию замка
        $data = array(
            'clientId'    => clientId, // ID клиента (желательно передавать параметром)
            'accessToken' => accessToken,                       // Токен доступа
            'lockData'    => lockData, // Информация о замке
            'lockAlias'   => '',                                 // Псевдоним замка (если нужен)
            'date'        => '90000',                            // Временная метка или дата запроса
        );

        // Настройка cURL для отправки POST запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Выполнение запроса и получение результата
        $response = curl_exec($ch);
        curl_close($ch);

        // Проверка на наличие ошибки в ответе API
        if (isset($response['errcode'])) {
            throw new NotFoundException("errcode => " . $response['errcode']
                . " errmsg => " . $response['errmsg']);
        }

        // Возврат объекта LockInit с данными ответа
        return new LockInit($response);
    }


    /**
     * Получение списка кодов доступа к замку через API
     *
     * @param string $clientId - ID клиента
     * @param string $accessToken - Токен доступа
     * @param string $lockId - ID замка
     * @param int $keyboardPwdVersion - Версия пароля клавиатуры
     * @param string $date - Дата запроса
     * @param int $keyboardPwdType - Тип пароля
     * @param string $startDate - Начало периода действия
     * @param string $endDate - Конец периода действия
     *
     * @return PassCode Объект с данными кодов доступа
     * @throws NotFoundException при ошибке API
     */
    public function getPassCode(
        $clientId,
        $accessToken,
        $lockId,
        $keyboardPwdVersion,
        $date,
        $keyboardPwdType,
        $startDate,
        $endDate
    ) {
        $url = 'https://api.sciener.com/v3/keyboardPwd/get';
        // Данные POST запроса для получения кодов доступа
        $data = array(
            'clientId' => $clientId,
            'accessToken' => $accessToken,
            'lockId' => $lockId,
            'keyboardPwdVersion' => $keyboardPwdVersion,
            'keyboardPwdType' => $keyboardPwdType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'date' => $date,
        );

        // Выполнение POST запроса через cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        // Проверка ответа на наличие ошибки
        if (isset($response['errcode'])) {
            throw new NotFoundException("errcode => " . $response['errcode']
                . " errmsg => " . $response['errmsg']);
        }

        return new PassCode($response);
    }


    /**
     * Добавление нового кода доступа к замку через API
     *
     * @param string $clientId - ID клиента
     * @param string $accessToken - Токен доступа
     * @param string $lockId - ID замка
     * @param string $keyboardPwd - Новый код клавиатуры
     * @param string $date - Дата запроса
     * @param int $addType - Тип добавления кода
     * @param string $startDate - Начало действия кода
     * @param string $endDate - Конец действия кода
     *
     * @return KeyboardPwdId Объект с ID добавленного пароля
     * @throws NotFoundException при ошибке API
     */
    public function addPassCode(
        $clientId,
        $accessToken,
        $lockId,
        $keyboardPwd,
        $date,
        $addType,
        $startDate,
        $endDate
    ) {
        $url = 'https://api.sciener.com/v3/keyboardPwd/add';
        // Данные POST запроса для добавления кода доступа
        $data = array(
            'clientId'    => $clientId,
            'accessToken' => $accessToken,
            'lockId'      => $lockId,
            'keyboardPwd' => $keyboardPwd,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'addType'     => $addType,
            'date'        => $date,
        );

        // Выполнение запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        // Проверка на наличие ошибки в ответе API
        if (isset($response['errcode'])) {
            throw new NotFoundException("errcode => " . $response['errcode']
                . " errmsg => " . $response['errmsg']);
        }

        // Возврат объекта с ID добавленного пароля
        return new KeyboardPwdId($response);
    }


    /**
     * Удаление кода доступа к замку через API
     *
     * @param string $clientId - ID клиента
     * @param string $accessToken - Токен доступа
     * @param string $lockId - ID замка
     * @param string $keyboardPwdId - ID кода доступа для удаления
     * @param string $date - Дата запроса
     * @param int $deleteType - Тип удаления
     *
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
        $url = 'https://api.sciener.com/v3/keyboardPwd/add'; // Ошибка: должен быть URL для удаления, скорее https://api.sciener.com/v3/keyboardPwd/delete
        // Данные запроса для удаления пароля
        $data = array(
            'clientId'      => $clientId,
            'accessToken'   => $accessToken,
            'lockId'        => $lockId,
            'keyboardPwdId' => $keyboardPwdId,
            'deleteType'    => $deleteType,
            'date'          => $date,
        );

        // Выполнение запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        // Проверка ошибок API
        if (isset($response['errcode'])) {
            throw new NotFoundException("errcode => " . $response['errcode']
                . " errmsg => " . $response['errmsg']);
        }
    }


    /**
     * Удаление замка через API
     *
     * @param string $clientId - ID клиента
     * @param string $accessToken - Токен доступа
     * @param string $lockId - ID замка для удаления
     * @param string $date - Дата запроса
     *
     * @throws NotFoundException при ошибке API
     */
    public function LockDelete($clientId, $accessToken, $lockId, $date)
    {
        $url = 'https://api.sciener.com/v3/lock/delete';
        // Данные для удаления замка
        $data = array(
            'clientId' => $clientId,
            'accessToken' => $accessToken,
            'lockId' => $lockId,
            'date' => $date
        );

        // Отправка запроса на удаление замка
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        // Проверка на ошибки в ответе
        if (isset($response['errcode'])) {
            throw new NotFoundException("errcode => " . $response['errcode']
                . " errmsg => " . $response['errmsg']);
        }
    }
}
