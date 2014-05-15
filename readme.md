# Funny Face App

This is an example application implemented with the Laravel 4 framework to
showcase the usage of the [AWS SDK for PHP](https://github.com/aws/aws-sdk-php).
The funny face app displays funny faces ("/" route" that you can upload via the
form ("upload" route). This application is intended to be an example only, and
should not be deployed into a production environment.

To setup the app you will need to:

1. Run `composer.phar install` to install the dependencies including Laravel and
   the AWS SDK for PHP.
2. Create an [AWS credentials file](http://docs.aws.amazon.com/aws-sdk-php/guide/latest/credentials.html#using-the-aws-credentials-file-and-credential-profiles).
3. Run `php artisan funnyface:setup` to create an S3 bucket and DynamoDB table.

The you can use `php artisan serve` to run the app locally, or you can deploy
to [AWS Elastic Beanstalk](https://aws.amazon.com/elasticbeanstalk/).

This application was used as an example in the
[AWS for Artisans](https://joind.in/talk/view/11330) presentation at
[Laracon 2014](https://conference.laravel.com/).
