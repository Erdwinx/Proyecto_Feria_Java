<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Ticket;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerApiController extends Controller
{
    public function __construct(private readonly JwtService $jwtService)
    {
    }

    public function register(Request $request)
    {
        $payload = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', 'min:6', 'max:120'],
        ]);

        if (Customer::where('email', $payload['email'])->exists()) {
            return response()->json(['message' => 'El correo ya esta registrado'], 400);
        }

        $customer = Customer::create([
            'nombre' => $payload['nombre'],
            'email' => $payload['email'],
            'password_hash' => Hash::make($payload['password']),
        ]);

        $token = $this->issueToken($customer);

        return response()->json([
            'token' => $token,
            'customer' => $this->customerToArray($customer),
        ]);
    }

    public function login(Request $request)
    {
        $payload = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', 'max:120'],
        ]);

        $customer = Customer::where('email', $payload['email'])->first();
        if (!$customer || !Hash::check($payload['password'], $customer->password_hash)) {
            return response()->json(['message' => 'Credenciales invalidas'], 400);
        }

        $token = $this->issueToken($customer);

        return response()->json([
            'token' => $token,
            'customer' => $this->customerToArray($customer),
        ]);
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
        $customer = $this->requireCustomer($request);

        $payload = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'fechaEvento' => ['required', 'date_format:Y-m-d'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:190'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $createdTickets = [];
        foreach ($payload['items'] as $item) {
            for ($i = 0; $i < $item['qty']; $i++) {
                $ticket = Ticket::create([
                    'id' => $this->nextTicketId($item['name']),
                    'nombre' => $item['name'],
                    'fecha_evento' => $payload['fechaEvento'],
                    'escaneado' => false,
                    'customer_id' => $customer->id,
                ]);
                $createdTickets[] = $this->ticketToArray($ticket);
            }
        }

        return response()->json($createdTickets);
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

        return [
            'id' => $ticket->id,
            'nombre' => $ticket->nombre,
            'fechaEvento' => (string) $fechaEvento,
            'escaneado' => (bool) $ticket->escaneado,
            'customerId' => $ticket->customer_id,
        ];
    }

    private function nextTicketId(string $nombre): string
    {
        $prefix = $this->buildPrefix($nombre);
        $max = Ticket::query()
            ->select('id')
            ->get()
            ->map(function (Ticket $ticket) {
                $raw = $ticket->id;
                if (strlen($raw) < 3) {
                    return 0;
                }

                return (int) substr($raw, 2);
            })
            ->max();

        $next = ((int) $max) + 1;
        return $prefix.str_pad((string) $next, 8, '0', STR_PAD_LEFT);
    }

    private function buildPrefix(string $nombre): string
    {
        $firstName = preg_split('/\s+/', trim($nombre))[0] ?? '';
        $letters = strtoupper(preg_replace('/[^\p{L}]/u', '', $firstName) ?? '');

        if (strlen($letters) >= 2) {
            return substr($letters, 0, 2);
        }
        if (strlen($letters) === 1) {
            return $letters.'X';
        }

        return 'XX';
    }
}
