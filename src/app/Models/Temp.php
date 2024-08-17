<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temp extends Model
{
    use HasFactory;

    protected $table = 'temp';
    protected $guarded = [''];
    protected $primaryKey = 'uid';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = NULL;


    
    /**
     * uid accessor function
     *
     * @param string $value
     * @return string
     */
    public function getUidAttribute(string $value): string
    {
        return $value;
    }

    /**
     * login data accessor function
     *
     * @param string $value
     * @return string
     */
    public function getLoginDataAttribute(string $value): string
    {
        return $value;
    }

}
