<?php

namespace App\Services;

use App\Models\CompetitionSetting;
use Carbon\Carbon;

class CompetitionSettingService
{
    public function getCurrent(): CompetitionSetting
    {
        return CompetitionSetting::query()->latest()->firstOrCreate([], []);
    }

    public function canManageLineups(): bool
    {
        $setting = $this->getCurrent();

        return ! $setting->lineup_deadline || Carbon::now()->lte($setting->lineup_deadline);
    }
}
