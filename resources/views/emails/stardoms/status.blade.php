<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>

    <title>{{Setting::get('site_name')}}</title>
   
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
   
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass * {
            line-height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block;
            margin: 13px 0;
        }
    </style>
   
    <style type="text/css">
        @media only screen and (max-width:480px) {
            @-ms-viewport {
                width: 320px;
            }
            @viewport {
                width: 320px;
            }
        }
    </style>
    
    <style type="text/css">
        @media only screen and (min-width:480px) {
            .mj-column-per-100 {
                width: 100% !important;
            }
        }
    </style>

</head>

<body style="background-color:#f9f9f9;">


    <div style="background-color:#f9f9f9;">


        <div style="background:#f9f9f9;background-color:#f9f9f9;Margin:0px auto;max-width:600px;">

            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#f9f9f9;background-color:#f9f9f9;width:100%;">
                <tbody>

                    <tr>

                        <td style="border-bottom:#333957 solid 5px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
                           
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <div style="background:#fff;background-color:#fff;Margin:0px auto;max-width:600px;">

            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#fff;background-color:#fff;width:100%;">
                <tbody>
                    <tr>
                        <td style="border:#dddddd solid 1px;border-top:0px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
                    
                            <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;">

                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:bottom;" width="100%">

                                    <tr>
                                        <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                                <tbody>
                                                    <tr>
                                                        <td style="width:64px;">

                                                            <img height="auto" src="{{Setting::get('site_logo')}}" style="border:0;display:block;outline:none;text-decoration:none;width:100%;" width="64" />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                            <div style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;">
                                                <br></br>

                                                <h3>{{tr('hello')}} {{$data['name']}}</h3>  

                                                @if($data['status'] ==  tr('approved'))

                                                    {{tr('your_account_has_been_approved_by_admin')}}

                                                @else

                                                    {{tr('your_account_has_been_declined_by_admin')}} 
                                                    
                                                @endif
                                                
                                            </div>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                            <div style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;">
                                                Best regards,<br><br>{{Setting::get('site_name')}}<br>{{Setting::get('tag_name')}}<br>
                                                
                                            </div>

                                        </td>
                                    </tr>

                                </table>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <div style="Margin:0px auto;max-width:600px;">

            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;">
                         
                            <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;">

                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align:bottom;padding:0;">

                                                <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">

                                                    <tr>
                                                        <td align="center" style="font-size:0px;padding:0;word-break:break-word;">

                                                            <div style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:12px;font-weight:300;line-height:1;text-align:center;color:#575757;">
                                                               {{tr('you_are_receiving_mails_from')}} {{Setting::get('site_name')}}
                                                            </div>

                                                        </td>
                                                    </tr>

                                                </table>

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

            <table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top;">
            <tr style="border-collapse:collapse;">
                <td style="padding:0;Margin:0;background-color:#F7F7F7;" bgcolor="#f7f7f7" align="center">
                    <table class="es-footer-body" width="600" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#F7F7F7;">
                        <tr style="border-collapse:collapse;">
                            <td align="left" style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;">
                                <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                    <tr style="border-collapse:collapse;">
                                        <td width="560" valign="top" align="center" style="padding:0;Margin:0;">
                                            <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                                <tr style="border-collapse:collapse;">
                                                    <td align="center" style="padding:0;Margin:0;padding-bottom:5px;">
                                                        <h3 style="Margin:0;line-height:30px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:20px;font-style:normal;font-weight:normal;color:#333333;">Let's get social media</h3>
                                                    </td>
                                                </tr>
                                                <tr style="border-collapse:collapse;">
                                                    <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0;">
                                                        <table class="es-table-not-adapt es-social" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                                            <tr style="border-collapse:collapse;">
                                                                @if(Setting::get('facebook_link'))
                                                                <td valign="top" align="center" style="padding:0;Margin:0;padding-right:20px;"><a href="{{Setting::get('facebook_link')}}" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Facebook" src="{{asset('images/facebook-logo-black.png')}}" alt="Fb" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a></td>
                                                                @endif
                                                                @if(Setting::get('youtube_link'))
                                                                <td valign="top" align="center" style="padding:0;Margin:0;padding-right:20px;"><a href="{{Setting::get('youtube_link')}}" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Youtube" src="{{asset('images/youtube-logo-black.png')}}" alt="Yt" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a></td>
                                                                @endif
                                                                @if(Setting::get('pinterest_link'))
                                                                <td valign="top" align="center" style="padding:0;Margin:0;padding-right:20px;"><a href="{{Setting::get('pinterest_link')}}" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Pinterest" src="{{asset('images/pinterest-logo-black.png')}}" alt="P" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a></td>
                                                                @endif
                                                                @if(Setting::get('instagram_link'))
                                                                <td valign="top" align="center" style="padding:0;Margin:0;padding-right:20px;"><a href="{{Setting::get('instagram_link')}}" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Instagram" src="{{asset('images/instagram-logo-black.png')}}" alt="Ig" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a></td>
                                                                @endif
                                                                @if(Setting::get('twitter_link'))
                                                                <td valign="top" align="center" style="padding:0;Margin:0;"><a href="{{Setting::get('twitter_link')}}" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Twitter" src="{{asset('images/twitter-logo-black.png')}}" alt="Tw" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a></td>
                                                                @endif
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
          </table>

        </div>

    </div>

</body>

</html>