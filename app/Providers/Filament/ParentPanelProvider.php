<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use App\Support\Filament\ConfiguresSchoolPanel;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class ParentPanelProvider extends PanelProvider
{
    use ConfiguresSchoolPanel;

    public function panel(Panel $panel): Panel
    {
        return $this->configureSchoolBrand($panel)
            ->id('parent')
            ->path('parent')
            ->login()
            ->discoverResources(in: app_path('Filament/Parent/Resources'), for: 'App\Filament\Parent\Resources')
            ->discoverPages(in: app_path('Filament/Parent/Pages'), for: 'App\Filament\Parent\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Parent/Widgets'), for: 'App\Filament\Parent\Widgets')
            ->widgets([
                \App\Filament\Parent\Widgets\ParentWelcomeWidget::class,
                \App\Filament\Parent\Widgets\MyChildrenWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('متابعة أبنائي')->collapsed(false),
                NavigationGroup::make('التواصل')->collapsed(false),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                'parent',
            ]);
    }
}
