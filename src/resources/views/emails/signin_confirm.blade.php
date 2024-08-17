<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="format-detection" content="telephone=no,address=no,email=no,date=no">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>メール</title>
  <style type="text/css"><!-- body {color: #1a1a1a;  margin: 0;  padding: 0;  font-family: HiraginoSans-W6, 'メイリオ', Meiryo, Osaka, 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;  -webkit-font-smoothing: antialiased;  word-wrap: break-word; } img { max-width:100%;border:0;line-height:0; } a {color:#666666; text-decoration:none; } #ticket-icon::after {border: 1px dashed white; content: ""; display: block; margin-top: 15px;} --></style>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#ffffff" style="background:#ffffff;font-family: 'メイリオ', Meiryo, Osaka, 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;">
<div style="background:#ffffff;">
  <!-- プリヘッダーここから -->
  <table border="0" align="center"  height="20px" cellpadding="0" cellspacing="0"
         style="max-width:340px;width:60%;padding: 24px 0 22px 0;
  margin-bottom: 10px;-moz-background-size:100% 100%;
  background-size:contain;
  background-repeat: no-repeat;
  background-position: center;
  background-image:url('https://plate.id/$imageUrl') !important;">
    <tr>
      <td></td>
    </tr>
  </table>
  <!-- プリヘッダーここまで -->
  <!-- headerここから -->
  <table border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:540px;width:100%;padding: 24px 22px 0 22px;margin-bottom: 0px;">
    <tr>
      <td colspan="3" display:inline-block text-align:left style="font-family: HiraginoSans-W6;font-size: 20px;line-height: 1;">
        <p>{{ $text }}</p>
      </td>
    </tr>
  </table>
  <!-- headerここまで -->
  <!-- 領収書ここから -->
  <table border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:600px;">

    <tr>
      <td width="30" style="width:5%;font-size:0;"></td>
      <td width="540" style="width:90%;">
        <table>
          <tr>
            <td width="70"></td>
            <td width="398">
              <table border="0" cellpadding="0" cellspacing="0" style="width:100%;margin:24px 0 21px;">
                <tr>
                  <td>
                    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin: 21px 0;">
                      <tr >
                        <td colspan="2" align="center" style="padding-top: 20px">
                        <table border="0" cellpadding="0" cellspacing="0" style="background-color: black;border-radius:25px; border: 8px; border:1px solid #ffffff;">
                          <tr>
                            <td width="300" height="50" align="center">
                              <a href={{ $url }} style="text-align: center;display:block;padding:0;color:#ffffff;font-size:14px;font-weight:bold;line-height:7px;text-decoration:none;"><p>サインインする</p><p style="font-size: 10px;">sign in</p></a></td>
                          </tr>
                        </table>
                        </td>
                      </tr>
                      <tr display:inline-block text-align:center>
                        <td colspan="2" style="padding-top: 7px;">
                          <a href="{{ $url }}" style="border-bottom: 1px solid;">{{ $urlText }}</a> 
                        </td>
                      </tr>
                      <tr display:inline-block text-align:left>
                        <td colspan="2" style=" padding-top: 10px;">
                            心当たりがない場合はこのメールを破棄してください。</br>
                            Please discard this email if you have no idea.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td align="left" style="font-size: 12px;line-height: 1.67;"></td>
                </tr>
              </table>
            </td>
            <td width="70"></td>
          </tr>
        </table>
      </td>
      <td width="30" style="width:5%;font-size:0;"></td>
    </tr>

  </table>
  <div style="font-size: 12px;line-height: 1.67; margin: auto;">
    <p style="margin-inline: auto; max-inline-size: max-content;">
      このメールは Aquabit Spirals Inc. が提供するスマートプレートから送信されています。</br>
      *This email is sent from SmartPlate provided by Aquabit Spirals Inc.
    </p>
  </div>
  
  <!-- footerここから -->
  <div style="height:70px;"></div>
  <!-- footerここまで -->
</div>
</body>
</html>