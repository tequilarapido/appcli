<?php namespace Tequilarapido\Cli;

use Symfony\Component\Process\Process;

class FileSystem
{

    public function checkDirectory($dir, $checkWritable = true)
    {
        // Check if it's not empty
        if (empty($dir)) {
            throw new \LogicException("Empty given directory.");
        }

        // Check if it exists and it is writable
        $directory = $this->getDirFullPath($dir);
        if (!$directory) {
            throw new \LogicException("Directory not found (Given:" . $dir . ").");
        }

        // Check if it's writable
        echo "\n writable ? $directory\n";
        if ($checkWritable && !is_writable($directory)) {
            //throw new \LogicException("Sorry but [$directory] is not writable!");
        }

        return $directory;
    }


    protected function getDirFullPath($dir)
    {
        // Let's try with nothing : full path ?
        if (is_dir($dir)) {
            return $dir;
        }

        // Let's try current directory
        $cd = getcwd();
        $fullpath = $cd . '/' . $dir;
        if ($cd && is_dir($fullpath)) {
            return $fullpath;
        }

        // If we are on windows using Cygwin, tryout Windows path
        $cmd = "cygpath -w $dir";
        $process = new Process($cmd);
        $result = $process->run();
        if ($process->isSuccessful()) {
            return trim($process->getOutput());
        }

        return false;
    }

}