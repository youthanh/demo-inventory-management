<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockExit;
use App\Models\Batch;
use App\Traits\ApiDataProcessingTrait;
use App\Services\InventoryService;

class StockExitController extends Controller
{
    use ApiDataProcessingTrait;
    protected $inventoryService;
    /**
     * Display a listing of the resource.
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate
        $stockExit = new StockExit;
        $batch = new Batch;
        $manyName = 'items';
        $validate = $this->applyValidate($stockExit->validate, ['name' => $manyName, 'validate' => $batch->validate]);
        unset($validate[$manyName.'.*.warehouse_id']);
        unset($validate[$manyName.'.*.date']);
        $request->validate($validate);

        $submitedData = $stockExit->create($request->all());

        if (!empty($submitedData->id)) {
            $stockExitItemsData = $request->input('items', []);
            foreach ($stockExitItemsData as $itemData) {
                $itemData['stock_exit_id'] = $submitedData->id;
                $itemData['warehouse_id'] = $submitedData->warehouse_id;
                $itemData['date'] = $submitedData->date;
                $batch->create($itemData);
            }
            return response()->json(['message' => 'Lưu thành công', 'data' => $submitedData], 201);
        }
        return response()->json(['message' => 'Lưu thất bại'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
