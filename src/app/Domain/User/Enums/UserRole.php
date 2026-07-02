<?php

namespace App\Domain\User\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Operator = 'operator';
}
