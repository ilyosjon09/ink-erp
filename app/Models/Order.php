<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'date',
        'status' => OrderStatus::class,
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paperType()
    {
        return $this->belongsTo(PaperType::class);
    }

    public function servicePrices()
    {
        return $this->belongsToMany(ServicePrice::class)->withTimestamps();
    }

    public function printingForms()
    {
        return $this->belongsToMany(PrintingForm::class)->withTimestamps();
    }

    public function maker()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function profitPercentage()
    {
        return $this->belongsTo(ProfitPercentage::class);
    }
}
