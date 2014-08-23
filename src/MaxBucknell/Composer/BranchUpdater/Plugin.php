<?php

namespace MaxBucknell\Composer\BranchUpdater;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\PackageEvent;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        var_dump('awesome');
        $this->io = $io;
        $io->write('hello, world');
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_PACKAGE_INSTALL => array(
                array('possiblyUpdateBranch', 0)
            ),
            ScriptEvents::POST_PACKAGE_UPDATE => array(
                array('possiblyUpdateBranch', 0)
            )
        );
    }

    public function possiblyUpdateBranch($event) {
        $operation = $event->getOperation();
        $package = $operation->getPackage();

        $prettyVersion = $package->getPrettyVersion();

        $this->io->write($prettyVersion);
    }
}
