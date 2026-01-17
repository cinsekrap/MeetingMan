<?php

namespace App\Enums;

enum ActionStatus: string
{
    case NotStarted = 'not_started';
    case OnTrack = 'on_track';
    case Complete = 'complete';
    case Dropped = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::OnTrack => 'On Track',
            self::Complete => 'Complete',
            self::Dropped => 'Dropped',
        };
    }
}
