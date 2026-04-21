<?php

return [
    'datatable' => asset('lang/en/datatable.json'),
    'base_columns' => [
        'id' => 'ID',
        'image' => 'Image',
        'name' => 'Name',
        'slug' => 'Slug',
        'username' => 'Username',
        'gender' => 'Gender',
        'title' => 'Title',
        'type' => 'Type',
        'status' => 'Status',
        'description' => 'Description',
        'email' => 'Email',
        'phone_number' => 'Phone Number',
        'central_phone' => 'Central phone',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'deleted_at' => 'Deleted At',
        'created_by' => 'Created By',
        'actions' => 'Actions',
        'user_agent' => 'User Agent',
        'ip_address' => 'IP Address',
    ],
    'buttons' => [
        'export' => 'Export',
        'refresh' => 'Refresh',
        'add_new' => 'Add New',
        'select_action' => 'Select Action',
    ],
    'roles' => [
        'list_title' => 'Roles List',
        'columns' => [
            'code' => 'Code',
            'permissions' => 'Permissions',
        ],
    ],
    'permissions' => [
        'list_title' => 'Permissions List',
        'columns' => [
            'code' => 'Code',
        ],
    ],
    'countries' => [
        'list_title' => 'Countries List',
        'columns' => [
            'native_name' => 'Native Name',
            'phone_code' => 'Phone Code',
            'currency' => 'Currency',
            'states_count' => 'States Count',
            'cities_count' => 'Cities Count',
        ],
    ],
    'admins' => [
        'list_title' => 'Admins List',
        'columns' => [
            'user' => 'User',
            'role' => 'Role',
            'lang' => 'Language',
            'last_login_at' => 'Last Login At',
            'joined_date' => 'Joined Date',
        ],
    ],
    'content_categories' => [
        'list_title' => 'Content Categories List',
        'columns' => [
            'slug' => 'Slug',
            'parent' => 'Parent Category',
            'can_be_deleted' => 'Can Be Deleted',
        ],
    ],
    'contents' => [
        'list_title' => 'Contents List',
        'columns' => [
            'category' => 'Category',
            'updated_by' => 'Updated By',
            'published_at' => 'Published At',
        ],
        'sliders' => [
            'list_title' => 'Sliders List',
            'columns' => [
                'placement' => 'Placement of Slider',
            ],
        ],
        'blogs' => [
            'list_title' => 'Blogs List',
        ],
        'pages' => [
            'list_title' => 'Pages List',
        ],
        'faqs' => [
            'list_title' => 'FAQs List',
        ],
        'categories' => [
            'list_title' => 'Categories List',
        ],
        'brands' => [
            'list_title' => 'Brands List',
        ],
        'shapes' => [
            'list_title' => 'Shapes List',
        ],
        'types_of_tires' => [
            'list_title' => 'Types of Tires List',
        ],
        'colors' => [
            'list_title' => 'Colors List',
        ],
        'materials' => [
            'list_title' => 'Materials List',
        ],
        'proportions' => [
            'list_title' => 'Proportions List',
        ],
        'gender' => [
            'list_title' => 'Gender List',
        ],
        'home_page' => [
            'list_title' => 'Home Page List',
        ],
    ],
    'categories' => [
        'list_title' => 'Categories List',
        'columns' => [
            'parent' => 'Parent Category',
            'can_be_deleted' => 'Can Be Deleted',
        ],
    ],
    'users' => [
        'list_title' => 'Users List',
        'list_title_customers' => 'Customers List',
        'list_title_service_providers' => 'Service Providers List',
        'quick_filter_pending_approval' => 'Pending approval',
        'quick_filter_pending_approval_title' => 'Show service providers not yet approved',
        'quick_filter_pending_approval_option' => 'Pending approval',
        'filtered_results_count_label' => 'providers in these results',
        'columns' => [
            'service_type' => 'Service type',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'package' => 'Package',
            'ranking_score' => 'Rank score',
        ],
    ],
    'notification_templates' => [
        'list_title' => 'Notification Templates List',
        'columns' => [
            'priority' => 'Priority',
            'channels' => 'Channels',
            'variables' => 'Variables',
        ],
    ],
    'notifications' => [
        'list_title' => 'Notifications List',
        'columns' => [
            'recipient' => 'Recipient',
            'added_by' => 'Added By',
            'link' => 'Link',
            'sent_at' => 'Sent At',
        ],
    ],
    'contactuses' => [
        'list_title' => 'Contact Us Requests List',
        'columns' => [
            'message' => 'Message',
            'reply' => 'Reply',
            'submission_date' => 'Submission date',
        ],
    ],
    'subscribes' => [
        'list_title' => 'Subscriptions List',
        'columns' => [
            'is_active' => 'Is Active',
            'subscription_date' => 'Subscription Date',
        ],
    ],
    'content_tags' => [
        'list_title' => 'Content Tags List',
        'columns' => [
        ],
    ],
    'api_logs' => [
        'list_title' => 'Api Logs List',
        'columns' => [
            'service_name' => 'Service Name',
            'method' => 'Method',
            'endpoint' => 'Endpoint',
            'status_code' => 'Status Code',
            'error' => 'Error',
            'request' => 'Request',
            'response' => 'Response',
        ],
    ],
    'activity_logs' => [
        'list_title' => 'Activity Logs List',
        'columns' => [
            'user_made_action' => 'User Made Action',
            'user_type' => 'User Type',
            'event' => 'event',
            'old_values' => 'Old Values',
            'new_values' => 'New Values',
            'action_date' => 'Action Date',
        ],
    ],
    'services' => [
        'list_title' => 'Services List',
        'columns' => [
            'icon' => 'Icon',
            'name' => 'Name',
            'service_providers_count' => 'Service providers',
            'show_in_search_filters' => 'In search filters',
        ],
    ],
    'packages' => [
        'list_title' => 'Packages List',
        'columns' => [
            'name' => 'Name',
            'free_tier' => 'Tier',
            'popular' => 'Popular',
            'price' => 'Price',
            'billing_period' => 'Billing',
            'services' => 'Services',
            'connections_count' => 'Number of calls',
        ],
    ],
    'package_subscriptions' => [
        'list_title' => 'Package subscriptions',
        'quick_filter_awaiting_verification' => 'Pending payment approval',
        'quick_filter_awaiting_verification_title' => 'Show subscriptions waiting for payment verification',
        'columns' => [
            'user' => 'User',
            'created_at' => 'Subscription date',
            'package' => 'Package (ordered)',
            'ordered_price' => 'Price (ordered)',
            'status' => 'Subscription status',
            'payment_status' => 'Payment status',
            'payment_method' => 'Payment method',
            'starts_at' => 'Starts at',
            'paid_at' => 'Paid at',
        ],
    ],
    'verimor_call_events' => [
        'list_title' => 'Verimor call events',
        'columns' => [
            'created_at' => 'Event date',
            'call_uuid' => 'Call UUID',
            'event_type' => 'Event type',
            'direction' => 'Direction',
            'destination' => 'Destination (normalized)',
            'provider' => 'Service provider',
            'answered' => 'Answered',
            'consumed_quota' => 'Quota consumed',
            'subscription_id' => 'Subscription ID',
            'raw_payload' => 'Raw webhook payload (JSON)',
        ],
    ],
    'reviews' => [
        'list_title' => 'Reviews List',
        'rating_filter_option' => ':n / 5',
        'columns' => [
            'user' => 'User',
            'rating' => 'Rating',
            'comment' => 'Comment',
            'call_event' => 'Event UUID',
            'reviewer_display_name' => 'Reviewer name',
            'reviewer_phone' => 'Reviewer phone',
            'status' => 'Status',
            'verimor_call_events_list' => 'Verimor call events',
        ],
    ],
];
