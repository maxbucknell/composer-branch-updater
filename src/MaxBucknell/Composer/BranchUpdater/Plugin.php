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
            PluginEvents::PRE_FILE_DOWNLOAD => array(
                array('onPreFileDownload', 0)
            ),
        );
        // return array(
        //    ScriptEvents::POST_PACKAGE_INSTALL => array(
        //        array('possiblyUpdateBranch', 0)
        //    ),
        //    ScriptEvents::POST_PACKAGE_UPDATE => array(
        //        array('possiblyUpdateBranch', 0)
        //    )
        //);
    }

    public function onPreFileDownload($event) {
        $this->io->write('hello again');
        var_dump($event);
    }
}
