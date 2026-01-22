<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomLogin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Enums\ThemeMode;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->defaultThemeMode(ThemeMode::Dark)
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->brandName('ISO Digital')
            ->favicon(asset('logo.jpg'))
            ->colors([
                'primary' => Color::Blue,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Emerald,
                'info' => Color::Sky,
            ])
            ->font('Plus Jakarta Sans')
            ->navigationGroups([
                'Attendance',
                'Documents',
                'Settings',
                'Asset Management',
                'HRD Management',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // \App\Filament\Widgets\StatsOverviewWidget::class, // Disabled - tables don't exist in this commit
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
            ])
            ->renderHook(
                'panels::auth.login.form.after',
                fn() => view('filament.login-microsoft-button')
            )
            ->renderHook(
                'panels::head.end',
                fn() => '
                    <link rel="stylesheet" href="' . asset('css/admin-theme.css') . '">
                    <link rel="stylesheet" href="' . asset('css/mobile-enhancements.css') . '">
                    
                    <!-- PWA Manifest -->
                    <link rel="manifest" href="' . asset('manifest.json') . '">
                    
                    <!-- PWA Meta Tags -->
                    <meta name="theme-color" content="#005f89">
                    <meta name="mobile-web-app-capable" content="yes">
                    <meta name="apple-mobile-web-app-capable" content="yes">
                    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
                    <meta name="apple-mobile-web-app-title" content="ISO Digital">
                    
                    <!-- Apple Touch Icon -->
                    <link rel="apple-touch-icon" href="' . asset('apple-touch-icon.png') . '">
                    
                    <!-- Viewport for mobile -->
                    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
                    
                    <!-- Service Worker Registration -->
                    <script>
                        if ("serviceWorker" in navigator) {
                            window.addEventListener("load", () => {
                                navigator.serviceWorker
                                    .register("' . asset('sw.js') . '")
                                    .then((registration) => {
                                        console.log("[PWA] Service Worker registered:", registration.scope);
                                    })
                                    .catch((error) => {
                                        console.log("[PWA] Service Worker registration failed:", error);
                                    });
                            });
                        }
                        
                        // PWA Install Prompt
                        let deferredPrompt;
                        window.addEventListener("beforeinstallprompt", (e) => {
                            e.preventDefault();
                            deferredPrompt = e;
                            
                            // Show custom install UI
                            const installPrompt = document.createElement("div");
                            installPrompt.className = "pwa-install-prompt";
                            installPrompt.innerHTML = `
                                <div class="icon">📱</div>
                                <div class="content">
                                    <div class="title">Install ISO Digital</div>
                                    <div class="description">Akses lebih cepat seperti aplikasi</div>
                                </div>
                                <div class="actions">
                                    <button class="install-btn">Install</button>
                                    <button class="dismiss-btn">✕</button>
                                </div>
                            `;
                            
                            document.body.appendChild(installPrompt);
                            
                            setTimeout(() => {
                                installPrompt.classList.add("show");
                            }, 100);
                            
                            installPrompt.querySelector(".install-btn").addEventListener("click", async () => {
                                deferredPrompt.prompt();
                                const { outcome } = await deferredPrompt.userChoice;
                                console.log(`[PWA] User response: ${outcome}`);
                                installPrompt.remove();
                                deferredPrompt = null;
                            });
                            
                            installPrompt.querySelector(".dismiss-btn").addEventListener("click", () => {
                                installPrompt.remove();
                            });
                        });
                    </script>
                '
            )
            ->renderHook(
                'panels::body.start',
                fn() => '<div id="particles-bg"><ul class="circles"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>'
            );
    }
}
