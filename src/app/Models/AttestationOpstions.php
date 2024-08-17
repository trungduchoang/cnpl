<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttestationOpstions extends Model
{
    use HasFactory;
    protected $table = 'attestation_options';
    protected $guarded = [''];
    protected $primaryKey = 'challenge';
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;


    /**
     * challenge accessor function
     *
     * @param string $value
     * @return string
     */
    public function getChallengeAttribute(string $value): string
    {
        return $value;
    }

    /**
     * origin accessor function
     *
     * @param string $value
     * @return string
     */
    public function getOriginAccessor(string $value): string
    {
        return $value;
    }

    
    /**
     * create attestation options function
     *
     * @param array $data
     * @return void
     */
    public static function createAttestationOptions(array $data): void
    {
        AttestationOpstions::create($data);
    }

    /**
     * get attestation opsions function
     *
     * @param string $challenge
     * @return object
     */
    public static function getAttestationOpsions(string $challenge): object
    {
        $data = AttestationOpstions::where('challenge', $challenge)->first();
        return $data;
    }


    /**
     * delete attestation opsions function
     *
     * @param string $challenge
     * @return void
     */
    public static function deleteAttestationOpsions(string $challenge): void
    {
        AttestationOpstions::where('challenge', $challenge)->delete();
    }

}
