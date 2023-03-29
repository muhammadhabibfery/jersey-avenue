<?php

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
