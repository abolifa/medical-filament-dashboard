<?php

namespace App\Models;

use Database\Factories\CenterProductStockFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static lockForUpdate()
 */
class CenterProductStock extends Model
{
    /** @use HasFactory<CenterProductStockFactory> */
    use HasFactory;

    protected $fillable = ['center_id', 'product_id', 'quantity', 'min_threshold'];

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
