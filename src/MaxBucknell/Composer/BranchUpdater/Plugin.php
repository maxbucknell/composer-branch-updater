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
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_PACKAGE_INSTALL => array(
                array('onPostPackageInstall', 1)
            ),
            ScriptEvents::POST_PACKAGE_UPDATE => array(
                array('onPostPackageUpdate', 1)
            )
        );
    }

    public function onPostPackageInstall(PackageEvent $event) {
        $package = $event->getOperation()->getPackage();

        $this->possiblyUpdateBranch($package);
    }

    public function onPostPackageUpdate(PackageEvent $event) {
        $package = $event->getOperation()->getTargetPackage();

        $this->possiblyUpdateBranch($package);
    }

    public function possiblyUpdateBranch($package) {
        $prettyVersion = $package->getPrettyVersion();

        $this->io->write($prettyVersion);
    }
}
