=== Plugin Name ===
Contributors: 10CentMail
Tags: 10CentMail, Email Marketing, Amazon SES, email
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 2.1.50
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

10CentMail Subscription Management and Analytics plugin for Wordpress.

== Description ==
The 10CentMail Wordpress plugin provides subscription management and analytics data to the 10CentMail
desktop application.

This plugin is required for 10CentMail to provide subscribe and unsubscribe functionality as well as
open and click tracking to the application. Install this plugin on any public facing website that will
be used as a central point of customer interaction.

== Installation ==
1. Unzip tencentmail.zip to a folder. The zip file contains a folder named `tencentmail`.
2. Upload the `tencentmail` folder, in toto, to the `/wp-content/plugins` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Within the 10CentMail Settings, on the Base Settings tab, enter the 10CentMail key from the 10CentMail application.
5. Fill in other relevant information such as Company Name, Support Email Address, etc.
6. Within the 10CentMail application, sync your website to activate the plugin.
7. Within the 10CentMail Settings, on the Contact List Settings tab, select a Contact List and configure the text and response emails sent to Subscribers.
8. Within the 10CentMail Settings, on the Shortcode Generator tab, select a Contact List and configure the Short Code Options. Then Copy the Shortcode and paste it into a Page within Wordpress.

== Screenshots ==
1. Basic Settings
2. Contact List Settings
3. WordPress ShortCode Generator

== Frequently Asked Questions ==
1. What is 10CentMail

10CentMail is an email marketing and analytics application that sends email via your chosen email carrier such as Amazon SES. 10CentMail frees you from monthly subscriptions, arbitrary email marketing rules, and most importantly, keeps your precious contact lists and customer data under your control.

== Changelog ==
= 2.0.0 =
- fixed Custom Field displays when not selected in the tencentmail_subscribe_form shortcode

= 2.0.2 =
- added redirect_url attribute to the tencentmail_subscribe_form

= 2.0.4 =
- initial public release via WordPress.org

= 2.0.5 =
- url forwarding will check for a valid scheme on the forwarding url. If none is found http will be added automatically.

= 2.1.0 =
- added support for WordPress MultiSite, aka WPMU

== Upgrade Notice ==
= 2.0.5 =
This version enables url forwarding even if you leave out the http scheme.

= 2.1.0 =
This version is compatible with and works well on both WordPress and WordPress MultiSite

= 2.1.36
Important fix for all WordPress sites, including MultiSite

= 2.1.45
Fix for WordPress sites that use symlinks

= 2.1.48
Updated for WordPress MultiSite and WordPress sites with or without symlinks, on sites that use arbitrary plugin names

= 2.1.50 =
Fix for subscribe page when a subscriber subscribes to a list more than once.