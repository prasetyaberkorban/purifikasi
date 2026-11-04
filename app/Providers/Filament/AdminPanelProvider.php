<?php

namespace App\Providers\Filament;

use App\Enums\UserRole;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName('Purifikasi Inventaris')
            ->brandLogo(fn () => asset('img/purifikasi-logo.png'))
            ->brandLogoHeight('2.25rem')
            ->favicon(asset('img/purifikasi-logo.png'))
            ->colors([
                'primary' => [
                    50 => '#eaf4ee',
                    100 => '#d3e9dc',
                    200 => '#a9d3ba',
                    300 => '#7cba97',
                    400 => '#4f9f74',
                    500 => '#2c8455',
                    600 => '#1e6b38',
                    700 => '#16552c',
                    800 => '#0d4c1e',
                    900 => '#06240f',
                    950 => '#031107',
                ],
                'info' => Color::hex('#6495ED'),
                'warning' => Color::hex('#8B8540'),
                'success' => Color::hex('#1E6B38'),
                'danger' => Color::Red,
                'gray' => Color::Gray,
            ])
            ->font('Instrument Sans')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->authGuard('web')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}