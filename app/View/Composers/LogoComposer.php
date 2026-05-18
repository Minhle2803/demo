<?php

namespace App\View\Composers;

use App\Services\Admin\AdminSettingService;
use Illuminate\View\View;

class LogoComposer
{
    public function __construct(
        protected AdminSettingService $settingService,
    ) {}

    public function compose(View $view): void
    {
        $view->with('projectLogo', $this->settingService->getLogo());
    }
}
