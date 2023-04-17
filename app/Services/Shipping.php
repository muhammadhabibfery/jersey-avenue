<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class Shipping
{
    private ?string $endpoint, $key, $param = null;
    private ?array $query = null;
    private array $couriers = [
        ['id' => 'jne', 'name' => 'JNE'],
        ['id' => 'tiki', 'name' => 'TIKI'],
        ['id' => 'pos', 'name' => 'POS']
    ];

    private function config(): void
    {
        $type = config('rajaongkir.type');
        $this->endpoint = "https://api.rajaongkir.com/$type/";
        $this->key = config('rajaongkir.key');
    }

    public function province(?int $id = null): self
    {
        $this->param = 'province';

        if ($id)
            $this->query = ['id' => $id];

        return $this;
    }

    public function city(?int $id = null): self
    {
        $this->param = 'city';

        if ($id)
            $this->query = ['id' => $id];

        return $this;
    }

    public function fromProvince(int $id): self
    {
        $this->query['province'] = $id;

        return $this;
    }

    public function cost(?int $origin, ?int $destination, ?int $weight, ?string $courier): self
    {
        $this->param = 'cost';

        $this->query = ['origin' => $origin, 'destination' => $destination, 'weight' => $weight, 'courier' => $courier];

        return $this;
    }

    public function couriers(): array
    {
        return $this->couriers;
    }

    public function get(): array
    {
        $this->config();
        $this->endpoint .= $this->param;

        if ($this->param === 'cost')
            $response = Http::withHeaders(['key' => $this->key])->post($this->endpoint, $this->query);
        else
            $response = Http::withHeaders(['key' => $this->key])->get($this->endpoint, $this->query);

        if ($response->clientError())
            throw new Exception($response->json('rajaongkir.status.description'), $response->json('rajaongkir.status.code'));

        $result = $response->json('rajaongkir.results');

        if ($this->param === 'cost')
            return $this->costResult($result);
        if ($this->query && count($result) < 1 || is_null($result))
            throw new Exception("{$this->param}(s) not found", Response::HTTP_BAD_REQUEST);

        return $result;
    }

    private function costResult(?array $result): array
    {
        if (is_null($result))
            throw new Exception("{$this->param}(s) not found", Response::HTTP_BAD_REQUEST);

        return $result;
    }
}
