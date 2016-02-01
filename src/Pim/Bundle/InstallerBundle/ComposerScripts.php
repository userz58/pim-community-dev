<?php

namespace Pim\Bundle\InstallerBundle;

use Composer\Script\Event;

/**
 * Contains all PIM composer scripts we run.
 *
 * @author    Willy Mesnage <willy.mesnage@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ComposerScripts
{
    /**
     * @param Event $event
     */
    public static function copyMigrationFolderFromDev(Event $event)
    {
        $event->getIO()->write('Copying migration folder from Akeneo PIM dependency to standard version.');
        $ds = DIRECTORY_SEPARATOR;

        $vendorPath    = $event->getComposer()->getConfig()->get('vendor-dir');
        $migrationPath = $ds . 'upgrades';
        $copyFromPath  = $vendorPath . $ds . 'akeneo' . $ds . 'pim-community-dev' . $migrationPath . $ds;
        $copyToPath    = $vendorPath . $ds . '..' . $migrationPath . $ds;

        self::copyFiles($event, $copyFromPath, $copyToPath);

        $event->getIO()->write('Done.');
    }

    /**
     * @param Event  $event        Composer script event
     * @param string $copyFromPath Copy files from this folder path
     * @param string $copyToPath   Copy files to this folder path
     * @param bool   $rm           Remove the destination folder if it already exists
     */
    protected static function copyFiles(Event $event, $copyFromPath, $copyToPath, $rm = true)
    {
        if (!file_exists($copyFromPath)) {
            $event->getIO()->writeError(sprintf(
                'Directory "%s" not found. Update will continue but no Akeneo PIM migration can be done.',
                $copyFromPath
            ));

            return;
        }

        if (file_exists($copyToPath) && $rm) {
            exec(sprintf('rm %s -rf', $copyToPath));
            mkdir($copyToPath);
        } elseif (!file_exists($copyToPath)) {
            mkdir($copyToPath);
        }

        $cmd = sprintf('cp -Rf %s. %s', $copyFromPath, $copyToPath);
        exec($cmd, $output, $cpHasError);

        if ($cpHasError) {
            $event->getIO()->writeError('Copy failed. Update will continue but no Akeneo PIM migration can be done.');
        }
    }
}
