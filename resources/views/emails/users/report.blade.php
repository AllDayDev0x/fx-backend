<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light dark" />
    <meta name="supported-color-schemes" content="light dark" />
    <title></title>
    <style type="text/css" rel="stylesheet" media="all">
        /* Base ------------------------------ */

        @import url("https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap");

        body {
            width: 100% !important;
            height: 100%;
            margin: 0;
            -webkit-text-size-adjust: none;
        }

        a {
            color: #3869D4;
        }

        a img {
            border: none;
        }

        td {
            word-break: break-word;
        }

        .preheader {
            display: none !important;
            visibility: hidden;
            mso-hide: all;
            font-size: 1px;
            line-height: 1px;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
        }

        /* Type ------------------------------ */

        body,
        td,
        th {
            font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
        }

        h1 {
            margin-top: 0;
            color: #333333;
            font-size: 22px;
            font-weight: bold;
            text-align: left;
        }

        h2 {
            margin-top: 0;
            color: #333333;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
        }

        h3 {
            margin-top: 0;
            color: #333333;
            font-size: 14px;
            font-weight: bold;
            text-align: left;
        }

        td,
        th {
            font-size: 16px;
        }

        p,
        ul,
        ol,
        blockquote {
            margin: .4em 0 1.1875em;
            font-size: 16px;
            line-height: 1.625;
        }

        p.sub {
            font-size: 13px;
        }

        /* Utilities ------------------------------ */

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .align-center {
            text-align: center;
        }

        /* Buttons ------------------------------ */

        .button {
            background-color: #3869D4;
            border-top: 10px solid #3869D4;
            border-right: 18px solid #3869D4;
            border-bottom: 10px solid #3869D4;
            border-left: 18px solid #3869D4;
            display: inline-block;
            color: #FFF;
            text-decoration: none;
            border-radius: 3px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
            -webkit-text-size-adjust: none;
            box-sizing: border-box;
        }

        .button--green {
            background-color: #22BC66;
            border-top: 10px solid #22BC66;
            border-right: 18px solid #22BC66;
            border-bottom: 10px solid #22BC66;
            border-left: 18px solid #22BC66;
        }

        .button--red {
            background-color: #FF6136;
            border-top: 10px solid #FF6136;
            border-right: 18px solid #FF6136;
            border-bottom: 10px solid #FF6136;
            border-left: 18px solid #FF6136;
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
                text-align: center !important;
            }
        }

        /* Attribute list ------------------------------ */

        .attributes {
            margin: 0 0 21px;
        }

        .attributes_content {
            background-color: #F4F4F7;
            padding: 16px;
        }

        .attributes_item {
            padding: 0;
            padding-bottom:20px !important;
        }

        /* Related Items ------------------------------ */

        .related {
            width: 100%;
            margin: 0;
            padding: 25px 0 0 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
        }

        .related_item {
            padding: 10px 0;
            color: #CBCCCF;
            font-size: 15px;
            line-height: 18px;
        }

        .related_item-title {
            display: block;
            margin: .5em 0 0;
        }

        .related_item-thumb {
            display: block;
            padding-bottom: 10px;
        }

        .related_heading {
            border-top: 1px solid #CBCCCF;
            text-align: center;
            padding: 25px 0 10px;
        }

        /* Discount Code ------------------------------ */

        .discount {
            width: 100%;
            margin: 0;
            padding: 24px;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            background-color: #F4F4F7;
            border: 2px dashed #CBCCCF;
        }

        .discount_heading {
            text-align: center;
        }

        .discount_body {
            text-align: center;
            font-size: 15px;
        }

        /* Social Icons ------------------------------ */

        .social {
            width: auto;
        }

        .social td {
            padding: 0;
            width: auto;
        }

        .social_icon {
            height: 20px;
            margin: 0 8px 10px 8px;
            padding: 0;
        }

        /* Data table ------------------------------ */

        .purchase {
            width: 100%;
            margin: 0;
            padding: 35px 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
        }

        .purchase_content {
            width: 100%;
            margin: 0;
            padding: 25px 0 0 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
        }

        .purchase_item {
            padding: 10px 0;
            color: #51545E;
            font-size: 15px;
            line-height: 18px;
        }

        .purchase_heading {
            padding-bottom: 8px;
            border-bottom: 1px solid #EAEAEC;
        }

        .purchase_heading p {
            margin: 0;
            color: #85878E;
            font-size: 12px;
        }

        .purchase_footer {
            padding-top: 15px;
            border-top: 1px solid #EAEAEC;
        }

        .purchase_total {
            margin: 0;
            text-align: right;
            font-weight: bold;
            color: #333333;
        }

        .purchase_total--label {
            padding: 0 15px 0 0;
        }

        body {
            background-color: #FFF;
            color: #333;
        }

        p {
            color: #333;
        }

        .email-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
        }

        .email-content {
            width: 100%;
            margin: 0;
            padding: 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
        }

        /* Masthead ----------------------- */

        .email-masthead {
            padding: 25px 0;
            text-align: center;
        }

        .email-masthead_logo {
            width: 94px;
        }

        .email-masthead_name {
            font-size: 16px;
            font-weight: bold;
            color: #A8AAAF;
            text-decoration: none;
            text-shadow: 0 1px 0 white;
        }

        /* Body ------------------------------ */

        .email-body {
            width: 100%;
            margin: 0;
            padding: 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
        }

        .email-body_inner {
            width: 570px;
            margin: 0 auto;
            padding: 0;
            -premailer-width: 570px;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
        }

        .email-footer {
            width: 570px;
            margin: 0 auto;
            padding: 0;
            -premailer-width: 570px;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            text-align: center;
        }

        .email-footer p {
            color: #A8AAAF;
        }

        .body-action {
            width: 100%;
            margin: 30px auto;
            padding: 0;
            -premailer-width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            text-align: center;
        }

        .body-sub {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #EAEAEC;
        }

        .content-cell {
            padding: 35px;
        }

        /*Media Queries ------------------------------ */

        @media only screen and (max-width: 600px) {

            .email-body_inner,
            .email-footer {
                width: 100% !important;
            }
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #333333 !important;
                color: #FFF !important;
            }

            p,
            ul,
            ol,
            blockquote,
            h1,
            h2,
            h3 {
                color: #FFF !important;
            }

            .attributes_content,
            .discount {
                background-color: #222 !important;
                padding-bottom:20px !important;
            }

            .email-masthead_name {
                text-shadow: none !important;
            }
        }

        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }
    </style>
    <!--[if mso]>
    <style type="text/css">
      .f-fallback  {
        font-family: Arial, sans-serif;
      }
    </style>
  <![endif]-->
</head>

<body>
    <span class="preheader"></span>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="email-masthead">

                        </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                        <td class="email-body" width="570" cellpadding="0" cellspacing="0">
                            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                        <div class="f-fallback">
                                            <h1>{{tr('hi')}} {{$data['name']}},</h1>
                                            <p>{{ tr('report_tag', Setting::get('site_name')) }}</p>
                                            <table class="attributes" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                                <tr>
                                                    <td class="attributes_content">
                                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- Action -->
                                            <table class="purchase" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td colspan="2">
                                                        <table class="purchase_content" width="100%" cellpadding="0" cellspacing="0">

                                                            <tr>
                                                                <th width="100%" class="purchase_item"><span class="f-fallback">{{$data['message']}}</span></th>
                                                            </tr>

                                                        </table>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <table class="purchase_content" width="100%" cellpadding="0" cellspacing="0">

                                                            <tr>
                                                                <td width="80%" class="purchase_item"><span class="f-fallback">{{tr('total_posts')}}</span></td>
                                                                <td class="align-right" width="20%" class="purchase_item"><span class="f-fallback">{{$data['main']['posts']}}</span></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="80%" class="purchase_item"><span class="f-fallback">{{tr('total_tips')}}</span></td>
                                                                <td class="align-right" width="20%" class="purchase_item"><span class="f-fallback">{{$data['main']['tip_payment']}}</span></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="80%" class="purchase_item"><span class="f-fallback">{{tr('post_payments')}}</span></td>
                                                                <td class="align-right" width="20%" class="purchase_item"><span class="f-fallback">{{$data['main']['post_payment']}}</span></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="80%" class="purchase_item"><span class="f-fallback">{{tr('subscription_payments')}}</span></td>
                                                                <td class="align-right" width="20%" class="purchase_item"><span class="f-fallback">{{$data['main']['subscription_payment']}}</span></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="80%" class="purchase_item"><span class="f-fallback">{{tr('total_likes')}}</span></td>
                                                                <td class="align-right" width="20%" class="purchase_item"><span class="f-fallback">{{$data['main']['likes']}}</span></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="80%" class="purchase_item"><span class="f-fallback">{{tr('followers')}}</span></td>
                                                                <td class="align-right" width="20%" class="purchase_item"><span class="f-fallback">{{$data['main']['followers']}}</span></td>
                                                            </tr>

                                                            <tr>
                                                                <td width="80%" class="purchase_item"><span class="f-fallback">{{tr('followings')}}</span></td>
                                                                <td class="align-right" width="20%" class="purchase_item"><span class="f-fallback">{{$data['main']['followings']}}</span></td>
                                                            </tr>

                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                                <tr>
                                                    <td align="center">
                                                        <!-- Border based button
           https://litmus.com/blog/a-guide-to-bulletproof-buttons-in-email-design -->
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                                            <tr>
                                                                <td align="center">
                                                                    <a style="background-color: #4CAF50;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;" href="{{Setting::get('frontend_url')}}">{{tr('visit_website')}}</a>

                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>

                                            <!-- Sub copy -->

                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                        <div style="font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;">
                                            {{tr('best_regards')}} ,<br><br>{{Setting::get('site_name')}}<br>{{Setting::get('tag_name')}}<br>

                                        </div>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
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
            </td>
        </tr>
    </table>
</body>

</html>