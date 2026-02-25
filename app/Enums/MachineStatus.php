<?php

namespace App\Enums;

enum MachineStatus: string
{
    case Ok = 'ok';
    case Updates = 'updates';
    case Security = 'security';
    case Stale = 'stale';
    case Error = 'error';

    public function label(): string
    {
        return match ($this) {
            self::Ok => 'Aktuell',
            self::Updates => 'Updates verfügbar',
            self::Security => 'Sicherheitsupdates',
            self::Stale => 'Keine Meldung',
            self::Error => 'Fehler',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ok => 'green',
            self::Updates => 'yellow',
            self::Security => 'red',
            self::Stale => 'gray',
            self::Error => 'red',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Ok => 'bg-green-100 text-green-800',
            self::Updates => 'bg-yellow-100 text-yellow-800',
            self::Security => 'bg-red-100 text-red-800',
            self::Stale => 'bg-gray-100 text-gray-800',
            self::Error => 'bg-red-100 text-red-800',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Ok => 'bg-green-500',
            self::Updates => 'bg-yellow-500',
            self::Security => 'bg-red-500',
            self::Stale => 'bg-gray-400',
            self::Error => 'bg-red-500',
        };
    }
}
