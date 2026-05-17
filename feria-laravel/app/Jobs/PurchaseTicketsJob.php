<?php

namespace App\Jobs;

use App\Exceptions\SeatUnavailableException;
use App\Models\Customer;
use App\Models\PurchaseQueueRequest;
use App\Services\PurchaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PurchaseTicketsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $purchaseRequestId)
    {
    }

    public function handle(PurchaseService $purchaseService): void
    {
        $request = PurchaseQueueRequest::query()->find($this->purchaseRequestId);
        if (!$request || $request->status !== 'queued') {
            return;
        }

        $request->status = 'processing';
        $request->save();

        try {
            $customer = Customer::query()->findOrFail($request->customer_id);
            $tickets = $purchaseService->execute($customer, (array) $request->payload);

            $request->status = 'completed';
            $request->result = [
                'ticketIds' => array_values(array_map(fn ($t) => $t->id, $tickets)),
            ];
            $request->error = null;
            $request->save();
        } catch (SeatUnavailableException $e) {
            $request->status = 'failed';
            $request->result = [
                'unavailableSeats' => $e->unavailableSeats(),
            ];
            $request->error = $e->getMessage();
            $request->save();
        } catch (\Throwable $e) {
            $request->status = 'failed';
            $request->error = 'Error al procesar compra';
            $request->result = [
                'detail' => $e->getMessage(),
            ];
            $request->save();
            throw $e;
        }
    }
}
