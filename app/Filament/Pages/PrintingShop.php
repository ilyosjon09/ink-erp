<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class PrintingShop extends Page
{
    protected static ?string $title = 'Печать';

    protected static ?string $navigationLabel = 'Печать';
    protected static ?string $navigationGroup = 'Работа';
    protected static ?string $navigationIcon = 'heroicon-o-printer';

    protected static ?string $slug = 'printing-shop';


    protected static string $view = 'filament.pages.printing-shop';
}
