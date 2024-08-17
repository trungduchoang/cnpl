<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>plate.id</title>
    <script src="{{ asset('auth/js/jquery-3.0.0.min.js') }}"></script>
    <script src="{{ asset('auth/js/jquery.modal.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('auth/css/jquery.modal.min.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/css/error.css') }}">
    <style>
        .content p.text01 {
            margin-bottom: 2vw;
            font-size: 40px !important;
            font-size: 5.33333vw !important;
            font-weight: 700;
            text-align: center;
        }

        .content p.text02 {
            width: 100%;
            margin: 0 auto;
            font-size: 26px !important;
            font-size: 3.46667vw !important;
        }
        .jquery-modal .modal#code-error {
            background: transparent;
            color: #fff;
        }

        .jquery-modal .modal#code-error .close-modal {
            display: none;
        }

        .jquery-modal .modal#code-error .content {
            text-align: center;
        }

        .jquery-modal .modal#code-error .content .image {
            width: 16.42857%;
            margin: 0 auto 5.06667vw;
        }

        .jquery-modal .modal#code-error .content p.text01 {
            margin-bottom: 2vw;
            font-size: 36px !important;
            font-size: 4.8vw !important;
            font-weight: 700;
        }

        .jquery-modal .modal#code-error .content p.text02 {
            font-size: 22px !important;
            font-size: 3.5vw !important;
        }

        .jquery-modal .modal#code-error .content .btn {
            width: 44.28571%;
            margin: 9.33333vw auto 0;
        }

        .jquery-modal .modal#code-error .content .btn .btn01 {
            border: 0.26667vw solid #fff;
            color: #fff;
        }
    </style>
</head>
<body>
    <section id="code-error" class="modal">
        <div class="content">
            <div class="image"><img src="http://plate.id/sc/project/4133/custom/img/icon/icon-scan02.png" alt=""></div>
               <p style="text-align: center;font-size: 1.1em;font-family: HiraginoSans-W3;">
                このURLは既に認証に使用されています。</br>
                もう一度Smart Plateにタッチするか</br>
                ブラウザのタブに該当ページがないか</br>
                お確かめください。
                </p>

            <!-- <div class="btn"><a href="" class="btn01">戻る</a></div>
            <div style="margin-top: 2em;position: fixed;bottom: 4%; width: 50%;left: 25%;color: #969696;font-family: HiraginoSans-W3;"> -->
            </div>
        </div>
        </section>

    </body>
    <script>
        var callback=''
        $(document).ready(function(){

            $("#code-error").modal({
                escapeClose: false,
                clickClose: false,
                showClose: false
            });

        });
        function returnPage(){
            if(callback){
                location.href=callback;
            }else{
                history.back();
            }
        }
    </script>
</html>