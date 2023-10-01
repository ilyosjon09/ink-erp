<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PostPrintShop extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Цех';
    protected static ?string $slug = 'post-print-shop';
    protected static ?string $navigationLabel = 'Цех';
    protected static ?string $navigationGroup = 'Работа';

    protected static string $view = 'filament.pages.post-print-shop';
}
