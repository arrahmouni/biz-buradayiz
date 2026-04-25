<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ getSetting(\Modules\Config\Constatnt::APP_NAME) }}</title>
    <style>
        /* Reset styles for email clients */
        body, table, td, p, a { margin: 0; padding: 0; border: 0; font-size: 100%; }
        body { background-color: #F3F4F6; font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; line-height: 1.5; }
        .container { max-width: 600px; width: 100%; margin: 0 auto; background-color: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #1F2937 0%, #111827 100%); padding: 32px 24px; text-align: center; }
        .content { padding: 40px 32px; background: #FFFFFF; }
        .footer { background: #F9FAFB; padding: 24px; text-align: center; border-top: 1px solid #E5E7EB; }
        .btn { display: inline-block; background-color: #DC2626; color: #FFFFFF; text-decoration: none; padding: 12px 28px; border-radius: 9999px; font-weight: 600; font-size: 14px; text-align: center; transition: all 0.2s; }
        .social-icons { margin-top: 20px; display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; }
        .social-icons a { display: inline-block; width: 32px; height: 32px; opacity: 0.8; transition: opacity 0.2s; }
        .social-icons img { width: 100%; height: auto; }
        @media only screen and (max-width: 600px) {
            .content { padding: 24px 20px; }
            .btn { display: block; width: 100%; text-align: center; box-sizing: border-box; }
        }
    </style>
</head>
<body style="background-color: #F3F4F6; margin: 0; padding: 20px 0; font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 0;">
                <!-- Main container -->
                <div class="container">
                    <!-- Header with logo -->
                    <div class="header">
                        <a href="{{ url('/') }}" target="_blank" style="display: inline-block; text-decoration: none;">
                            <img src="{{ getSetting(\Modules\Config\Constatnt::EMAIL_LOGO, asset('images/default/logos/app_logo.svg')) }}" alt="{{ getSetting(\Modules\Config\Constatnt::APP_NAME) }}" style="height: 48px; width: auto; max-width: 200px;">
                        </a>
                    </div>

                    <!-- Dynamic content -->
                    <div class="content">
                        @yield('content')
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <p style="margin: 0 0 8px; font-size: 13px; color: #6B7280;">
                            &copy; {{ date('Y') }} {{ getSetting(\Modules\Config\Constatnt::APP_NAME) }}. {{ __('admin::strings.all_rights_reserved') }}
                        </p>
                        <p style="margin: 0; font-size: 12px; color: #9CA3AF;">
                            {{ __('admin::strings.email_footer_registered_user_notice') }}
                        </p>

                        <!-- Social links -->
                        <div class="social-icons">
                            @if($facebook = getSetting(\Modules\Config\Constatnt::FACEBOOK))
                                <a href="{{ $facebook }}" target="_blank"><img src="{{ asset('modules/admin/metronic/demo/media/svg/social-logos/facebook.svg') }}" alt="Facebook"></a>
                            @endif
                            @if($twitter = getSetting(\Modules\Config\Constatnt::TWITTER))
                                <a href="{{ $twitter }}" target="_blank"><img src="{{ asset('modules/admin/metronic/demo/media/svg/social-logos/twitter.svg') }}" alt="Twitter"></a>
                            @endif
                            @if($linkedin = getSetting(\Modules\Config\Constatnt::LINKEDIN))
                                <a href="{{ $linkedin }}" target="_blank"><img src="{{ asset('modules/admin/metronic/demo/media/svg/social-logos/linkedin.svg') }}" alt="LinkedIn"></a>
                            @endif
                            @if($instagram = getSetting(\Modules\Config\Constatnt::INSTAGRAM))
                                <a href="{{ $instagram }}" target="_blank"><img src="{{ asset('modules/admin/metronic/demo/media/svg/social-logos/instagram.svg') }}" alt="Instagram"></a>
                            @endif
                            @if($youtube = getSetting(\Modules\Config\Constatnt::YOUTUBE))
                                <a href="{{ $youtube }}" target="_blank"><img src="{{ asset('modules/admin/metronic/demo/media/svg/social-logos/youtube.svg') }}" alt="YouTube"></a>
                            @endif
                            @if($tiktok = getSetting(\Modules\Config\Constatnt::TIKTOK))
                                <a href="{{ $tiktok }}" target="_blank"><img src="{{ asset('modules/admin/metronic/demo/media/svg/social-logos/tiktok.svg') }}" alt="TikTok"></a>
                            @endif
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
