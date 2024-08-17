<!DOCTYPE html>
<html lang="ja">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGNUP</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        function arrayBufferToBase64(buffer) {
            let binary = '';
            const bytes = new Uint8Array(buffer);
            const len = bytes.byteLength;
            for (let i = 0; i < len; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return window.btoa(binary); // Base64 encode
        }
        window.onload = function () {
            const publicKey = {
                challenge: Uint8Array.from('82a50f8199c80af4f188f7cae61a6b33a948e9e5'.match(/.{1,2}/g).map(byte => parseInt(byte, 16))).buffer,
                rp: { id: 'ea9c-2402-800-6312-f7f5-ccb1-8c3b-3d0c-6950.ngrok-free.app', name: "ACME Corporation" },
                user: {
                    id: new Uint8Array([79, 252, 83, 72, 214, 7, 89, 26]),
                    name: "DucHT",
                    displayName: "HT-Duc",
                },
                pubKeyCredParams: [{ type: "public-key", alg: -7 }, { type: "public-key", alg: -257 }],
            };

            const publicKeyCredential = navigator.credentials.create({ publicKey }).then(credential => {
                const clientDataJSON = arrayBufferToBase64(credential.response.clientDataJSON);
                const attestationObject = arrayBufferToBase64(credential.response.attestationObject);
                console.log({
                    clientDataJSON,
                    attestationObject
                });
            });
        }

    </script>
</head>


<body>
    <div id="test"></div>
</body>


</html>