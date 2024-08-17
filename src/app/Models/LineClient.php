<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineClient extends Model
{
    use HasFactory;
    protected $table = 'line_client';
    protected $guarded = [''];
    protected $primaryKey = 'project_id';
    const UPDATED_AT = null;

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
     * channel id accessor function
     *
     * @param string $value
     * @return string
     */
    public function getChannelIdLoginAttribute(string $value): string
    {
        return $value;
    }

    /**
     * channel id accessor function
     *
     * @param string $value
     * @return string
     */
    public function getChannelIdMessageAttribute(string $value): string
    {
        return $value;
    }

    /**
     * channel secret accessor function
     *
     * @param string $value
     * @return string
     */
    public function getChannelSecretLoginAttribute(string $value): string
    {
        return $value;
    }

    /**
     * channel secret accessor function
     *
     * @param string $value
     * @return string
     */
    public function getChannelSecretMessageAttribute(string $value): string
    {
        return $value;
    }

    /**
     * channel access token accesor function
     *
     * @param string $value
     * @return string
     */
    public function getChannelAccessTokenLoginAttribute(string $value): string
    {
        return $value;
    }

    /**
     * channel access token accesor function
     *
     * @param string $value
     * @return string
     */
    public function getChannelAccessTokenMessageAttribute(string $value): string
    {
        return $value;
    }


    /**
     * get line client data function
     *
     * @param string $projectId
     * @return LineClient
     */
    public static function getLineClientData(string $projectId, array $columns): LineClient
    {
        return self::where('project_id', $projectId)->first($columns);
    }
}
