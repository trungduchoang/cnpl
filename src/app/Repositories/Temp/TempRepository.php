<?php
namespace App\Repositories\Temp;

use App\Models\Temp;

class TempRepository implements TempRepositoryInterface
{
    private $temp;

    public function __construct(Temp $temp)
    {
        $this->temp = $temp;
    }


    /**
     * create temp data function
     *
     * @param string $uid
     * @param string $loginData
     * @return void
     */
    public function createTemp(string $uid, string $loginData): void
    {
        try {
            $this->temp->create(['uid' => $uid, 'login_data' => $loginData]);
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    /**
     * get temp data function
     *
     * @param string $uid
     * @return string
     */
    public function getTemp(string $uid): mixed
    {
        try {
            return $this->temp->where('uid', $uid)->first();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    /**
     * delete temp function
     *
     * @param string $uid
     * @return void
     */
    public function deleteTemp(string $uid): void
    {
        try {
            $this->temp->where('uid', $uid)->delete();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }
}