<?php

namespace API;

use App\Helpers\ExchangeRateHelper;
use CodeIgniter\HTTP\ResponseInterface;
use Models\ExchangeRateModel;

class ExchangeRateController extends BaseApiController
{
    protected ExchangeRateModel $exchangeRateModel;

    public function __construct()
    {
        $this->exchangeRateModel = model('Models\ExchangeRateModel');
    }

    /**
     * [get] /api/exchange-rate
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->response->setJSON(ExchangeRateHelper::getExchangeRate());
    }

}
