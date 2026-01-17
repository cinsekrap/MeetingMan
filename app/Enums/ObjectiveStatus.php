<?php

namespace App\Enums;

enum ObjectiveStatus: string
{
    case OnTrack = 'on_track';
    case OffTrack = 'off_track';
    case Complete = 'complete';
    case Dropped = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::OnTrack => 'On Track',
            self::OffTrack => 'Off Track',
            self::Complete => 'Complete',
            self::Dropped => 'Dropped',
        };
    }
}
