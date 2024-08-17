<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LINE LOGIN</title>
    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>

        window.onload = function() {
            queries = getUrlQueries()
            liff.init({
                liffId: queries.liffId
            })
            liff.ready.then(() => {
                let accessToken = liff.getAccessToken()
                if (!liff.isInClient() || !accessToken) {
                    let params = {
                        state: getState(queries),
                        botPrompt: 'aggressive'
                    }
                    axios.get('https://' + location.host + '/api/auth/auth-url/line', {params: params}).then((res) => {
                        window.location.href = res.data.line
                    }).catch((err) => {
                        
                    })
                }

                axios.post('https://' + location.host + '/api/auth/liff/crypto', {accessToken: accessToken})
                    .then((res) => {
                        queries['accessToken'] = encodeURIComponent(res.data.accessToken)
                        liff.openWindow({
                            url: 'https://' + location.host + '/auth/liff/redirect' + getRedirectQuery(queries),
                            external: true
                        })
                    })
                    .then(() => {
                        new Promise( resolve => setTimeout(resolve, 3000) )
                            .then( ()=>{
                                liff.closeWindow()
                            })
                    })
            })
        }


        function getState(queries) {
            let state = {}
            for (query in queries) {
                if (query != 'liffId') {
                    state[query] = decodeURIComponent(queries[query])
                }
            }
            return state;
        }
        

        function getRedirectQuery(queries, start = 0) {
            let queryStr = ''
            let counter = start
            for (query in queries) {
                if (!['liffId', 'redirectUrlErr'].includes(query)) {
                    if (counter == 0) {
                        queryStr += '?' + query + '=' + queries[query]
                    } else {
                        queryStr += '&' + query + '=' + queries[query]
                    }
                    counter += 1
                }
            }
            return queryStr;
        }


        function getUrlQueries() {
            let queryStr = window.location.search.slice(1)
                queries = {}

            if (!queryStr) {
                return queries
            }


            
            queryStr.split('&').forEach(function(queryStr) {
                let queryArr = queryStr.split('=')
                queries[queryArr[0]] = queryArr[1]
            })


            if (queries['liff.state']) {
                queryStr = decodeURIComponent(queries['liff.state']).slice(1)
                queryStr.split('&').forEach(function(queryStr) {
                    let queryArr = queryStr.split('=')
                    queries[queryArr[0]] = queryArr[1]
                })
            }

            return queries;
        }

    </script>
</head>


<body>
    <div id="test"></div>
</body>


</html>


