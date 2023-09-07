=== WP Simple Related Posts ===
Contributors: horike,amimotoami,webnist,wokamoto,gatespace,mt8biz
Tags:  related posts,related
Requires at least: 3.8.1
Tested up to: 6.3.1
Stable tag: 1.6.1

Related Posts plugin. It's flexible and fast and simple.

== Description ==
*Related posts based on categories, tags.
*Can be replaced and sort of related posts at admin screen.
*I can be added as an add-on another related posts algorithm.

You can make addon to use [Addon Template](https://github.com/horike37/wp-simple-related-posts-addon-template/).

You can use JSON REST API Endpoint. Require plugin [JSON REST API](https://wordpress.org/plugins/json-rest-api/)
`http://example.com/wp-json/posts/%post_id%/sirp_related/`
`http://example.com/wp-json/sirp_related/%post_id%/`
`http://example.com/wp-json/sirp_related/%post_id%/?filter[num]=5`

= Translators =
* Japanese(ja) - [Horike Takahiro](http://profiles.wordpress.org/horike)
* Thai(th_TH) - [TG Knowledge](http://www.xn--12cg1cxchd0a2gzc1c5d5a.com)

= Contributors =
- @[ShinichiN](https://profiles.wordpress.org/shinichin)
- @[Webnist](https://profiles.wordpress.org/webnist)
- @[mt8biz](https://profiles.wordpress.org/mt8biz)

== Installation ==
1. Upload `simple-related-posts` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==
1. post screen.
2. option page.
3. sigle page.

== Changelog ==
= 1.0 =
* first release. 
= 1.2.2 =
* Add thumbnail image
= 1.2.3 =
* Add Related Posts title
= 1.2.4 =
* Related Posts addon with [Addon Template](https://github.com/horike37/wp-simple-related-posts-addon-template/)
= 1.3 =
* fixed bug on based tag and category
* add pot file
= 1.3.1 =
* Thai support
= 1.4 =
* Edit CSS on Admin panel.
* With a function to initialize the Related Posts.
= 1.4.1 =
* Bug fix
= 1.4.2 =
* Bug fix
= 1.4.3 =
* Bug fix
= 1.4.4 =
* Bug fix
= 1.5 =
* Add Json Rest API Endpoint
* Add Widget
= 1.5.1 =
* Bug fix
= 1.5.2 =
* stop save when running AUTO SAVE
= 1.5.3 =
* Show only posts publish status
= 1.5.4 =
* Exclude posts password protected
= 1.5.5 =
* Bug fix
= 1.5.6 =
* fix Skip processing at wp-cron, such as when publishing a future post
= 1.5.7 =
* Fixed warning errors
= 1.5.8 =
* Fixed a bug that caused metadata to be deleted in Quick Edit.
= 1.5.9 =
* Support for Block Editor
= 1.6.0 =
* Added support for manual addition when posts are in draft status.
= 1.6.1 =
* Changed to always get post ID as an array.
