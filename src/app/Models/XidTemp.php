<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XidTemp extends Model
{
    use HasFactory;

    protected $table = 'xid_temp';
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
     * redirect url accessor function
     *
     * @param string $value
     * @return string
     */
    public function getRedirectUrlAttribute(string $value): string
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
     * env accessor function
     *
     * @param string $value
     * @return string
     */
    public function getEnvAttribute(string $value): string
    {
        return $value;
    }

}
