<?php  

namespace mf\utils;
class ClassLoader extends AbstractClassLoader{

    function __construct($fileroot)
    {
        parent::__construct($fileroot);     //intiliaser le repertoire src
    }


   function getFilename(string $classname):string{
          /*reçoit le nom de la classe à require */
     $path =  str_replace('\\',DIRECTORY_SEPARATOR,$classname);
     $path = $path.'.php';
     return $path;
    } 

    function makePath(string $filename):string{
        /*construire tout le chemin jusqu'au fichier de la class */
        return $this->prefix.DIRECTORY_SEPARATOR.$filename; /*filename est le fichier de la class*/
    }

    function loadClass(string $classname){              /*load la class avec un require_once*/
        $filename = $this->getFilename($classname);
        $path = $this->makePath($filename);

        if(file_exists($path)){
            require_once($path);
        }
    }


}