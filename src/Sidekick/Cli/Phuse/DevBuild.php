<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Phuse;

use Cubex\Cli\CliCommand;
use Sidekick\Components\Phuse\Mappers\Package;
use Sidekick\Components\Phuse\Mappers\Release;
use Sidekick\Components\Phuse\PhuseHelper;

class DevBuild extends CliCommand
{
  /**
   * @required
   * @valuerequired
   */
  public $path;

  /**
   * @valuerequired
   */
  public $branch = 'master';

  public function execute()
  {
    $composerJson = $this->path . '/composer.json';
    if(!file_exists($composerJson))
    {
      throw new \Exception("The path specified is not a valid composer project");
    }

    $composer   = json_decode(file_get_contents($composerJson));
    $compileDir = PhuseHelper::getArchiveDir($this->config("phuse"));
    $version    = 'dev-' . $this->branch;

    if(!file_exists($compileDir))
    {
      mkdir($compileDir, 0777, true);
    }

    $zipName = PhuseHelper::safePackageName($composer->name);
    $zipName .= "-" . $version;
    $zipName .= ".zip";
    $zipLoc = $compileDir . $zipName;

    $zip = new \ZipArchiveEx();
    $zip->open($zipLoc, \ZipArchive::OVERWRITE);
    $zip->addDirContents($this->path);
    $zip->close();

    /**
     * @var $package Package
     */
    $package       = Package::loadWhereOrNew(['name' => $composer->name]);
    $package->name = $composer->name;
    $package->saveChanges();

    $release              = new Release([$package->id(), $version]);
    $release->packageId   = $package->id();
    $release->version     = $version;
    $release->zipLocation = $zipLoc;
    $release->zipHash     = md5_file($zipLoc);
    $release->saveChanges();
  }
}
