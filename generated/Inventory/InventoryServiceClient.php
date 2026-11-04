<?php
// عميل gRPC لخدمة InventoryService (مطابق لما يولّده grpc_php_plugin)

namespace Inventory;

class InventoryServiceClient extends \Grpc\BaseStub
{
    public function __construct($hostname, $opts, $channel = null)
    {
        parent::__construct($hostname, $opts, $channel);
    }

    public function CreateProduct(\Inventory\CreateProductRequest $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/inventory.InventoryService/CreateProduct',
            $argument, ['\Inventory\ProductResponse', 'decode'], $metadata, $options);
    }

    public function UpdateStock(\Inventory\UpdateStockRequest $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/inventory.InventoryService/UpdateStock',
            $argument, ['\Inventory\ProductResponse', 'decode'], $metadata, $options);
    }

    public function DeleteProduct(\Inventory\DeleteProductRequest $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/inventory.InventoryService/DeleteProduct',
            $argument, ['\Inventory\PBEmpty', 'decode'], $metadata, $options);
    }

    public function ReserveStock(\Inventory\StockRequest $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/inventory.InventoryService/ReserveStock',
            $argument, ['\Inventory\ProductResponse', 'decode'], $metadata, $options);
    }

    public function ReleaseStock(\Inventory\StockRequest $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/inventory.InventoryService/ReleaseStock',
            $argument, ['\Inventory\ProductResponse', 'decode'], $metadata, $options);
    }

    public function CheckStock(\Inventory\StockRequest $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/inventory.InventoryService/CheckStock',
            $argument, ['\Inventory\ProductResponse', 'decode'], $metadata, $options);
    }

    public function ListProducts(\Inventory\PBEmpty $argument, $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/inventory.InventoryService/ListProducts',
            $argument, ['\Inventory\ProductListResponse', 'decode'], $metadata, $options);
    }
}
