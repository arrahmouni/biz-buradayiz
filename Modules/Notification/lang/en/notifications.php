<?php

return [
    'notifications'                                             => 'Notifications',
    'mark_all_as_read'                                          => 'Mark all as read',
    'notification_templates'                                    => [
        'welcome_in_our_platform'                               => [
            'title'                                             => 'Welcome in our platform',
            'description'                                       => 'This is a welcome message for the user',
            'short_template'                                    => 'Welcome {{username}} in our platform',
            'long_template'                                     => 'Welcome {{username}} in our platform',
        ],
        'forget_password'                                       => [
            'title'                                             => 'Reset password',
            'description'                                       => 'A password reset link is sent to the user email',
            'short_template'                                    => 'Reset your password',
            'long_template'                                     => '
                <div style="direction: ltr; text-align: left; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
                    <h3 style="color: #2d3748; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #667eea; font-size: 20px;">
                        🔐 Reset your password
                    </h3>

                    <p style="color: #4a5568; margin-bottom: 20px; line-height: 1.6;">
                        <strong>Hello {{username}},</strong><br>
                        We received a request to reset the password for your account. To set a new password, please click the button below.
                    </p>

                    <div style="text-align: center; margin: 25px 0;">
                        <a href="{{reset_link}}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 5px;">
                            🔑 Reset password
                        </a>
                    </div>

                    <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border-left: 4px solid #667eea; margin-bottom: 25px;">
                        <p style="margin: 0 0 10px 0; color: #4a5568; font-size: 14px; line-height: 1.6;">
                            ⚠️ If you did not request this, you can ignore this message and no changes will be made to your account.
                        </p>
                        <p style="margin: 0; color: #718096; font-size: 14px;">
                            ⏱️ Note: This link expires 60 minutes after this email was sent.
                        </p>
                    </div>
                </div>
            ',
        ],
        'priority'                                              => [
            'low'                                               => 'Low',
            'medium'                                            => 'Medium',
            'high'                                              => 'High',
            'default'                                           => 'Default',
        ],
    ],
    'statuses'                                                  => [
        'delivered'                                             => 'Delivered',
        'pending'                                               => 'Pending',
        'seen'                                                  => 'Seen',
        'failed'                                                => 'Failed',
        'read'                                                  => 'Read',
    ],
    'added_by'                                                  => [
        'system'                                                => 'System',
        'admin'                                                 => 'Admin',
    ],
];
