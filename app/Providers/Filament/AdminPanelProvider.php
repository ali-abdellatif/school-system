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
use App\Filament\Widgets\ApplicationsChartWidget;
use App\Filament\Widgets\ApplicationsOverviewWidget;
use App\Filament\Widgets\DashboardWelcomeWidget;
use App\Filament\Widgets\SchoolOverviewWidget;
use App\Support\Filament\ConfiguresSchoolPanel;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

class AdminPanelProvider extends PanelProvider
{
    use ConfiguresSchoolPanel;

    public function panel(Panel $panel): Panel
    {
        return $this->configureSchoolBrand($panel)
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                DashboardWelcomeWidget::class,
                SchoolOverviewWidget::class,
                ApplicationsOverviewWidget::class,
                ApplicationsChartWidget::class,
                AccountWidget::class,
            ])
            // ملاحظة: Filament لا يسمح بوجود أيقونة للمجموعة وأيقونات لعناصرها معًا.
            // اخترنا الإبقاء على أيقونات العناصر (لكل مورد أيقونته)، لذا لا نضع أيقونة للمجموعة.
            ->navigationGroups([
                NavigationGroup::make('القبول والتسجيل')
                    ->collapsed(false),
                NavigationGroup::make('الهيكل الأكاديمي')
                    ->collapsed(true),
                NavigationGroup::make('المعلمون والمواد')
                    ->collapsed(true),
                NavigationGroup::make('إدارة الوصول')
                    ->collapsed(true),
                NavigationGroup::make('إعدادات الموقع')
                    ->collapsed(true),
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
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('إدارة الوصول')
                    ->navigationSort(1)
                    ->navigationIcon('heroicon-o-shield-check'),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
