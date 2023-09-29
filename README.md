# Fast META Wordpress plugin

Add META Title/Description in Wordpress without the Yoast bloat. Simple, clean UI. Works with posts/pages/tax/categories/custom types.

![alt text](https://github.com/seowner/fast-meta/blob/main/preview.jpg?raw=true)

If you're <strong>not migrating</strong> from Yoast, disregard the steps below, just install and activate.

# Migrating from Yoast

Please backup your database before doing this process. I've migrated many sites and never had a problem, but I'm not responsible if something happens to your site.

To use the Migration plugin you <strong>MUST</strong> leave Yoast enabled until the migration is complete. The plugin uses some of Yoast's functions to do the migration.

Process of migrating from Yoast:

1) <strong>Backup your database please.</strong>
2) Make sure Yoast is still enabled.
3) Install Fast META & Migration plugin.
4) Activate Migration plugin and go to the admin page, click button and wait until it finishes.
5) Disable Yoast.
6) Disable Migration plugin and remove it.
7) Check your posts/pages/tax (backend and frontend) to make sure everything worked.
8) Click on any admin page and watch how much faster it is - maybe even your frontend too ;)

# Breadcrumbs

To use breadcrumbs, go to the settings page, click enable, and add this to your theme files where you want them to be displayed.

```
<?php
if ( function_exists('fast_breadcrumb') ) {
  fast_breadcrumb( '<p id="breadcrumbs">','</p>' );
}
?>
```

I don't intend on doing any form of support for this. I use it on all of my sites, and it works great... but results may vary.

# Links

Follow me on Twitter/X at <a href="https://twitter.com/tehseowner" target="_blank">@tehseowner</a>

Check out my other plugins on <a href="https://www.ocscripts.com/" target="_blank">OCScripts.com</a>
