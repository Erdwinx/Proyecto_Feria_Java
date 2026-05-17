<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\PurchaseTicketsJob;
use App\Models\Customer;
use App\Models\PurchaseQueueRequest;
use App\Models\Ticket;
use App\Services\JwtService;
use App\Services\PurchaseService;
use App\Exceptions\SeatUnavailableException;
use App\Traits\ApiErrorHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerApiController extends Controller
{
    use ApiErrorHandler;

    public function __construct(
        private readonly JwtService $jwtService,
        private readonly PurchaseService $purchaseService,
    )
    {
    }

    public function register(Request $request)
    {
        try {
            $payload = $request->validate([
                'nombre' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:190', 'unique:customers,email'],
                'password' => ['required', 'string', 'min:6', 'max:120'],
            ]);

            $customer = Customer::create([
                'nombre' => $payload['nombre'],
                'email' => $payload['email'],
                'password_hash' => Hash::make($payload['password']),
            ]);

            $token = $this->issueToken($customer);
            $this->logError('Customer registered: ' . $customer->id, [], 'info');

            return response()->json([
                'token' => $token,
                'customer' => $this->customerToArray($customer),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            if (isset($errors['email'])) {
                $this->logError('Email already registered: ' . ($request->input('email') ?? 'unknown'));
                return $this->errorResponse('El correo ya esta registrado', 400, ['errors' => $errors]);
            }

            $this->logError('Register validation error', ['errors' => $errors]);
            return $this->errorResponse('Datos inválidos', 422, ['errors' => $errors]);
        } catch (\Exception $e) {
            $this->logError('Register error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->errorResponse('Error al registrar', 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $payload = $request->validate([
                'email' => ['required', 'email', 'max:190'],
                'password' => ['required', 'string', 'max:120'],
            ]);

            $customer = Customer::where('email', $payload['email'])->first();
            if (!$customer || !Hash::check($payload['password'], $customer->password_hash)) {
                $this->logError('Login failed for email: ' . $payload['email']);
                return $this->errorResponse('Credenciales invalidas', 400);
            }

            $token = $this->issueToken($customer);
            $this->logError('Customer logged in: ' . $customer->id, [], 'info');

            return response()->json([
                'token' => $token,
                'customer' => $this->customerToArray($customer),
            ]);
        } catch (\Exception $e) {
            $this->logError('Login error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->errorResponse('Error al iniciar sesion', 500);
        }
    }

    public function me(Request $request)
    {
        $customer = $this->requireCustomer($request);

        return response()->json($this->customerToArray($customer));
    }

    public function listTickets(Request $request)
    {
        $customer = $this->requireCustomer($request);

        $tickets = Ticket::query()
            ->where('customer_id', $customer->id)
            ->orderBy('fecha_evento')
            ->get();

        return response()->json($tickets->map(fn (Ticket $ticket) => $this->ticketToArray($ticket))->values());
    }

    public function purchase(Request $request)
    {
        try {
            $customer = $this->requireCustomer($request);
            $payload = $this->validatePurchasePayload($request);
            $createdTickets = $this->purchaseService->execute($customer, $payload);

            return response()->json(array_map(fn (Ticket $ticket) => $this->ticketToArray($ticket), $createdTickets));
        } catch (SeatUnavailableException $e) {
            return $this->errorResponse('Uno o más asientos ya no están disponibles', 409, [
                'unavailableSeats' => $e->unavailableSeats(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logError('Purchase validation error', ['errors' => $e->errors()]);
            return $this->errorResponse('Datos inválidos', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            $this->logError('Purchase error: ' . $e->getMessage(), ['exception' => $e]);
            return $this->errorResponse('Error al procesar compra', 500);
        }
    }

    public function queuePurchase(Request $request)
    {
        $customer = $this->requireCustomer($request);
        $payload = $this->validatePurchasePayload($request);

        $id = (string) Str::uuid();
        PurchaseQueueRequest::query()->create([
            'id' => $id,
            'customer_id' => $customer->id,
            'payload' => $payload,
            'status' => 'queued',
            'result' => null,
            'error' => null,
        ]);

        PurchaseTicketsJob::dispatch($id)->onQueue('purchases');

        return response()->json([
            'requestId' => $id,
            'status' => 'queued',
            'message' => 'Solicitud encolada',
        ], 202);
    }

    public function queuePurchaseStatus(Request $request, string $id)
    {
        $customer = $this->requireCustomer($request);
        $purchaseRequest = PurchaseQueueRequest::query()
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$purchaseRequest) {
            return $this->errorResponse('Solicitud no encontrada', 404);
        }

        return response()->json([
            'requestId' => $purchaseRequest->id,
            'status' => $purchaseRequest->status,
            'result' => $purchaseRequest->result,
            'error' => $purchaseRequest->error,
            'updatedAt' => optional($purchaseRequest->updated_at)?->toISOString(),
        ]);
    }

    private function validatePurchasePayload(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'fechaEvento' => ['required', 'date_format:Y-m-d'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:190'],
            'items.*.category' => ['required', 'string', 'max:30'],
            'items.*.seatNumbers' => ['nullable', 'array'],
            'items.*.seatNumbers.*' => ['string', 'max:10'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);
    }

    private function requireCustomer(Request $request): Customer
    {
        $customer = $request->attributes->get('authCustomer');
        if (!$customer instanceof Customer) {
            abort(401, 'No autenticado');
        }

        return $customer;
    }

    private function issueToken(Customer $customer): string
    {
        return $this->jwtService->issueToken([
            'sub' => (int) $customer->id,
            'nombre' => $customer->nombre,
            'email' => $customer->email,
        ]);
    }

    private function customerToArray(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'nombre' => $customer->nombre,
            'email' => $customer->email,
        ];
    }

    private function ticketToArray(Ticket $ticket): array
    {
        $fechaEvento = $ticket->fecha_evento;
        if ($fechaEvento instanceof \DateTimeInterface) {
            $fechaEvento = $fechaEvento->format('Y-m-d');
        }

        // Map evento name based on fecha_evento
        $eventMap = [
            '2026-05-10' => 'Concierto Central',
            '2026-05-14' => 'Concierto Central',
            '2026-05-18' => 'Concierto Central',
            '2026-06-15' => 'Ritmo Urbano',
            '2026-06-21' => 'Ritmo Urbano',
            '2026-06-28' => 'Ritmo Urbano',
            '2026-06-19' => 'Electro Fest',
            '2026-06-23' => 'Electro Fest',
            '2026-06-27' => 'Electro Fest',
            '2026-06-17' => 'Noche de Pop',
            '2026-06-20' => 'Noche de Pop',
            '2026-06-24' => 'Noche de Pop',
            '2026-05-11' => 'Feria Local',
            '2026-05-12' => 'Feria Local',
            '2026-05-13' => 'Feria Local',
            '2026-05-17' => 'Feria Local',
        ];

        $data = [
            'id' => $ticket->id,
            'nombre' => $ticket->nombre,
            'fechaEvento' => (string) $fechaEvento,
            'nombreEvento' => $eventMap[(string) $fechaEvento] ?? 'Evento',
            'tipoEvento' => $ticket->tipo_evento,
            'category' => $ticket->category,
            'seatNumbers' => $ticket->seat_numbers ?? [],
            'escaneado' => (bool) $ticket->escaneado,
            'customerId' => $ticket->customer_id,
        ];

        if ($ticket->tipo_evento === 'concierto' && $ticket->package_id) {
            $package = $ticket->package;
            if ($package) {
                $data['packageId'] = $package->id;
                $data['packageName'] = $package->nombre;
            }
        }

        return $data;
    }

}
