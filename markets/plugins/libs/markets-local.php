<?php
if(!class_exists('Markets_Local')) {
    
    /**
     * Local File Backup
     *
     */
    class Markets_Local{

        /**
         * Write to local irectory
         *
         */
        static function write($plugin, $data){
            $data = json_encode($data);
            $local_file = fopen(MARKETS_CONTENT_DIR."/".$plugin.".markets", "w") or wp_die(__('Unable to write local file','markets'));
            fwrite($local_file, $data);
            fclose($local_file);
        }
        
        /**
         * Read a file
         */
        static function read($plugin){
            $local_file = fopen(MARKETS_CONTENT_DIR."/".$plugin.".markets", "r") or wp_die(__('Unable to open local file','markets'));
            $data = fread($local_file,filesize(MARKETS_CONTENT_DIR."/".$plugin.".markets"));
            fclose($local_file);
            return json_decode($data, true);
        }
    }
}
?>