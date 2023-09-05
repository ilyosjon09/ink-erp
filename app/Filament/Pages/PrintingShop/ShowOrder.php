<?php

namespace App\Filament\Pages\PrintingShop;

use Filament\Pages\Page;

class ShowOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.printing-shop.show-order';

    protected static ?string $title = 'Печать';

    protected static ?string $slug = 'printing-shop/show';

    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
