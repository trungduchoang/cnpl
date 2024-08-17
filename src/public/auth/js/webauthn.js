function credentialsCreate(challenge, rp) {
    let userId = new Uint8Array(16)
    challenge = Base64toUint8Array(challenge)
    let publicKey = new createOptions(userId, challenge, rp)
    return navigator.credentials.create({'publicKey': publicKey})
        .then((newCredentialInfo) => {
            let { id, rawId, response, type } = newCredentialInfo
            let { attestationObject, clientDataJSON } = response
            return {
                id: id,
                clientDataJSON: Uint8ArraytoBase64(clientDataJSON),
                attestationObject: Uint8ArraytoBase64(attestationObject)
            }
        })

}

function credentialsGet(id, challenge) {
    publicKey = getOptions(id, challenge)
    return navigator.credentials.get({'publicKey': publicKey})
        .then((credentialInfo) => {
            let { id, type, rawId, response } = credentialInfo
            let { authenticatorData, clientDataJSON, signature, userHandle } = response
            return {
                id: id,
                authenticatorData: authenticatorData,
                clientDataJSON: clientDataJSON,
                signature: signature
            }
        })
}

function createOptions(userId, challenge, rp) {
    return {
        rp: {
            id: rp,
            name: rp
        },
        user: {
            id: userId,
            name: userId,
            displayName: userId
        },
        authenticatorSelection: {
            requireResidentKey: false,
            authenticatorAttachment: 'platform',
            userVerification: 'discouraged'
        },
        challenge: challenge,
        pubKeyCredParams: [
            {
                type: 'public-key',
                alg: -7
            }
        ],
        timeout: 60000,
        attestation: 'direct',
        extensions: null
    }
}

function getOptions(id, challenge) {
    return {
        challenge: Base64toUint8Array(challenge),
        userVerification: 'required',
        allowCredentials: [
            {
                transports: ['internal'],
                type: 'public-key',
                id: Base64toUint8Array(id),
            },
        ],
    }
}

function Uint8ArraytoBase64(bin, opt = { urlsafe: true }) {
    const uint8array = new Uint8Array(bin)

    const str = btoa(String.fromCharCode(...uint8array))
    if (opt.urlsafe) {
        return str
            .replace(/\+/g, "-")
            .replace(/\//g, "_")
            .replace(/=/g, "")
    }
    return str
}

function Base64toUint8Array(b64str, opt = { urlsafe: true }) {
    if (opt.urlsafe) {
        const len = b64str.length
        b64str = b64str
            .replace(/-/g, "+")
            .replace(/_/g, "/")
            .padEnd(len + ((4 - len % 4) % 4), "=")
    }
    return new Uint8Array([...atob(b64str)].map((e) => e.charCodeAt(0)))
}
