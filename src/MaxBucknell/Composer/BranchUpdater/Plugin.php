<?php

namespace MaxBucknell\Composer\BranchUpdater;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\PackageEvent;
use Composer\Util\ProcessExecutor;
use Composer\Util\FileSystem;
use Composer\Util\Git;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private $composer;
    private $io;
    private $processExecutor;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->processExecutor = new ProcessExecutor($this->io);
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

    public function onPostPackageInstall(PackageEvent $event)
    {
        $package = $event->getOperation()->getPackage();

        $this->possiblyUpdateBranch($package);
    }

    public function onPostPackageUpdate(PackageEvent $event)
    {
        $package = $event->getOperation()->getTargetPackage();

        $this->possiblyUpdateBranch($package);
    }

    private function possiblyUpdateBranch($package)
    {
        $prettyVersion = $package->getPrettyVersion();

        if (strpos($prettyVersion, 'dev-') === 0) {
            $this->updateBranch($package, substr($prettyVersion, 4));
        } else {
            return;
        }
    }

    private function updateBranch($package, $branch)
    {
        $this->io->write('Updating to ' . $branch);

        $command = 'git reset --hard ' . $branch;
        $cwd = realpath(
            $this->composer->getConfig()->get('vendor-dir') .
            '/' .
            $package->getName()
        );
        $this->processExecutor->execute($command, $this->io, $cwd);
    }
}

