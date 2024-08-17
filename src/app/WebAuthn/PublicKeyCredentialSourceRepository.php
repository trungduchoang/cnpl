<?php
namespace App\WebAuthn;

use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialSourceRepository as PublicKeyCredentialSourceRepositoryInterface;
use Webauthn\PublicKeyCredentialUserEntity;
use App\Models\WebAuthnCredential as WebAuthnCredentialModel;
// use Project\Entity\WebauthnCredential;


class PublicKeyCredentialSourceRepository implements PublicKeyCredentialSourceRepositoryInterface
{

    public function findAllForUserEntity(PublicKeyCredentialUserEntity $publicKeyCredentialUserEntity): array
    {
        $WebauthnCredentials = $this->repository->findBy(['user_handle' => $publicKeyCredentialUserEntity->getId()]);
        return array_map(function ($WebauthnCredential) {
            $array = json_decode($WebauthnCredential->getCredential(), true);
            return PublicKeyCredentialSource::createFromArray($array);
        }, $WebauthnCredentials);
    }

    public function findOneByCredentialId(string $publicKeyCredentialId): ?PublicKeyCredentialSource
    {
        if ($WebauthnCredential = $this->repository->findOneBy(['publicKeyCredentialSourceId' => base64_encode($publicKeyCredentialId)])){
            $array = json_decode($WebauthnCredential->getCredential(), true);
            return PublicKeyCredentialSource::createFromArray($array);
        }
        return null;
    }

    public function saveCredentialSource(PublicKeyCredentialSource $publicKeyCredentialSource, bool $flush = true): void
    {

        $data['publicKeyCredentialSourceId'] = base64_encode($publicKeyCredentialSource->getPublicKeyCredentialId());
        $data['userHandle'] = $publicKeyCredentialSource->getUserHandle();
        $data['credential'] = json_encode($publicKeyCredentialSource);

        $WebauthnCredential = $this->repository->findOneBy(['publicKeyCredentialSourceId' => $data['publicKeyCredentialSourceId']]);

        if (!$WebauthnCredential) {
            $WebauthnCredential = new WebauthnCredential();
            $WebauthnCredential->fromArray($data);
            $this->entityManager->presist($WebauthnCredential);
        } else {
            $WebauthnCredential->fromArray($data);
            $this->entityManager->merge($WebauthnCredential);
        }
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    
}