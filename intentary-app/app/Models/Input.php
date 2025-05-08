<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Input
 * 
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property Carbon $date
 * @property string|null $destination
 * @property string|null $observation
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product $product
 * @property User $user
 *
 * @package App\Models
 */
class Input extends Model
{
	protected $table = 'inputs';

	protected $casts = [
		'product_id' => 'int',
		'quantity' => 'int',
		'date' => 'datetime',
		'user_id' => 'int'
	];

	protected $fillable = [
		'product_id',
		'quantity',
		'date',
		'destination',
		'observation',
		'user_id'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
