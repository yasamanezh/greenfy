<?php


namespace App\Library;


use GuzzleHttp\Client;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;


class SMS
{

    public static function sendVerify($number, $token)
    {

        try {
            $_request['header'] = [
                'Content-Type' => 'application/json',
            ];
            $client = new Client();
            $result = $client->request(
                'POST',
                "https://api.kavenegar.com/v1/766456653173615A6D616D59564E41423657416378653935496A716E4D6C5645686756415A784A3849524D3D/verify/lookup.json?receptor=" . $number . "&token=" . $token . "&template=verifyipaeez",
                [
                    'headers' => $_request['header'],
                    'http_errors' => false
                ]
            );
            return $result;
        } catch (ApiException $e) {
            echo $e->errorMessage();
        } catch (HttpException $e) {
            echo $e->errorMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public static function sendByPatern($number, $token,$tmplate)
    {
        try {
            $_request['header'] = [
                'Content-Type' => 'application/json',
            ];
            $client = new Client();
            $result = $client->request(
                'POST',
                "https://api.kavenegar.com/v1/766456653173615A6D616D59564E41423657416378653935496A716E4D6C5645686756415A784A3849524D3D/verify/lookup.json?receptor=" . $number . "&token=" . $token . "&template=" . $tmplate,
                [
                    'headers' => $_request['header'],
                    'http_errors' => false
                ]
            );
            return $result;
        } catch (ApiException $e) {
            echo $e->errorMessage();
        } catch (HttpException $e) {
            echo $e->errorMessage();
        } catch (\Exception $e) {

        }
        return false;
    }

    public static function sendGroup($number, $sender, $messages)
    {
        try {
            $api = new KavenegarApi("654E673545304D666E53483230486E2F3154364A3677384E434D76487441574944735568456233305073633D");
            $senders = $sender;
            $receptors = $number;
            $message = $messages;
            $result = $api->SendArray($senders, $receptors, $message);
            if ($result) {
                return json_encode($result);
            }
        } catch (ApiException $e) {
            echo $e->errorMessage();
        } catch (HttpException $e) {
            echo $e->errorMessage();
        }
        return false;
    }
}
