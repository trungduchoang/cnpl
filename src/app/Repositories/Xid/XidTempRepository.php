<?php
namespace App\Repositories\Xid;

use App\Models\XidTemp;

class XidTempRepository implements XidTempRepositoryInterface
{

    private $xidTemp;

    public function __construct(XidTemp $xidTemp)
    {
        $this->xidTemp = $xidTemp;
    }

    
    /**
     * create xid temp function
     *
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        try {
            $this->xidTemp->updateOrCreate(
                ['cookie' => $data['cookie']],
                [
                    'redirect_url'  => $data['redirectUrl'],
                    'project_id'    => $data['projectId'],
                    'env'           => $data['env']
                ]
            );
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    
    /**
     * get xid temp function
     *
     * @param string $cookie
     * @return object
     */
    public function get(array $data): ?object
    {
        try {
            return $this->xidTemp->where('cookie', $data['cookie'])->first();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }

    
    /**
     * delete xid temp function
     *
     * @param string $cookie
     * @return void
     */
    public function delete(string $cookie): void
    {
        try {
            $this->xidTemp->where('cookie', $cookie)->delete();
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception('db error', 500);
        }
    }
}