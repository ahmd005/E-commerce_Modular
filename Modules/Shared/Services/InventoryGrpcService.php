<?php

namespace Modules\Shared\Services;

use Exception;
use Grpc\ChannelCredentials;
use Inventory\InventoryServiceClient;
use Inventory\CreateProductRequest;
use Inventory\UpdateStockRequest;
use Inventory\DeleteProductRequest;
use Inventory\StockRequest;
use Inventory\PBEmpty;

class InventoryGrpcService
{
    protected InventoryServiceClient $client;

    public function __construct()
    {
        $this->client = new InventoryServiceClient(
            env('INVENTORY_GRPC_ADDR', 'localhost:50051'),
            ['credentials' => ChannelCredentials::createInsecure()]
        );
    }

    /** ينفّذ النداء، وإذا فشل يرمي Exception برسالة مفهومة */
    private function run($call)
    {
        [$response, $status] = $call->wait();

        if ($status->code !== \Grpc\STATUS_OK) {
            if ($status->code === \Grpc\STATUS_UNAVAILABLE
                || $status->code === \Grpc\STATUS_DEADLINE_EXCEEDED) {
                throw new Exception('خدمة المستودع غير متاحة حالياً');
            }
            throw new Exception($status->details);
        }
        return $response;
    }

    private function opts(): array
    {
        return ['timeout' => 2000000]; // مهلة ثانيتين (بالميكروثانية)
    }

    private function toArray($p): array
    {
        return [
            'id'       => $p->getId(),
            'name'     => $p->getName(),
            'sku'      => $p->getSku(),
            'quantity' => $p->getQuantity(),
            'in_stock' => $p->getInStock(),
        ];
    }

    public function createProduct(array $data)
    {
        $req = new CreateProductRequest();
        $req->setName($data['name']);
        $req->setSku($data['sku'] ?? 'SKU-' . uniqid());
        $req->setInitialQuantity($data['initial_quantity'] ?? 0);

        return $this->toArray($this->run($this->client->CreateProduct($req, [], $this->opts())));
    }

    public function updateStock(string $uuid, int $delta)
    {
        $req = new UpdateStockRequest();
        $req->setProductId($uuid);
        $req->setDelta($delta);

        return $this->toArray($this->run($this->client->UpdateStock($req, [], $this->opts())));
    }

    public function deleteProduct(string $productId)
    {
        try {
            $req = new DeleteProductRequest();
            $req->setProductId($productId);
            $this->run($this->client->DeleteProduct($req, [], $this->opts()));
        } catch (Exception $e) {
            // نفس السلوك القديم: تجاهل/تسجيل الخطأ فقط
        }
    }

    public function reserveStock(string $productId, int $quantity)
    {
        $req = new StockRequest();
        $req->setProductId($productId);
        $req->setQuantity($quantity);

        return $this->toArray($this->run($this->client->ReserveStock($req, [], $this->opts())));
    }

    public function listProducts()
    {
        $res = $this->run($this->client->ListProducts(new PBEmpty(), [], $this->opts()));

        $out = [];
        foreach ($res->getData() as $p) {
            $row = $this->toArray($p);
            $row['product_id'] = $row['sku']; // نفس الحقل اللي كان يرجع بالنسخة القديمة
            $out[] = $row;
        }
        return $out;
    }
}
