<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use DateTime;
use DateInterval;
use DateTimeZone;
use yii\console\ExitCode;
use app\repositories\Hydrator;
use yii\console\Controller;
use app\entities\lock\KeyId;
use app\entities\lock\lockId;
use app\entities\lock\LockData;
use app\entities\lock\LockInit;
use app\entities\user\ClientId;
use app\repositories\NotFoundException;
use app\repositories\lock\LockRepository;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }
    public function actionGet()
    {
        $url = 'https://api.sciener.com/oauth2/token';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => '610626a4526744958c54655a4d0f6b33',
            'client_secret' => '2aa2dd4a48779adda36fb3ab4663c77d',
            'username' => 'servicebook_admin',
            'password' => '2020524031ebed89bb114fc6bbd094f1',
        ]));


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            echo "Curl Error: " . $error;
        } else {
            echo $response;
        }

        //return $response['access_token'];
    }

    public function actionReg()
    {
        $url = 'https://api.sciener.com/v3/user/register';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'clientId' => '610626a4526744958c54655a4d0f6b33',
            'clientSecret' => '2aa2dd4a48779adda36fb3ab4663c77d',
            'username' => 'servicebook_admin',
            'password' => '2020524031ebed89bb114fc6bbd094f1',
            "date" => (int)round(microtime(true) * 1000)
        ]));


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            echo "Curl Error: " . $error;
        } else {
            echo $response;
        }

        //return $response['access_token'];
    }

    public function actionInit()
    {
        $futureTimestamp = time();

        // Преобразование времени в миллисекунды
        $futureTimestamp = (time() + 1) * 1000;

        $url = 'https://euapi.sciener.com/v3/lock/initialize';
        $data = array(
            'clientId'      => '610626a4526744958c54655a4d0f6b33',
            'accessToken'   => '37d86f3752e2355bd827621c596c1fb4',
            'lockData'      => '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',
            'date'          =>  $futureTimestamp
        );
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true
        );
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }

    public function actionList()
    {
        $repo = new LockRepository(new Hydrator());
        $futureTimestamp = time();

        // Преобразование времени в миллисекунды
        $futureTimestamp = (time() + 1) * 1000;

        $url = 'https://euapi.sciener.com/v3/lock/list';
        $data = array(
            'clientId'      => '610626a4526744958c54655a4d0f6b33',
            'accessToken'   => '37d86f3752e2355bd827621c596c1fb4',
            'lockData'      => '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',
            'date'          =>  $futureTimestamp,
            'pageNo' => 1,
            'pageSize' => 1000
        );
        $options = array(

            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true
        );
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = json_decode(curl_exec($curl));
        $error = curl_error($curl);

        curl_close($curl);
        if ($error) {
            echo "Curl Error: " . $error;
        } else {
            foreach ($response->list as $key) {
                $lock = new LockInit(
                    new lockId($key->lockId),
                    null,
                    new ClientId('610626a4526744958c54655a4d0f6b33'),
                    new LockData($key->lockData)
                );
                if ($key->lockData === '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') {
                    $repo->addLock($lock);
                    var_dump('Тестовый id: '.$key->lockId);
                }
                $repo->addLock($lock);
                var_dump('+'.$key->lockId);
            }
        }
        var_dump($response);

    }
    public function actionPassList()
    {
        // Преобразование времени в миллисекунды
        $date = new \DateTimeImmutable();
        $time = (int)$date->format('Uv');
        $url = 'https://euapi.sciener.com/v3/lock/listKeyboardPwd';
        $data = array(
            'clientId'      => '610626a4526744958c54655a4d0f6b33',
            'accessToken'   => '37d86f3752e2355bd827621c596c1fb4',
            'lockId'   => 4335406,
            'date'   =>  $time,
            'pageNo' => 1	,
            'pageSize' => 1000
        );
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,

            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array('ContentType:application/x-www-form-urlencoded'),
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true
        );
        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            echo "Curl Error: " . $error;
        } else {
            var_dump(json_decode($response));
        }

    }
}
