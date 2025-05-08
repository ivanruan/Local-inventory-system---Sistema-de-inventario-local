<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $unit
 * @property int $stock
 * @property int $ubication_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Ubication $ubication
 * @property Collection|Input[] $inputs
 * @property Collection|Output[] $outputs
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'products';

	protected $casts = [
		'stock' => 'int',
		'ubication_id' => 'int'
	];

	protected $fillable = [
		'code',
		'name',
		'unit',
		'stock',
		'ubication_id'
	];

	public function ubication()
	{
		return $this->belongsTo(Ubication::class);
	}

	public function inputs()
	{
		return $this->hasMany(Input::class);
	}

	public function outputs()
	{
		return $this->hasMany(Output::class);
	}
}
