<?php
namespace Designnbuy\Base\Service;
class FTPUploader {

    public function make_directory($ftp_stream, $dir)
    {
        // if directory already exists or can be immediately created return true
        if (FTPUploader::ftp_is_dir($ftp_stream, $dir) || @ftp_mkdir($ftp_stream, $dir)) return true;
        // otherwise recursively try to make the directory
        if (!$this->make_directory($ftp_stream, dirname($dir))) return false;
        // final step to create the directory
        return ftp_mkdir($ftp_stream, $dir);
    }

    public function ftp_is_dir($ftp_stream, $dir)
    {
        // get current directory
        $original_directory = ftp_pwd($ftp_stream);
        // test if you can change directory to $dir
        // suppress errors in case $dir is not a file or not a directory
        if ( @ftp_chdir( $ftp_stream, $dir ) ) {
            // If it is a directory, then change the directory back to the original directory
            ftp_chdir( $ftp_stream, $original_directory );
            return true;
        } else {
            return false;
        }
    }
    public function connect($server_address, $username, $password)
    {
        try {
            $ftp_conn = ftp_connect($server_address);
            ftp_login($ftp_conn, $username, $password);
        } catch (Exception $e) {
            //echo $e->getMassage(); exit;
            return FALSE;
        }
        return $ftp_conn;
    }

    public function write_remote_file(
        $file_content, $remote_path, $remote_url, $server_address, $username, $password) {


        // writing content to a temporary file
        $tmp_file = 'tmp.'.date('YmdHis'); /* timestamp to prevent overwriting existing file */
        $tmp = fopen($tmp_file, 'w+');
        fwrite($tmp, $file_content);
        fclose($tmp);

        // upload the temporary file
        $upload = FTPUploader::upload($tmp_file, $remote_path, $remote_url, $server_address, $username, $password, FTP_ASCII);

        // delete temporary file
        unlink($tmp_file);


        $result = $upload;
        return $result;
    }

    public function upload(
        $local_file_location, $remote_path, $remote_url, $server_address, $username, $password, $mode = FTP_BINARY) {

        $error_msg = '';


        // try to connect
        $ftp_conn = FTPUploader::connect($server_address, $username, $password);
        if (! $ftp_conn)
        {
            $error_msg = 'Could not connect to server';
        }
       /* $contents = ftp_nlist($ftp_conn, ".");
        echo "<pre>";
        print_r($contents);
        die;*/
        // create directories if don't exist
        $path = explode('/', $remote_path);
        unset($path[count($path) - 1]);
        $path = implode('/', $path);
        $mkdir = FTPUploader::make_directory($ftp_conn, $path);
        if (! $mkdir)
        {
            $error_msg = 'Could not create folder';
        }

        // create backup file
        /*if(! is_dir('.ftpbackup')) mkdir('.ftpbackup', 0777);
        $segments = explode('/', $local_file_location);
        $filename = $segments[count($segments) - 1];
        copy($local_file_location, '.ftpbackup/'.$filename.'.'.date('YmdHis'));*/

        // upload the file
        try{
            $chk_uploaded = ftp_put($ftp_conn, $remote_path, $local_file_location, $mode);
        }
        catch ( \Exception $exc )
        {
            $error_msg = 'Could not upload file';
        }

        /*if (! $chk_uploaded)
        {
            $error_msg = 'Could not upload file';
        }*/
        ftp_close($ftp_conn);

        // checking remote_url if file exist
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $chk_verify = ($http_code < 400);
        if (! $chk_verify)
        {
            $error_msg = 'Could not verify file was uploaded via URL';
        }*/

        $result = array(
            'error' => (int)($error_msg !== ''),
            'message' => $error_msg);
        return $result;
    }

}
