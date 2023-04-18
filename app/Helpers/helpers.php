<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * get rules for check role.
 */
function getRulesRole(): array
{
    $roles = User::$roles;
    $rolesCount = count($roles);

    foreach ($roles as $role) {
        $rules[$role] = $rolesCount;
        $rolesCount--;
    }

    return $rules;
}

/**
 * Identify the user role.
 */
function checkRole(string $availableRole, string $userRole): bool
{
    $rules = getRulesRole();
    return $rules[$userRole] >= $rules[$availableRole];
}

/**
 * Transform Date Format.
 */
function transformDateFormat(string $data, ?string $format = null): string|Carbon
{
    $result = Carbon::parse($data);

    if ($format) $result = $result->translatedFormat($format);

    return $result;
}

/**
 * Set and display currency format.
 */
function currencyFormat(int $value): string
{
    return "Rp. " .  number_format($value, 0, '.', '.');
}

/**
 * Set and display integer format.
 */
function integerFormat(string $value): int
{
    return (int) preg_replace('/[^0-9]/', '', $value);
}

/**
 * Generate invoice number.
 */
function generateInvoiceNumber(): string
{
    return 'JERSEY-AVENUE-' . date('djy') . Str::random(10);
}

/**
 * Get an order with in cart status.
 */
function getOrderCart(): ?Order
{
    return auth()->user()
        ->orders()
        ->where('status', Order::$status[0])
        ->first();
}

/**
 * Update jersey stock.
 */
function updateJerseyStock(Order $order): array
{
    $result = [];

    foreach ($order->jerseys as $jersey) {
        $newStock = array_merge($jersey->stock, [$jersey->pivot->size =>  $jersey->stock[$jersey->pivot->size] - $jersey->pivot->quantity]);

        $jersey->stock = $newStock;
        $jersey->sold += $jersey->pivot->quantity;
        $jersey->save();

        array_push($result, $newStock);
    }

    return $result;
}

/**
 * Format courier service from array to string.
 */
function courierServiceFormat(?array $couriers = null): string
{
    if (is_array($couriers)) {
        $couriers['code'] = strtoupper($couriers['code']);
        $couriers['value'] = currencyFormat($couriers['value']);

        return "{$couriers['code']} - {$couriers['service']} {$couriers['description']} {$couriers['value']} Estimasi : {$couriers['etd']}";
    }

    return "-";
}

/**
 * Set permissions.
 */
function setPermissions(string $availableRole, User $user, ?Closure $ability = null): bool
{
    return $ability
        ? $ability() && checkRole($availableRole, $user->role)
        : checkRole($availableRole, $user->role);
}
