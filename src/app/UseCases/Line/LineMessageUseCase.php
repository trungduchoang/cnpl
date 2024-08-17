<?php

namespace App\UseCases\Line;

use Illuminate\Support\Facades\DB;
use App\Models\LineClient;
use App\Repositories\Line\LineApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;
use App\Repositories\S3\S3ApiRepositoryInterface;

class LineMessageUseCase
{
    private $lineClient;
    private $s3Client;
    private $lineApiRepository;
    private $loginRepository;
    

    public function __construct(
        LoginRepositoryInterface $loginRepository,
        LineClient $lineClient,
        S3ApiRepositoryInterface $s3Client,
        LineApiRepositoryInterface $lineApiRepository
    ) {
        $this->lineClient = $lineClient;
        $this->s3Client = $s3Client;
        $this->lineApiRepository = $lineApiRepository;
        $this->loginRepository = $loginRepository;
    }

    /**
     * send line message multicast usecase function
     *
     * @param array $data
     * @return array
     */
    public function handle(array $data): array
    {
        DB::beginTransaction();
        try {
            list($bucket, $userNamesPath, $userName, $messages, $projectId) = $data;

            $lineClientData = $this->lineClient::where('project_id', $projectId)
                                                ->first([
                                                    'channel_id_message',
                                                    'channel_secret_message',
                                                    'channel_access_token_message'
                                                ]);
            

            $channelAccessToken = $lineClientData->channel_access_token_message;
            if (!$this->lineApiRepository->verifyChannelAccessToken($lineClientData->channel_access_token_message)) {
                $channelAccessToken = $this->lineApiRepository->getChannelAccessToken(
                    $lineClientData->channel_id_message,
                    $lineClientData->channel_secret_message
                );


                $this->lineClient::where('channel_id_message', $lineClientData->channel_id_message)
                                 ->where('project_id', $projectId)
                                 ->update(['channel_access_token_message' => $channelAccessToken]);
            }


            if ($bucket && $userNamesPath) {
                $userNameListCsv = $this->s3Client->getObject($bucket, $userNamesPath)['Body']->getContents();
    
                $userNameRows = explode("\n", $userNameListCsv);
                $userNameList = [];
                foreach ($userNameRows as $row) {
                    $columns = str_getcsv($row);
                    if (!empty($columns[0])) {
                        $userNameList[] = $columns[0];
                    }
                }
                $cookieCount = count($userNameList);
            } else {
                $userNameList[] = $userName;
                $userNameCount = count($userNameList);
            }
            
            // $userNames = $this->loginRepository->getUsers($cookieList, $projectId);
            $userNameCount = count($userNameList);
            $userNameListChank = array_chunk($userNameList, 500);

            $sendCount = 0;
            foreach ($userNameListChank as $key => $chank) {
                $this->lineApiRepository->sendMessageMulticast($chank, $messages, $channelAccessToken);
                $sendCount++;
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return [
            'sendCount' => $sendCount,
            'messages'  => $messages
        ];
    }
}