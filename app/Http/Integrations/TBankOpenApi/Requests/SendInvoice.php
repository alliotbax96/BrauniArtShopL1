<?php

namespace App\Http\Integrations\TBankOpenApi\Requests;

use App\Http\Integrations\TBankOpenApi\TBankOpenApiConnector;
use App\Models\OrderItem;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SendInvoice extends Request implements HasBody
{
    use HasJsonBody;
    /**
     * The HTTP method of the request
     */
    protected ?string $connector = TBankOpenApiConnector::class;
    protected Method $method = Method::POST;

    public function __construct(
      protected string $OrderId,
      protected string $PayerName,
      protected string $PayerEmail,
      protected string $PayerInn,
      protected string $PayerKpp = '',
    ){}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/invoice/send';
    }

    private function GetItems() {
        return OrderItem::where('order_id', $this->OrderId)->get();
    }
    protected function defaultBody(): array
    {
        $items = array();
        foreach ($this->GetItems() as $item) {
            $items[] = array(
                'name' => $item->name,
                'price' => (int)$item->price,
                'unit' => 'Шт',
                'vat' => 'None',
                'amount' => $item->quantity,
            );
        }
        return array(
            'invoiceNumber' => $this->OrderId,
            'dueDate' => date('Y-m-d', strtotime('+3 day')),
            'invoiceDate' => date('Y-m-d'),
            'accountNumber' => '40702810310001273894',
            'payer' => array(
                'name' => $this->PayerName,
                'inn' => $this->PayerInn,
                'kpp' => $this->PayerKpp,
            ),
            'items' => $items,
            'contacts' => array(
                '0' => array(
                    'email' => $this->PayerEmail,
                )
            )
        );
    }
}
