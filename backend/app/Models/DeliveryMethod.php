<?php
namespace App\Models;

use App\Core\Model;

/**
 * DeliveryMethod Model
 */
class DeliveryMethod extends Model {
    protected $table = 'delivery_methods';
    protected $useSoftDelete = false;
}
