# WP Instagram

Full of handy features for developers and non-developers alike, WP Instagram is the easiest way to integrate Instagram with WordPress.

*Note:* At this point the plugin is not fit for public consumption as it's more infrastructure than anything else.

## Installation

### Development version

If you're installing WP Instagram via the git repository you'll need to initialize its dependencies. Most of the integration with Instagram is handled through [Christian Metz's Instagram PHP API class](https://github.com/cosenary/Instagram-PHP-API). To get the plugin running simply `cd` into the plugin directory and run `git submodule init` to download Christian's class into lib/Instagram-PHP-API to be used by the plugin.

## Roadmap

### 1.0

* Shortcode embeds
* Instagram feed widget
* Caching via WordPress transients
* WordPress.org readme.txt
* API documentation