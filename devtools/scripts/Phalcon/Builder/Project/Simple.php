<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Developer Tools                                                |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2016 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  |          Serghei Iakovlev <serghei@phalconphp.com>                     |
  +------------------------------------------------------------------------+
*/

namespace Phalcon\Builder\Project;

use Phalcon\Builder\Controller as ControllerBuilder;
use Phalcon\Web\Tools;
use Phalcon\Builder\Component;
use Phalcon\Builder\Options;

/**
 * Simple
 *
 * Builder to create Simple application skeletons
 *
 * @package Phalcon\Builder\Project
 */
class Simple extends ProjectBuilder
{
    /**
     * Project directories
     * @var array
     */
    protected $projectDirectories = [
        'app',
        'app/views',
        'app/config',
        'app/models',
        'app/controllers',
        'app/library',
        'app/migrations',
        'app/views/index',
        'app/views/layouts',
        'cache',
        'public',
        'public/img',
        'public/css',
        'public/temp',
        'public/files',
        'public/js',
        '.phalcon'
    ];

    /**
     * Create indexController file
     *
     * @return $this
     */
    private function createControllerFile()
    {
        $builder = new ControllerBuilder([
            'name'           => 'index',
            'directory'      => $this->options->get('projectPath') ,
            'controllersDir' => $this->options->get('projectPath')  . 'app/controllers',
            'baseClass'      => 'ControllerBase'
        ]);

        $builder->build();

        return $this;
    }

    /**
     * Create .htaccess files by default of application
     *
     * @return $this
     */
    private function createHtaccessFiles()
    {
        if (file_exists($this->options->get('projectPath') . '.htaccess') == false) {
            $code = '<IfModule mod_rewrite.c>'.PHP_EOL.
                "\t".'RewriteEngine on'.PHP_EOL.
                "\t".'RewriteRule  ^$ public/    [L]'.PHP_EOL.
                "\t".'RewriteRule  (.*) public/$1 [L]'.PHP_EOL.
                '</IfModule>';
            file_put_contents($this->options->get('projectPath').'.htaccess', $code);
        }

        if (file_exists($this->options->get('projectPath') . 'public/.htaccess') == false) {
            file_put_contents(
                $this->options->get('projectPath').'public/.htaccess',
                file_get_contents($this->options->get('templatePath') . '/project/simple/htaccess')
            );
        }

        if (file_exists($this->options->get('projectPath').'index.html') == false) {
            $code = '<html><body><h1>Mod-Rewrite is not enabled</h1><p>Please enable rewrite module on your web server to continue</body></html>';
            file_put_contents($this->options->get('projectPath').'index.html', $code);
        }

        return $this;
    }

    /**
     * Create view files by default
     *
     * @return $this
     */
    private function createIndexViewFiles()
    {
		$ext = 'phtml';
		if ($this->options->get('templateEngine') == 'volt') {
			$ext = 'volt';
		}

		$getFile = $this->options->get('templatePath') . '/project/simple/views/index.'.$ext;
		$putFile = $this->options->get('projectPath').'app/views/index.'.$ext;
		$this->generateFile($getFile, $putFile);

		if ($this->isConsole()) {
            $this->_notifySuccess(
                sprintf('Main view "%s" was successfully created.', 'index.'.$ext)
            );
            echo $putFile, PHP_EOL;
		}

		$getFile = $this->options->get('templatePath') . '/project/simple/views/index/index.'.$ext;
		$putFile = $this->options->get('projectPath').'app/views/index/index.'.$ext;
		$this->generateFile($getFile, $putFile);

		if ($this->isConsole()) {
            $this->_notifySuccess(
                sprintf('Controler "%s" view was successfully created.', 'index')
            );
            echo $putFile, PHP_EOL;
		}

        return $this;
    }

    /**
     * Creates the configuration
     *
     * @return $this
     */
    private function createConfig()
    {
        $type = $this->options->contains('useConfigIni') ? 'ini' : 'php';

        $getFile = $this->options->get('templatePath') . '/project/simple/config.' . $type;
        $putFile = $this->options->get('projectPath') . 'app/config/config.' . $type;
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('adapter', 'Mysql'), $this->options->get('username', 'root'), $this->options->get('password'), $this->options->get('dbname'));

		if ($this->isConsole()) {
            $this->_notifySuccess(
                'Config was successfully created.'
            );
            echo $putFile, PHP_EOL;
		}

        $getFile = $this->options->get('templatePath') . '/project/simple/loader.php';
        $putFile = $this->options->get('projectPath') . 'app/config/loader.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'));

		if ($this->isConsole()) {
            $this->_notifySuccess(
                'Loader was successfully created.'
            );
            echo $putFile, PHP_EOL;
		}

        $getFile = $this->options->get('templatePath') . '/project/simple/services.php';
        $putFile = $this->options->get('projectPath') . 'app/config/services.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'));

		if ($this->isConsole()) {
            $this->_notifySuccess(
                'Services was successfully created.'
            );
            echo $putFile, PHP_EOL;
		}

        return $this;
    }

    /**
     * Create ControllerBase
     *
     * @return $this
     */
    private function createControllerBase()
    {
        $getFile = $this->options->get('templatePath') . '/project/simple/ControllerBase.php';
        $putFile = $this->options->get('projectPath') . 'app/controllers/ControllerBase.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'));

		if ($this->isConsole()) {
            $this->_notifySuccess(
                'ControllerBase was successfully created.'
            );
            echo $putFile, PHP_EOL;
		}

        return $this;
    }

    /**
     * Create Bootstrap file by default of application
     *
     * @return $this
     */
    private function createBootstrapFiles()
    {
        $getFile = $this->options->get('templatePath') . '/project/simple/index.php';
        $putFile = $this->options->get('projectPath') . 'public/index.php';
        $this->generateFile($getFile, $putFile);

		if ($this->isConsole()) {
            $this->_notifySuccess(
                'Bootstrap was successfully created.'
            );
            echo $putFile, PHP_EOL;
		}

        return $this;
    }

    /**
     * Create .htrouter.php file
     *
     * @return $this
     */
    private function createHtrouterFile()
    {
        $getFile = $this->options->get('templatePath') . '/project/simple/.htrouter.php';
        $putFile = $this->options->get('projectPath') . '.htrouter.php';
        $this->generateFile($getFile, $putFile);

		if ($this->isConsole()) {
            $this->_notifySuccess(
                'Htrouter was successfully created.'
            );
            echo $putFile, PHP_EOL;
		}

        return $this;
    }

    /**
     * Build project
     *
     * @return bool
     */
    public function build()
    {
        $this
            ->buildDirectories()
            ->getVariableValues()
            ->createConfig()
            ->createBootstrapFiles()
            ->createHtaccessFiles()
            ->createControllerBase()
            ->createIndexViewFiles()
            ->createControllerFile()
            ->createHtrouterFile();

        return true;
    }
}
