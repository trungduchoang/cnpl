<?php

namespace App\UseCases\Library;

use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use App\Repositories\S3\S3ApiRepositoryInterface;
use App\Repositories\Ses\SesApiRepositoryInterface;
use App\Repositories\Login\LoginRepositoryInterface;

class SendEmailUseCase
{

    private $s3;
    private $ses;
    private $login;
    private $cognito;

    public function __construct(
        S3ApiRepositoryInterface $s3,
        SesApiRepositoryInterface $ses,
        LoginRepositoryInterface $login,
        CognitoApiRepositoryInterface $cognito
    ) {
        $this->s3 = $s3;
        $this->ses = $ses;
        $this->login = $login;
        $this->cognito = $cognito;
    }

    public function index(array $data)
    {
        try {
            list($bucket, $emailPath, $cookieListPath, $exclusionDomainListPath, $projectId, $subject) = $data;
            $html = $this->s3->getObject($bucket, $emailPath)['Body']->getContents();
            $cookieListCsv = $this->s3->getObject($bucket, $cookieListPath)['Body']->getContents();
            $exclusionDomainListCsv = $this->s3->getObject($bucket, $exclusionDomainListPath)['Body']->getContents();

            $cookieRows = explode("\n", $cookieListCsv);
            $cookieList = [];
            foreach ($cookieRows as $row) {
                $columns = str_getcsv($row);
                if (!empty($columns[0])) {
                    $cookieList[] = $columns[0];
                }
            }
            $cookieCount = count($cookieList);


            $exclusionDomainRows = explode("\n", $exclusionDomainListCsv);
            $exclusionDomainCount = count($exclusionDomainRows);
            $domainList = [];
            foreach ($exclusionDomainRows as $row) {
                $columns = str_getcsv($row);
                if (!empty($columns[0])) {
                    $domainList[] = $columns[0];
                }
            }
            $exclusionDomainCount = count($domainList);


            $emailCount = 0;
            $emailList = [];
            foreach ($cookieList as $key => $cookie) {
                $userName = $this->login->getUserData([
                    'cookie' => $cookie,
                    'projectId' => $projectId
                ]);

                if (!$userName) continue;
                $userName = $userName->oauth_username;

                $userInfo = $this->cognito->getUserInfo("username = \"" .$userName . "\"");
                if ($userName &&
                    count($userInfo['Users']) === 1 &&
                    ($userInfo['Users'][0]['UserStatus'] === 'CONFIRMED' || $userInfo['Users'][0]['UserStatus'] === 'EXTERNAL_PROVIDER')
                    ) {

                    $email = '';
                    foreach ($userInfo['Users'][0]['Attributes'] as $value) {
                        if ($value['Name'] == 'email') {
                            $email = $value['Value'];
                            break;
                        }
                    }
                    preg_match('/@([0-9a-zA-Z._-]+)/', $email, $domain);
                    $mailDomain = $domain[1];
                    $isCareer = in_array($mailDomain, $domainList);
                    if (!$isCareer) {
                        $emailList[$emailCount] = $email;
                        $emailCount++;
                    }
                }
            }

            if (!$emailList) throw new \Exception('no user list', 400);

            $separator = 0;
            $sendCount = 0;
            $errorCount = 0;
            $startTime = date("Y/m/d - (D) H:i:s");
            foreach ($emailList as $key => $email) {
                try {
                    $separator++;
                    $this->ses->sendEmail([
                        'Destination' => [
                            'BccAddresses' => [$email],
                        ],
                        'Source' => 'paychoiice@smartplate.pro',
                        'Message' => [
                        'Body' => [
                            'Html' => [
                                'Charset' => 'utf-8',
                                'Data' => $html,
                            ],
                        ],
                        'Subject' => [
                            'Charset' => 'utf-8',
                            'Data' => $subject,
                        ],
                        ],
                    ]);
                    if ($separator >= 20) {
                        $separator = 0;
                        sleep(1);
                    }
                    $sendCount++;
                } catch (\Exception $e) {
                    logger(date("Y/m/d - (D) H:i:s") . ' : ' . $e->getMessage());
                    $errorCount++;
                    continue;
                }
            }
            $endTime = date("Y/m/d - (D) H:i:s");
        } catch (\Exception $e) {
            logger($e);
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return [
            'cookieCount'              => $cookieCount,
            'emailCount'               => $emailCount,
            'sendCount'                => $sendCount,
            'exclusionDomainListCount' => $exclusionDomainCount,
            'startTime'                => $startTime,
            'endTime'                  => $endTime
        ];
    }
}
