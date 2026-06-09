<?php

namespace App\Filament\Parent\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Collection;
use UnitEnum;

class Notifications extends Page
{
    protected string $view = 'filament.parent.pages.notifications';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static string|UnitEnum|null $navigationGroup = 'التواصل';

    protected static ?string $navigationLabel = 'الإشعارات';

    protected static ?string $title = 'الإشعارات';

    public static function getNavigationBadge(): ?string
    {
        $count = auth()->user()?->unreadNotifications()->count() ?? 0;

        return $count > 0 ? (string) $count : null;
    }

    /** @return Collection<int, \Illuminate\Notifications\DatabaseNotification> */
    public function getNotificationsProperty(): Collection
    {
        return auth()->user()?->notifications()->latest()->limit(100)->get() ?? collect();
    }

    public function markAsRead(string $id): void
    {
        auth()->user()?->notifications()->where('id', $id)->update(['read_at' => now()]);
    }

    public function markAllRead(): void
    {
        auth()->user()?->unreadNotifications->markAsRead();
    }
}
