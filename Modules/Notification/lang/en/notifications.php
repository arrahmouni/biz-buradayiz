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
            'long_template' => '
                <div style="font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
                    <!-- Title with red accent bar -->
                    <div style="margin-bottom: 28px;">
                        <h2 style="color: #1F2937; font-size: 22px; font-weight: 700; margin: 0 0 10px 0;">Reset Your Password</h2>
                        <div style="width: 50px; height: 3px; background-color: #DC2626; border-radius: 2px;"></div>
                    </div>

                    <!-- Greeting -->
                    <p style="color: #374151; font-size: 15px; line-height: 1.5; margin: 0 0 16px 0;">
                        <strong>Hello {{username}},</strong>
                    </p>
                    <p style="color: #4B5563; font-size: 15px; line-height: 1.5; margin: 0 0 28px 0;">
                        We received a request to reset the password for your account. To create a new password, please click the button below.
                    </p>

                    <!-- Reset button -->
                    <div style="text-align: center; margin: 32px 0;">
                        <a href="{{reset_link}}" style="display: inline-block; background-color: #DC2626; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 14px; letter-spacing: 0.3px;">Reset Your Password</a>
                    </div>

                    <!-- Info box -->
                    <div style="background-color: #F8FAFC; padding: 20px 24px; border-radius: 8px; border-left: 3px solid #DC2626; margin: 32px 0 16px;">
                        <p style="margin: 0 0 12px 0; color: #4B5563; font-size: 14px; line-height: 1.5;">
                            <strong>Did not request this?</strong> You can safely ignore this email. No changes will be made to your account.
                        </p>
                        <p style="margin: 0; color: #6B7280; font-size: 13px; line-height: 1.5;">
                            <strong>Link expiration:</strong> This password reset link will expire 60 minutes after this email was sent.
                        </p>
                    </div>

                    <!-- Help text with plain URL fallback -->
                    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #E5E7EB;">
                        <p style="color: #9CA3AF; font-size: 12px; line-height: 1.5; margin: 0 0 8px 0;">
                            If the button above does not work, copy and paste the following link into your browser:
                        </p>
                        <p style="margin: 0;">
                            <a href="{{reset_link}}" style="color: #DC2626; font-size: 12px; word-break: break-all; text-decoration: underline;">{{reset_link}}</a>
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
