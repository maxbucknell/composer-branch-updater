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

    /**
     * Activate the plugin.
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->processExecutor = new ProcessExecutor($this->io);
    }

    /**
     * Return an array of events the plugin subscribes to.
     *
     * @return array
     */
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

    /**
     * Find the package for an install event.
     *
     * @param PackageEvent $event
     */
    public function onPostPackageInstall(PackageEvent $event)
    {
        $package = $event->getOperation()->getPackage();

        $this->possiblyUpdateBranch($package);
    }

    /**
     * Find the package for an update event.
     *
     * @param PackageEvent $event
     */
    public function onPostPackageUpdate(PackageEvent $event)
    {
        $package = $event->getOperation()->getTargetPackage();

        $this->possiblyUpdateBranch($package);
    }

    /**
     * Check if a branch was requested.
     *
     * @param Package $package
     */
    private function possiblyUpdateBranch($package)
    {
        $prettyVersion = $package->getPrettyVersion();

        if (strpos($prettyVersion, 'dev-') === 0) {
            $this->updateBranch($package, substr($prettyVersion, 4));
        } else {
            return;
        }
    }

    /**
     * Update a package to a given branch.
     *
     * @param Package $package
     * @param string $branch
     */
    private function updateBranch($package, $branch)
    {
        $this->io->write('Updating to ' . $branch);

        $command = 'git reset --hard HEAD && git checkout ' . $branch;
        $cwd = realpath(
            $this->composer->getConfig()->get('vendor-dir') .
            '/' .
            $package->getName()
        );
        $output = '';
        $this->processExecutor->execute($command, $output, $cwd);
    }
}

