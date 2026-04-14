<?php

namespace App\Services;

use App\Http\Integrations\TBank\Requests\DeleteCard;
use App\Http\Integrations\TBank\Requests\GetCardList;
use App\Http\Integrations\TBank\Requests\GetCustomer;
use App\Http\Integrations\TBank\Requests\InitPayment;
use App\Http\Integrations\TBank\Requests\ReqPayment;
use App\Http\Integrations\TBank\TbankConnector;
use App\Http\Integrations\TBank\Requests\AddCustomer;
use App\Http\Integrations\TBankOpenApi\Requests\SendInvoice;
use App\Http\Integrations\TBankOpenApi\TBankOpenApiConnector;
use App\Http\Integrations\TBank\Requests\Cancel;
use Saloon\Exceptions\RequestException;

class TbankService
{
    /**
     * Add customer to TBank system
     *
     * @param string $email Customer email
     * @param string $phone Customer phone number
     * @param string $customerKey Unique customer identifier
     * @return array Response from API
     * @throws \Exception
     */
    public function addCustomer(string $customerKey): array
    {
        try {
            // Создаём коннектор
            $connector = new TbankConnector();

            // Создаём запрос
            $request = new AddCustomer(
                CustomerKey: $customerKey
            );

            // Отправляем запрос через коннектор
            $response = $connector->send($request);

            // Логируем успешный ответ
            \Log::info('AddCustomer request successful', [
                'response' => $response->json(),
            ]);

            return $response->json();

        } catch (RequestException $e) {
            // Логируем ошибку
            \Log::error('AddCustomer request failed', [
                'error' => $e->getMessage(),
                'response_body' => $e->getResponse()?->body(),
            ]);

            throw new \Exception('Failed to add customer: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Ловим другие возможные ошибки
            \Log::error('Unexpected error in addCustomer', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
    public function init(string $OrderId, string $Amount, string $customerKey, string $Recurrent = null, string $SuccessURL = '', string $FailURL = ''): array
    {
        try {
            // Создаём коннектор
            $connector = new TbankConnector();

            // Создаём запрос
            $request = new InitPayment(
                CustomerKey: $customerKey,
                Recurrent: $Recurrent,
                OrderId: $OrderId,
                Amount: $Amount,
                SuccessURL: $SuccessURL,
                FailURL: $FailURL
            );

            // Отправляем запрос через коннектор
            $response = $connector->send($request);
            return $response->json();

        } catch (RequestException $e) {
            throw new \Exception('Failed to Init: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function ReqPayment($PaymentID, $RebildID): array
    {
        try {
            // Создаём коннектор
            $connector = new TbankConnector();

            $request = new ReqPayment(
                PaymentID: $PaymentID,
                RebildID: $RebildID
            );

            // Отправляем запрос через коннектор
            $response = $connector->send($request);;
            return $response->json();

        } catch (RequestException $e) {
            throw new \Exception('Failed to Pay: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function GetCards(string $customerKey): array
    {
        try {
            // Создаём коннектор
            $connector = new TbankConnector();

            // Создаём запрос
            $request = new GetCardList(
                CustomerKey: $customerKey,
            );

            // Отправляем запрос через коннектор
            $response = $connector->send($request);;
            return $response->json();

        } catch (RequestException $e) {
            throw new \Exception('Failed to GetCard ' . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function InitCustomer($customerKey): array
    {
        $TbankCustomer = $this->GetCustomer($customerKey);
        if(!isset($TbankCustomer['CustomerKey'])){
            $this->AddCustomer($customerKey);
            return ['CustomerKey' => $customerKey];
        }
        return $TbankCustomer;
    }

    private function GetCustomer($customerKey): array
    {
        try {
            // Создаём коннектор
            $connector = new TbankConnector();
            // Создаём запрос
            $request = new GetCustomer(
                CustomerKey: $customerKey
            );
            // Отправляем запрос через коннектор
            $response = $connector->send($request);

            return $response->json();

        } catch (RequestException $e) {
            // Логируем ошибку
            \Log::error('GetCustomer request failed', [
                'error' => $e->getMessage(),
                'response_body' => $e->getResponse()?->body(),
            ]);

            throw new \Exception('Failed to get customer: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Ловим другие возможные ошибки
            \Log::error('Unexpected error in addCustomer', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function SendInvoice($order): array
    {
        try {
            // Создаём коннектор
            $connector = new TBankOpenApiConnector();
            // Создаём запрос
            $request = new SendInvoice(
                $order->id,
                'ООО Рога и Копыта',
                'alliotbax@yandex.ru',
                '772745469525'
            );
            // Отправляем запрос через коннектор
            $response = $connector->send($request);

            return $response->json();

        } catch (RequestException $e) {
            // Логируем ошибку
            \Log::error('SendInvoice request failed', [
                'error' => $e->getMessage(),
                'response_body' => $e->getResponse()?->body(),
            ]);
            throw new \Exception('Failed to send invoice: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function CancelPayment($PaymentID): array
    {
        try {
            // Создаём коннектор
            $connector = new TbankConnector();

            $request = new Cancel($PaymentID);

            // Отправляем запрос через коннектор
            $response = $connector->send($request);;
            return $response->json();

        } catch (RequestException $e) {
            throw new \Exception('Failed to Cancel: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function RemoveCard($CardID, $customerKey): array
    {
        try {
            // Создаём коннектор
            $connector = new TbankConnector();

            $request = new DeleteCard($customerKey, $CardID);

            // Отправляем запрос через коннектор
            $response = $connector->send($request);;
            return $response->json();

        } catch (RequestException $e) {
            throw new \Exception('Failed to RemoveCard: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
