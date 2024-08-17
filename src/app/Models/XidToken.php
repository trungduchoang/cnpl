<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XidToken extends Model
{
    use HasFactory;


    protected $table = 'xid_token';
    protected $guarded = [''];
    protected $primaryKey = 'cookie';
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;

    /**
     * cookie accessor function
     *
     * @param string $value
     * @return string
     */
    public function getCookieAttribute(string $value): string
    {
        return $value;
    }

    /**
     * access token accessor function
     *
     * @param string $value
     * @return string
     */
    public function getAccessTokenAttribute(string $value): string
    {
        return $value;
    }


    /**
     * project id accessor function
     *
     * @param string $value
     * @return string
     */
    public function getProjectIdAttribute(string $value): string
    {
        return $value;
    }

    /**
     * created_at function
     *
     * @param string $value
     * @return string
     */
    public function getCreatedAtAttribute(string $value): string
    {
        return $value;
    }
}
