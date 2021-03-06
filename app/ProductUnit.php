<?php
/**
 * Created by PhpStorm.
 * User: Sugito
 * Date: 9/10/2016
 * Time: 12:57 PM
 */

namespace App;

use \Illuminate\Database\Eloquent\Model;

/**
 * App\ProductUnit
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $store_id
 * @property integer $unit_id
 * @property boolean $is_base
 * @property float $conversion_value
 * @property string $remarks
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereStoreId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereUnitId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereIsBase($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereConversionValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereRemarks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductUnit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductUnit extends Model
{
    protected $table = 'product_unit';

    protected $fillable = [
        ''
    ];
}