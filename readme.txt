=== Blue Network Plugins! ===
Contributors: r-a-y
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6U36PVEZB4BAE
Tags: 3.0, multisite, network, network admin, super admin, plugins
Requires at least: 3.0
Tested up to: 3.2+
Stable tag: 1.1

Separates active network plugins from inactive ones on the Wordpress network plugins page. Requires Wordpress Multisite.

== Description ==

This plugin makes things a little easier for network administrators managing their network plugins.

How so?  Glad you asked! Active network plugins are separated from inactive ones on the Wordpress network plugins page.

Need a visual?  Check out [this screenshot](http://i46.tinypic.com/6gv32g.png).

For WP 3.1+, if you're on a regular admin plugins page, you'll see a small blurb with an easy, direct link to your network plugins page.

If you're **not** using [network mode](http://codex.wordpress.org/Create_A_Network), you *don't* need this plugin.


== Installation ==

#### This plugin requires Wordpress to be in [network mode](http://codex.wordpress.org/Create_A_Network) ####

1. Download and install the plugin.
1. Navigate to your Network Admin plugins page (usually /wp-admin/network/plugins.php) and activate the plugin. (For WP 3.0, navigate to your regular plugins page and activate.)

I'd recommend putting `blue-network-plugins.php` in /wp-content/mu-plugins/ so all your sites can take advantage of this plugin without manually activating.


== Screenshots ==

A screenshot can be found [here](http://i46.tinypic.com/6gv32g.png).


== Frequently Asked Questions ==

#### Why blue? ####

The plugin was originally inspired by Wordpress MU, which used a blue color to identify which plugins were activated network-wide.  Also, in MU, network plugins were also neatly separated from regular site plugins.

When Wordpress 3.0 came about, all plugins were merged together into the regular plugin list.  This made things hard to administrate and hence the genesis of this plugin!

So to answer the question, the blue color is a throwback and tip of the hat to the ol' WPMU days.

The original plugin description is below:

`Wordpress 3.0 is cool like Thelonius Monk, but for super administrators coming from Wordpress MU, if you head on over to the plugins page, you'll notice something missing.

Network (or sitewide) plugins are now merged into the entire plugins list!

If you're like me and missed how your network plugins were listed at the very top with the uber-cool blue color, then you'll love this plugin!`


#### Is this plugin even necessary when I'm using WP 3.1+? ####

Good question!  I initially wrote this plugin for WP 3.0 when it was hard to distinguish between a regular plugin and a network plugin.

WP 3.1+ now offers two screens to administrate plugins - one for regular site admin use and another for your network.  It's a little better now, but this plugin is still useful from a usability perspective.  Give it a shot and you'll see! :)


== Recommendations ==

Check out the companion plugin, [Green Active Plugins!](http://wordpress.org/extend/plugins/green-active-plugins/).

They go well together like coffee and doughnuts!


== Changelog ==

= 1.1 =
* Rewrote the plugin for compatibility with WP 3.1+.
* Added localization support.

= 1.0 =
* First version!