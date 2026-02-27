# library Mailer

The library Mailer is a software development kit (library) package designed to provide a convenient and efficient way to integrate email functionality into budget control applications.

## Features

- **Easy Integration**: The library Mailer offers a simple and straightforward integration process, allowing developers to quickly incorporate email sending capabilities into their applications.

- **Flexible Configuration**: With the library Mailer, you have the flexibility to configure various email settings, such as the SMTP server, port, authentication credentials, and more.

- **Multiple Email Providers**: The library  supports popular email providers, including Gmail, Outlook, and SendGrid, ensuring compatibility with a wide range of email services.

- **Template Support**: Simplify your email creation process by utilizing pre-defined email templates. The library Mailer provides a template engine that allows you to customize and personalize your emails effortlessly.

## Getting Started

To get started with the library Mailer, follow these steps:

1. Install the library Mailer package using your preferred package manager:

## Example
``
$data = [
    "name" => "name"
];
$bodyemail = new Mail($data);
$attachment = [
    [
        'path' => ...,
        'name' => ...
    ]
];

$dsn = new Dsn(env('MAIL_SCHEME', 'mailhog'), env('MAIL_HOST', 'mailhog'), '', '');
$mailer = new EmailService($dsn);
$mailer->sendEmail(
    $to,
    $subject,
    $bodyemail,
    $attachment,
    $cc
);

``
