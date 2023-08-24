WP Simple Related Posts
====================
Welcome to the official repository for Simple Related Posts WordPress plugin.
The latest stable version is available from the [WordPress Plugin Directory](https://wordpress.org/plugins/wp-simple-related-posts/).

- Related posts based on categories, tags.
- Can be replaced and sort of related posts at admin screen.
- I can be added as an add-on another related posts algorithm.

You can make addon to use [Addon Template](https://github.com/horike37/wp-simple-related-posts-addon-template/).

# Installation
1. Upload `wp-simple-related-posts` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

# How to use API
`sirp_get_related_posts_id($display_num = '')`
- @param $display_num int Number of acquisitions.
- @return array Related Posts IDs.


`sirp_get_related_posts_id_api($display_num = '', $post_id)`
- @param $display_num int Number of acquisitions.
- @param $post_id int post_id.
- @return array Related Posts IDs.

# Contributors
- @[ShinichiNishikawa](https://github.com/ShinichiNishikawa)
- @[Webnist](https://github.com/Webnist)
- @[mt8biz](https://profiles.wordpress.org/mt8biz)
- @[marushu](https://profiles.wordpress.org/marushu/)
