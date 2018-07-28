<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Presenter\Message\AbstractMessagePresenter;
use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Presenter\Message\WarningMessage;
use PharData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReleasesAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        string $appConfigCacheDir,
        string $appConfigReleasesDir,
        string $appConfigSymlink,
        string $appConfigSuperAdminPassword
    ): Response {

        $message = null;

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            if ($request->get('new-url')) {
                $name = $request->get('name');
                $url = $request->get('new-url');
                $password = $request->get('password');
                if ($password === $appConfigSuperAdminPassword) {
                    $message = $this->downloadNewRelease(
                        $name,
                        $url,
                        $appConfigCacheDir,
                        $appConfigReleasesDir
                    );
                } else {
                    $message = new ErrorMessage('Password was incorrect');
                }
            }

            if ($request->get('delete-release')) {
                $release = $request->get('delete-release');

                $directory = $appConfigReleasesDir . '/' . $release;
                $this->recursiveDelete($directory);
                $message = new OkMessage($release . ' was deleted');
            }
            if ($request->get('do-release')) {
                $release = $request->get('do-release');

                //appConfigSymlink, appConfigReleasesDir
                // todo
                $message = new WarningMessage('FAKE do release' . $release);
            }
        }

        // get all releases (order desc)
        $dirs = array_filter(glob($appConfigReleasesDir . '/*'), 'is_dir');
        $releases = array_map('basename', $dirs);

        // get current release

        return $this->renderAdminSite(
            'releases.html.twig',
            [
                'message' => $message,
                'releases' => $releases,
                'currentRelease' => null, // todo - set this
            ],
            $request
        );
    }

    private function downloadNewRelease(
        string $name,
        string $url,
        string $appConfigCacheDir,
        string $appConfigReleasesDir
    ): AbstractMessagePresenter {
        $archiveFilePath = $appConfigCacheDir . '/releases';
        if (!file_exists($archiveFilePath) && !mkdir($archiveFilePath, 0755, true) && !is_dir($archiveFilePath)) {
            return new ErrorMessage(sprintf('Directory "%s" was not created', $archiveFilePath));
        }
        $archiveFileName = $archiveFilePath . '/' . $name . '.tar';

        file_put_contents($archiveFileName, fopen($url, 'rb'));

        // extract to the releases folder
        $releaseDir = $appConfigReleasesDir . '/' . $name;
        if (!file_exists($releaseDir) && !mkdir($releaseDir, 0755, true) && !is_dir($releaseDir)) {
            return new ErrorMessage(sprintf('Directory "%s" was not created', $releaseDir));
        }

        try {
            $phar = new PharData($archiveFileName);
            $phar->extractTo($releaseDir); // extract all files
        } catch (\Exception $e) {
            // handle errors
            return new ErrorMessage($e->getMessage());
        }

        return new OkMessage('Release successfully downloaded');
    }

    private function recursiveDelete($dirPath): void
    {
        if (!is_dir($dirPath)) {
            return;
        }
        $objects = scandir($dirPath, SCANDIR_SORT_ASCENDING);
        foreach ($objects as $object) {
            if ($object !== '.' && $object !== '..') {
                if (filetype($dirPath . '/' . $object) === 'dir') {
                    $this->recursiveDelete($dirPath . '/' . $object);
                } else {
                    unlink($dirPath . '/' . $object);
                }
            }
        }
        reset($objects);
        rmdir($dirPath);
    }
}
