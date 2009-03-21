<?php

spl_autoload_register("AutoLoader::autoLoad");

class AutoLoader {

    static function autoLoad($class) {
        if(self::zendStyle($class)) return true ;
        else if(self::sufix($class)) return true ;
        else if(self::allDir($class)) return true ;
        else if(self::allDirExt($class)) return true ;
        else if(self::nonSense($class)) return true ;
        else throw new Exception("Class ".$class." not found!");
        return false;
    }

    static function zendStyle($class) {
        $p = explode("_",$class);
        $file = implode("/",$p).".php";
        if(!file_exists($file)) return false;
        include $file;
        return true ;
    }

    static function sufix($class) {
        if (!preg_match("#(Model|View|Controller)#i", $class, $fragment)) {
            return false ;
        }
        $class = substr($class, 0, strlen($class)-strlen($fragment[1]));
        $sufix = $fragment[1];
        $file = $sufix."/".$class.".php";
        if(file_exists($file)) {
            include $file ;
            return true ;
        }
        return false ;
    }

    static function allDir($class){
        if(self::findClass($class,".")) return true ;
        else return false;
    }

    static function findClass($class,$dir) {
        $ponteiro = opendir($dir);
        while($file = readdir($ponteiro)) {
            $file_path = $dir."/".$file ;
            if($file == "." or $file == "..") {
                    continue ;
            } else if(is_dir($file_path)) {
                    if(self::findClass($class,$file_path)) {
                            return true ;
                    }
            } else if($file == $class.".php") {
                    include $file_path ;
                    return true ;
            } 
        }
        return false ;
    }

    static function allDirExt($class) {
        if(self::findClassExt($class,".")) return true ;
        else return false;
    }

    static function findClassExt($class,$dir) {
        $ponteiro = opendir($dir);
        while($file = readdir($ponteiro)) {
            $file_path = $dir."/".$file ;
            if($file == "." or $file == "..") {
                    continue ;
            } else if(is_dir($file_path)) {
                    if(self::findClassExt($class,$file_path)) {
                            return true ;
                    }
            } else if($file == $class.".class.php") {
                    include $file_path ;
                    return true ;
            } 
        }
        return false ;
    }

    static function nonSense($class) {
        if(self::findClassNonSense($class,".")) return true ;
        else return false;
    }

    static function findClassNonSense($class,$dir) {
        $ponteiro = opendir($dir);
        while($file = readdir($ponteiro)) {
            $file_path = $dir."/".$file ;
            if($file[0] == "." or $file == "..") {
                continue ;
            } else if(is_dir($file_path)) {
                if(self::findClassNonSense($class,$file_path)) {
                    return true ;
                }
            } else {
                //$content = file_get_contents( $file_path );
                //$pattern = "/class[\s]+".$class."[\s|{]/i";
                $file = file($file_path);
                $pattern = "/^[\s]*class[\s]+".$class."[\s|{]/i";
                if(count($file) >= 1){
                    foreach($file as $content){
                        if(preg_match($pattern,$content)) {
                            include $file_path;
                            return true;
                        }
                    }
               }
            } 
        }
        return false ;
    }

}
?>
