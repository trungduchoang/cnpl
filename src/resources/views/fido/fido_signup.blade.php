<!DOCTYPE html>
<html lang="ja">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGNUP</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        window.onload = async function signUp() {
            const projectId = "222"; // FIXME: Update projectId after getting response from Q&A
            const { origin, host } = window.location;
            const challengeResponse = await axios.request({
                url: 'https://' + host + '/api/auth/signup/attestation/options',
                method: "POST",
                data: { projectId, origin }
            }).then(res => res).catch(e => {
                alert(e.message || "Unknown Error!")
            });
            const { data: { challenge } } = challengeResponse;
            if (!challenge) throw new Error("Empty challenge response!");
            const credentailId = generateRandomUint8Array(8);
            const navigatorResponse = await navigator.credentials.create({
                publicKey: {
                    challenge: Uint8Array.from(`${challenge}`.match(/.{1,2}/g).map(byte => parseInt(byte, 16))).buffer,
                    rp: { id: host, name: "Smartplate" },
                    user: {
                        id: credentailId,
                        name: "Smartplate SignUp",
                        displayName: "Smartplate",
                    },
                    pubKeyCredParams: [{ type: "public-key", alg: -7 }, { type: "public-key", alg: -257 }],
                }
            })
                .then(res => res.response)
                .catch(err => {
                    alert('Error during credential creation:', err);
                    return null;
                });
            if (!navigatorResponse) return;
            const clientDataJson = arrayBufferToBase64(navigatorResponse.clientDataJSON);
            const attestationObject = arrayBufferToBase64(navigatorResponse.attestationObject);
            const signUpResultResponse = await axios.request({
                url: 'https://' + host + '/api/auth/signup/assertion/result',
                method: "POST",
                data: {
                    id: arrayBufferToBase64(credentailId),
                    clientDataJson,
                    attestationObject,
                    projectId,
                    cognito: 0
                }
            }).then(res => res).catch(e => {
                alert(e.message || "Unknown Error!")
            });
        }
        function arrayBufferToBase64(buffer) {
            let binary = '';
            const bytes = new Uint8Array(buffer);
            const len = bytes.byteLength;
            for (let i = 0; i < len; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return window.btoa(binary);
        }
        function generateRandomUint8Array(length) {
            const array = new Uint8Array(length);
            for (let i = 0; i < length; i++) {
                array[i] = Math.floor(Math.random() * 256);
            }
            return array;
        }
    </script>
</head>

<body>
    <div id="test">

    </div>
</body>

</html>