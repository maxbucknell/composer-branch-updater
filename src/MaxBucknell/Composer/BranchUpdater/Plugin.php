<?php

namespace MaxBucknell\Composer\BranchUpdater;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\PackageEvent;

class Plugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {

    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_PACKAGE_INSTALL => array(
                array('possiblyUpdateBranch', 1)
            ),
            ScriptEvents::POST_PACKAGE_UPDATE => array(
                array('possiblyUpdateBranch', 1)
            )
        );
    }

    public function possiblyUpdateBranch(PackageEvent $event) {
        var_dump($event);
    }
}
