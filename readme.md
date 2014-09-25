# Branch Updater for Composer


## Installation

Simply add it to your composer.json. Remember to add it first.

```json
"require": {
  "maxbucknell/composer-branch-updater": "0.1.~",
  // ...
}
```

Since it's not on Packagist yet, you'll need to tell Composer about it,
too:

```json
"repositories": [
  {
    "type": "vcs",
    "url": "git@github.com:maxbucknell/composer-branch-updater.git"
  },
  // ...
]
```

And you're done.


## Why?

In your project, you might have internal composer packages to separate
the concerns of your code. Most PHP frameworks have some notion of module
that roughly equates to the notion of a Composer package.

Before feature freeze and release candidates and production builds, etc.,
we reference these internal modules at dev-master, and any commit to
master on any repository we have included triggers a build on our CI
server.

If you want to reference your internal modules on a per repository basis,
then that's fine, but it's also slow. You might want to set up a [Satis][1]
instance, with all of your internal modules.

But beware, Satis will cache your versions, including branches. So when you
run `composer install`, you'll end up with a stale version of the master
branch.

This extension fixes that problem, while still allowing the performance
benefits of Satis.


## Warning

Remember, this is not default behaviour for a pretty good reason. If
the commit is not cached rather than a branch pointer, then dependencies
are not locked, and that's important, even if you're referencing a branch.

For our use case, we can ignore this risk. We know about all of our
dependencies, and we can make certain assumptions. If you want to use
this extension, make sure you can make those assumptions too.

[1]: https://github.com/composer/satis
