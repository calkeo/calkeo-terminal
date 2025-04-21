<?php

namespace App\Commands;

enum CommandStates: string {
    case CLEAR = 'clear';
    case LOGOUT = 'logout';
    case INTERACTIVE = 'interactive';
}
