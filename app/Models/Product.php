<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Product extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Type',
        'Model',
        'Color',
        'Rostest',
        'PurchasePrice',
        'Wholesaler',
        'Retailer',
        'Bonus',
        'CardCash',
        'Monitor',
        'SellPrice',
        'parseDate',
        'SberParseDate',
        'LinkToSMM',
        'FeedID',
    ];

    protected $primaryKey = 'ProductID';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    protected $table = 'product';
    public $timestamps = false;

}
