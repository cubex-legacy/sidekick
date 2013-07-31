<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Phuse;

use Cubex\Cli\CliCommand;
use Cubex\Data\ZipArchive\RecursiveZipArchive;
use Sidekick\Components\Phuse\Mappers\Package;
use Sidekick\Components\Phuse\Mappers\Release;
use Sidekick\Components\Phuse\PhuseHelper;

class CreateBuild extends CliCommand
{
  /**
   * @required
   * @valuerequired
   */
  public $path;

  /**
   * @required
   * @valuerequired
   */
  public $projectId;

  /**
   * @valuerequired
   * @required
   */
  public $version = 'dev-master';

  public function execute()
  {
    $composerJson = $this->path . '/composer.json';
    if(!file_exists($composerJson))
    {
      throw new \Exception("The path specified is not a valid composer project");
    }

    $composer = json_decode(file_get_contents($composerJson));
    if(!isset($composer->name))
    {
      throw new \Exception("Invalid project. Composer project doesn't have a name.");
    }

    $compileDir = PhuseHelper::getArchiveDir($this->config("phuse"));

    if(!file_exists($compileDir))
    {
      mkdir($compileDir, 0777, true);
    }

    $zipName = PhuseHelper::safePackageName($composer->name);
    $zipName .= "-" . $this->version;
    $zipName .= ".zip";
    $zipLoc = $compileDir . $zipName;

    $zip = new RecursiveZipArchive();
    $zip->open($zipLoc, \ZipArchive::OVERWRITE);
    $zip->addDir('', $this->path);
    $zip->close();

    /**
     * @var $package Package
     */
    $package              = Package::loadWhereOrNew(
      ['name' => $composer->name]
    );
    $package->projectId   = $this->projectId;
    $package->name        = $composer->name;
    $package->description = $composer->description;
    $package->vendor      = explode('/', $composer->name)[0];
    $package->library     = explode('/', $composer->name)[1];
    $package->version     = $composer->version;
    $package->license     = $composer->license;
    $package->authors     = $composer->authors;
    $package->require     = $composer->require;
    $package->rawComposer = $composer;
    $package->saveChanges();

    $release              = new Release([$package->id(), $this->version]);
    $release->zipLocation = $zipLoc;
    $release->name        = $composer->name;
    $release->description = $composer->description;
    $release->license     = $composer->license;
    $release->authors     = $composer->authors;
    $release->vendor      = $package->vendor;
    $release->library     = $package->library;
    $release->require     = $package->require;
    $release->zipHash     = md5_file($zipLoc);
    $release->saveChanges();
  }
}
