<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub extends Model
{
    use HasFactory;
    protected $fillable = [
        'sub_name',
        'category',
        'next_payment',
        'amount',
        'user_id',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public $timestamps = true;
    /**
     * Relation
     */
    //Belongs to
    public function user(){
        return $this ->belongsTo('App\Models\User','user_id','id');
    }
}
