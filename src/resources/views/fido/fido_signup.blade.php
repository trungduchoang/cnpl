<!DOCTYPE html>
<html lang="ja">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGNUP</title>
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

        #signup-btn {
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
        }

        #project-id,
        #name {
            padding: 10px;
            font-size: 16px;
            margin-bottom: 7px;
        }

        .link-sign-in {
            text-decoration: none;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <label for="project-id">ProjectId:</label>
    <input type="number" id="project-id" name="projectId" min="1" required>
    <label for="name">Name</label>
    <input id="name" required>

    <button id="signup-btn" onclick="signUp()">Sign Up</button>
    <a href="/auth/fido/signin" class="link-sign-in">Go to Sign in page</a>
    <script>
        async function signUp() {
            const projectId = document.getElementById('project-id').value;
            if (!projectId) {
                alert("Please enter a valid ProjectId!");
                return;
            }
            const name = document.getElementById('name').value;
            if (!name) {
                alert("Please enter a name!");
                return;
            }

            const { origin, host } = window.location;
            const challengeResponse = await axios.request({
                url: 'https://' + host + '/api/auth/signup/attestation/options',
                method: "POST",
                data: { projectId, origin }
            }).then(res => res).catch(e => {
                alert(e.response?.data?.error?.message || e.message || "Unknown Error!")
            });
            const { data: { challenge } } = challengeResponse;
            if (!challenge) throw new Error("Empty challenge response!");
            const credentialId = generateRandomUint8Array(8);
            const navigatorResponse = await navigator.credentials.create({
                publicKey: {
                    challenge: Uint8Array.from(`${challenge}`.match(/.{1,2}/g).map(byte => parseInt(byte, 16))).buffer,
                    rp: { id: host, name },
                    user: {
                        id: credentialId,
                        name,
                        displayName: name,
                    },
                    pubKeyCredParams: [{ type: "public-key", alg: -7 }, { type: "public-key", alg: -257 }],
                }
            })
                .then(res => res.response)
                .catch(err => {
                    console.log({ err });
                    alert('Error during credential creation');
                    return null;
                });
            if (!navigatorResponse) return;
            const clientDataJson = arrayBufferToBase64(navigatorResponse.clientDataJSON);
            const attestationObject = arrayBufferToBase64(navigatorResponse.attestationObject);
            const signUpResultResponse = await axios.request({
                url: 'https://' + host + '/api/auth/signup/assertion/result',
                method: "POST",
                data: {
                    id: arrayBufferToBase64(credentialId),
                    clientDataJson,
                    attestationObject,
                    projectId,
                    cognito: 0
                }
            }).then(res => {
                alert("Signing up successfully!");
                localStorage.setItem("projectId", projectId);
                window.open("/auth/fido/signin", "_self");
            }).catch(e => {
                console.log(e);
                alert(e.response?.data?.error?.message || e.message || "Unknown Error!")
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
</body>

</html>