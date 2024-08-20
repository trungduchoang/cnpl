<?php

namespace App\Models;

use Dotenv\Util\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credentials extends Model
{
    use HasFactory;
    protected $table = 'credentials';
    protected $guarded = [''];
    protected $primaryKey = 'credential_id';
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
     * oauth_username accessor function
     *
     * @param string $value
     * @return string
     */
    public function getOauthUsernameAttribute(string $value): string
    {
        return $value;
    }

    /**
     * credential id accessor function
     *
     * @param string $value
     * @return string
     */
    public function getCredentialIdAttribute(string $value): string
    {
        return $value;
    }

    /**
     * pem accessor function
     *
     * @param string $value
     * @return string
     */
    public function getPemAttribute(string $value): string
    {
        return $value;
    }


    /**
     * create credentials function
     *
     * @param array $data
     * @return void
     */
    public static function createCredentials(array $data): void
    {
        Credentials::create($data);
    }


    /**
     * get credentials function
     *
     * @param string $cookie
     * @param string $userName
     * @return object
     */
    public static function getCredentials(string $cookie, string $projectId): ?object
    {
        $data = Credentials::where('cookie', $cookie)->where('project_id', $projectId)->first();
        return $data;
    }
}
