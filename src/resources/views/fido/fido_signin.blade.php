<!DOCTYPE html>
<html lang="ja">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGNIN</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        #signin-btn {
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
        }

        .link-sign-up {
            text-decoration: none;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <button id="signin-btn" onclick="signIn()">Sign In</button>
    <a href="/auth/fido/signup" class="link-sign-up">Go to Sign up page</a>
    <script>
        async function signIn() {
            const projectId = localStorage.getItem("projectId")
            const { origin, host } = window.location;
            const challengeResponse = await axios.request({
                url: 'https://' + host + '/api/auth/signin/attestation/options',
                method: "POST",
                data: { projectId, origin }
            }).then(res => res).catch(e => {
                alert(e.response?.data?.error?.message || e.message || "Unknown Error!")
            });
            const { data: { challenge, credentialId } } = challengeResponse;
            if (!challenge) throw new Error("Empty challenge response!");
            const navigatorResponse = await navigator.credentials.get({
                publicKey: {
                    challenge: Uint8Array.from(`${challenge}`.match(/.{1,2}/g).map(byte => parseInt(byte, 16))).buffer,
                    rpId: host,
                    userVerification: "required",
                }
            }).then(res => res.response)
                .catch(err => {
                    console.log(e);
                    alert('Error during credential verification:', err);
                    return null;
                });
            if (!navigatorResponse) return;
            const signInResultResponse = await axios.request({
                url: 'https://' + host + '/api/auth/signin/assertion/result',
                method: "POST",
                data: {
                    id: credentialId,
                    clientDataJson: Array.from(new Uint8Array(navigatorResponse.clientDataJSON)),
                    authenticatorData: Array.from(new Uint8Array(navigatorResponse.authenticatorData)),
                    signature: Array.from(new Uint8Array(navigatorResponse.signature)),
                    projectId,
                    cognito: 0
                }
            }).then(res => {
                alert("Signing in successfully!")
            }).catch(e => {
                console.log(e);
                alert(e.response?.data?.error?.message || e.message || "Unknown Error!")
            });
        }
        function base64ToUint8Array(base64) {
            const binaryString = window.atob(base64);
            const len = binaryString.length;
            const bytes = new Uint8Array(len);
            for (let i = 0; i < len; i++) {
                bytes[i] = binaryString.charCodeAt(i);
            }
            return bytes;
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
    </script>
</body>

</html>